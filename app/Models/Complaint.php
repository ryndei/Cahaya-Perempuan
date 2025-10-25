<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Complaint extends Model
{
    use HasFactory;

    /** Status constants */
    public const STATUS_SUBMITTED         = 'submitted';
    public const STATUS_IN_REVIEW         = 'in_review';
    public const STATUS_FOLLOW_UP         = 'follow_up';
    public const STATUS_CLOSED            = 'closed';             // Selesai (umum)
    public const STATUS_CLOSED_PA         = 'closed_pa';          // Selesai – Putusan Pengadilan Agama
    public const STATUS_CLOSED_PN         = 'closed_pn';          // Selesai – Putusan Pengadilan Negeri
    public const STATUS_CLOSED_MEDIATION  = 'closed_mediation';   // Selesai – Mediasi/Damai

    protected $fillable = [
        'user_id','code','category','description','attachment_path','status','admin_note',
        'reporter_name','reporter_phone','reporter_is_disability','reporter_age','reporter_job',
        'province_code','province_name','regency_code','regency_name','district_code','district_name',
        'reporter_address',
        'perpetrator_name','perpetrator_job','perpetrator_age',
    ];

    /** Casts & default attributes (opsional, tapi bagus) */
    protected $casts = [
        'reporter_is_disability' => 'boolean',
        'reporter_age'           => 'integer',
        'perpetrator_age'        => 'integer',
    ];

    protected $attributes = [
        'status' => self::STATUS_SUBMITTED,
    ];

    protected static function booted(): void
    {
        static::creating(function (Complaint $c) {
            $c->code    ??= 'CP-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
            $c->user_id ??= Auth::id(); // pastikan user login saat submit
        });
    }

    /** Relasi */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Map value -> label untuk dropdown & badge */
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

    /** Accessor: $complaint->status_label */
    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status]
            ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /** Scopes bantu */
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
}
