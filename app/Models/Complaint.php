<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Complaint extends Model
{
    use HasFactory, LogsActivity;

    /* =========================
     |  Konstanta Status
     |=========================*/
    public const STATUS_SUBMITTED        = 'submitted';
    public const STATUS_IN_REVIEW        = 'in_review';
    public const STATUS_FOLLOW_UP        = 'follow_up';
    public const STATUS_CLOSED           = 'closed';
    public const STATUS_CLOSED_PA        = 'closed_pa';
    public const STATUS_CLOSED_PN        = 'closed_pn';
    public const STATUS_CLOSED_MEDIATION = 'closed_mediation';

    /* =========================
     |  Mass Assignment
     |  (user_id & code di-set otomatis)
     |=========================*/
    protected $fillable = [
        // 'user_id', 'code',
        'category',
        'description',
        'status',
        'admin_note',

        'reporter_name',
        'reporter_phone',
        'reporter_is_disability',
        'reporter_age',
        'reporter_job',
        'reporter_age_bucket',
        'reporter_phone_hash',

        'province_code',
        'province_name',
        'regency_code',
        'regency_name',
        'district_code',
        'district_name',
        'reporter_address',

        'perpetrator_name',
        'perpetrator_job',
        'perpetrator_age',
        'perpetrator_age_bucket',

        // kolom operasional (opsional, jika ada di migration)
        'assigned_admin_id',
        'priority',
        'due_at',
        'first_response_at',
        'pinned_until',
    ];

    /* =========================
     |  Casts
     |=========================*/
    protected $casts = [
        // terenkripsi (kolom TEXT)
        'reporter_name'    => 'encrypted',
        'reporter_phone'   => 'encrypted',
        'reporter_address' => 'encrypted',
        'reporter_job'     => 'encrypted',
        'reporter_age'     => 'encrypted',
        'perpetrator_name' => 'encrypted',
        'perpetrator_job'  => 'encrypted',
        'perpetrator_age'  => 'encrypted',
        'description'      => 'encrypted',
        'admin_note'       => 'encrypted',

        // non-encrypted
        'reporter_is_disability' => 'boolean',
        'reporter_age_bucket'    => 'integer',
        'perpetrator_age_bucket' => 'integer',

        // tanggal
        'closed_at'         => 'datetime',
        'due_at'            => 'datetime',
        'first_response_at' => 'datetime',
        'pinned_until'      => 'datetime',
    ];

    /* =========================
     |  Default Attributes
     |=========================*/
    protected $attributes = [
        'status' => self::STATUS_SUBMITTED,
    ];

    /* =========================
     |  Events
     |=========================*/
    protected static function booted(): void
    {
        // Set otomatis user_id & code unik saat creating
        static::creating(function (Complaint $c) {
            // Kalau dari job/seed dan tidak ada session, biarkan null atau hormati nilai yang sudah diisi
            $c->user_id = $c->user_id ?? Auth::id();

            // Generate code unik: CP-YYMMDD-ABCDE (retry 5x)
            $date = now()->format('ymd');
            for ($i = 0; $i < 5; $i++) {
                $candidate = 'CP-'.$date.'-'.strtoupper(Str::random(5));
                if (! static::where('code', $candidate)->exists()) {
                    $c->code = $candidate;
                    break;
                }
            }
            // Fallback: random 9 char (AMAN terhadap limit varchar(40); hindari UUID yang bisa > 40)
            $c->code ??= 'CP-'.$date.'-'.strtoupper(Str::random(9));
        });

        // Normalisasi dan turunan sebelum simpan
        static::saving(function (Complaint $m) {
            // Blind index nomor telepon (pakai digit-only)
            if ($m->reporter_phone) {
                $digits = preg_replace('/\D+/', '', (string) $m->reporter_phone);
                $m->reporter_phone_hash = $digits ? hash('sha256', $digits) : null;
            } else {
                $m->reporter_phone_hash = null;
            }

            // Bucket umur (tanpa membuka nilai terenkripsi ke laporan)
            $m->reporter_age_bucket    = self::makeAgeBucket($m->reporter_age);
            $m->perpetrator_age_bucket = self::makeAgeBucket($m->perpetrator_age);
        });

        // Auto set/unset closed_at ketika status berubah
        static::updating(function (Complaint $m) {
            if ($m->isDirty('status')) {
                $closed = [
                    self::STATUS_CLOSED,
                    self::STATUS_CLOSED_PA,
                    self::STATUS_CLOSED_PN,
                    self::STATUS_CLOSED_MEDIATION,
                ];

                if (in_array($m->status, $closed, true)) {
                    $m->closed_at ??= now();
                } else {
                    if (in_array($m->getOriginal('status'), $closed, true)) {
                        $m->closed_at = null; // opsional
                    }
                }
            }
        });
    }

    /* =========================
     |  Util: Bucket Umur (1..7)
     |  1: <=17, 2: 18-24, 3: 25-34, 4: 35-44,
     |  5: 45-54, 6: 55-64, 7: >=65
     |=========================*/
    public static function makeAgeBucket($agePlain): ?int
    {
        if ($agePlain === null || $agePlain === '') return null;
        $n = (int) preg_replace('/\D+/', '', (string) $agePlain);
        if ($n <= 0) return null;

        return match (true) {
            $n <= 17 => 1,
            $n <= 24 => 2,
            $n <= 34 => 3,
            $n <= 44 => 4,
            $n <= 54 => 5,
            $n <= 64 => 6,
            default  => 7,
        };
    }

    /* =========================
     |  Relasi
     |=========================*/
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

    /* =========================
     |  Label & Accessor
     |=========================*/
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

    /* =========================
     |  Scopes
     |=========================*/
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

    /* =========================
     |  Activity Log
     |=========================*/
    public function getActivitylogOptions(): LogOptions
    {
        // Log hanya kolom 'status' agar PII aman
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

    /* =========================
     |  Router Binding
     |=========================*/
    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
