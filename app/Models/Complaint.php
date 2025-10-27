<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Complaint extends Model
{
    use HasFactory, LogsActivity;

    /** Status constants */
    public const STATUS_SUBMITTED         = 'submitted';
    public const STATUS_IN_REVIEW         = 'in_review';
    public const STATUS_FOLLOW_UP         = 'follow_up';
    public const STATUS_CLOSED            = 'closed';
    public const STATUS_CLOSED_PA         = 'closed_pa';
    public const STATUS_CLOSED_PN         = 'closed_pn';
    public const STATUS_CLOSED_MEDIATION  = 'closed_mediation';

    /**
     * KEAMANAN:
     * - user_id & code DIHAPUS dari fillable agar tidak bisa di-mass assign.
     * - Nilai tsb. dipaksa di event creating (lihat booted()).
     */
    protected $fillable = [
        // 'user_id','code',
        'category','description','attachment_path','status','admin_note',
        'reporter_name','reporter_phone','reporter_is_disability','reporter_age','reporter_job',
        'reporter_age_bucket','reporter_phone_hash',
        'province_code','province_name','regency_code','regency_name','district_code','district_name',
        'reporter_address',
        'perpetrator_name','perpetrator_job','perpetrator_age','perpetrator_age_bucket',
    ];

    /** Casts (PII terenkripsi) */
    protected $casts = [
        // Encrypted (kolom harus TEXT)
        'reporter_name'        => 'encrypted',
        'reporter_phone'       => 'encrypted',
        'reporter_address'     => 'encrypted',
        'reporter_job'         => 'encrypted',
        'reporter_age'         => 'encrypted',
        'perpetrator_name'     => 'encrypted',
        'perpetrator_job'      => 'encrypted',
        'perpetrator_age'      => 'encrypted',
        'description'          => 'encrypted',
        'admin_note'           => 'encrypted',

        // Non-encrypted
        'reporter_is_disability'   => 'boolean',
        'reporter_age_bucket'      => 'integer',
        'perpetrator_age_bucket'   => 'integer',
    ];

    protected $attributes = [
        'status' => self::STATUS_SUBMITTED,
    ];

    protected static function booted(): void
    {
        // Paksa set user_id & code (abaikan input)
        static::creating(function (Complaint $c) {
            $c->user_id = Auth::id();

            // Retry generate code unik (hindari collision pada unique index)
            $date = now()->format('ymd');
            for ($i=0; $i<5; $i++) {
                $candidate = 'CP-'.$date.'-'.strtoupper(Str::random(5));
                if (!static::where('code', $candidate)->exists()) {
                    $c->code = $candidate;
                    break;
                }
            }
            $c->code ??= 'CP-'.$date.'-'.strtoupper(Str::uuid()->toString());
        });

        // Normalisasi sebelum terenkripsi ke DB
        static::saving(function (Complaint $m) {
            // Blind index nomor telepon pelapor
            if ($m->reporter_phone) {
                $digits = preg_replace('/\D+/', '', (string) $m->reporter_phone);
                $m->reporter_phone_hash = $digits ? hash('sha256', $digits) : null;
            } else {
                $m->reporter_phone_hash = null;
            }

            // Bucket umur (1..7) untuk analitik tanpa membuka nilai asli
            $m->reporter_age_bucket    = self::makeAgeBucket($m->reporter_age);
            $m->perpetrator_age_bucket = self::makeAgeBucket($m->perpetrator_age);
        });
    }

    /**
     * Konversi umur (string terenkripsi sebelum tulis) menjadi bucket 1..7:
     * 1: <=17, 2: 18-24, 3: 25-34, 4: 35-44, 5: 45-54, 6: 55-64, 7: >=65
     */
    public static function makeAgeBucket($agePlain): ?int
    {
        if ($agePlain === null || $agePlain === '') return null;

        // agePlain belum terenkripsi saat saving; user bisa isi "20", " 34 ", dsb.
        $n = (int) preg_replace('/\D+/', '', (string) $agePlain);
        if ($n <= 0) return null;

        return match (true) {
            $n <= 17         => 1,
            $n <= 24         => 2,
            $n <= 34         => 3,
            $n <= 44         => 4,
            $n <= 54         => 5,
            $n <= 64         => 6,
            default          => 7,
        };
    }

    /* ===================== Relasi ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject');
    }

    public function lastStatusActivity()
    {
        return $this->morphOne(\Spatie\Activitylog\Models\Activity::class, 'subject')
            ->where('event', 'updated')
            ->whereNotNull('properties->attributes->status')
            ->latestOfMany();
    }

    /* =============== Label & Accessor bantu =============== */

    public static function statusLabels(): array
    {
        return [
            self::STATUS_SUBMITTED        => 'Terkirim',
            self::STATUS_IN_REVIEW        => 'Ditinjau',
            self::STATUS_FOLLOW_UP        => 'Tindak Lanjut',
            self::STATUS_CLOSED           => 'Selesai (Umum)',
            self::STATUS_CLOSED_PA        => 'Selesai (Putusan Pengadilan Agama)',
            self::STATUS_CLOSED_PN        => 'Selesai (Putusan Pengadilan Negeri)',
            self::STATUS_CLOSED_MEDIATION => 'Selesai (Mediasi/Damai)',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status]
            ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getLastStatusUpdatedByAttribute(): ?string
    {
        return optional($this->lastStatusActivity?->causer)->name;
    }

    public function getLastStatusUpdatedAtAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->lastStatusActivity?->created_at;
    }

    /* ===================== Scopes ===================== */

    public function scopeClosed($q)
    {
        return $q->whereIn('status', [
            self::STATUS_CLOSED,
            self::STATUS_CLOSED_PA,
            self::STATUS_CLOSED_PN,
            self::STATUS_CLOSED_MEDIATION,
        ]);
    }

    public function scopeOpen($q)
    {
        return $q->whereNotIn('status', [
            self::STATUS_CLOSED,
            self::STATUS_CLOSED_PA,
            self::STATUS_CLOSED_PN,
            self::STATUS_CLOSED_MEDIATION,
        ]);
    }

    /* ===================== Activity Log ===================== */

    public function getActivitylogOptions(): LogOptions
    {
        // Hanya log "status" agar PII (admin_note, dsb.) TIDAK masuk log.
        return LogOptions::defaults()
            ->useLogName('complaint')
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Pengaduan dibuat',
            'updated' => 'Pengaduan diperbarui',
            'deleted' => 'Pengaduan dihapus',
            default   => $eventName,
        };
    }

    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName)
    {
        $labels    = static::statusLabels();
        $oldStatus = $this->getOriginal('status');
        $newStatus = $this->status;

        $activity->properties = $activity->properties->merge([
            'complaint_code' => $this->code ?? $this->id,
            'ip'             => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'from'           => $oldStatus,
            'to'             => $newStatus,
            'from_label'     => $oldStatus ? ($labels[$oldStatus] ?? $oldStatus) : null,
            'to_label'       => $newStatus ? ($labels[$newStatus] ?? $newStatus) : null,
        ]);
    }


        public function getRouteKeyName(): string
    {
    return 'code';
    }
}
