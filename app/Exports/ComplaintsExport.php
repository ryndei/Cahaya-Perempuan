<?php

namespace App\Exports;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    /** @var \Illuminate\Database\Eloquent\Builder */
    protected Builder $query;

    public function __construct(Builder $filteredQuery)
    {
        // terima query hasil filter dari controller biar konsisten dengan index()
        $this->query = $filteredQuery;
    }

    /** Gunakan query (lebih hemat memori dibanding FromCollection) */
    public function query()
    {
        return $this->query
            ->with('user')        // agar kolom user bisa dipakai di map()
            ->orderByDesc('id');  // urut terbaru
    }

    /** Header kolom Excel */
    public function headings(): array
    {
        return [
            'Kode',
            'Kategori',
            'Deskripsi',
            'Nama Pelapor',
            'No. HP Pelapor',
            'Umur Pelapor',
            'Disabilitas (Ya/Tidak)',
            'Pekerjaan Pelapor',
            'Provinsi',
            'Kab/Kota',
            'Kecamatan',
            'Alamat Spesifik',
            'Nama Pelaku',
            'Umur Pelaku',
            'Pekerjaan Pelaku',
            'Akun Pelapor',
            'Email Akun',
            'Status',
            'Dibuat',
            'Selesai Pada',
        ];
    }

    /** Mapping tiap baris */
    public function map($c): array
    {
        $statusLabels = Complaint::statusLabels();

        // Hindari baris panjang untuk deskripsi (agar nyaman di Excel)
        $desc = (string) $c->description;
        if (mb_strlen($desc) > 2000) {
            $desc = mb_substr($desc, 0, 2000);
        }
        // Excel sering “mengakali” nomor telp → pakai string apa adanya
        $disability = is_null($c->reporter_is_disability) ? '' : ($c->reporter_is_disability ? 'Ya' : 'Tidak');

        return [
            (string) ($c->code ?? $c->id),
            (string) ($c->category ?? ''),
            $desc,
            (string) ($c->reporter_name ?? ''),
            (string) ($c->reporter_phone ?? ''),     // biarkan string
            (string) ($c->reporter_age ?? ''),       // tampilkan umur asli (bukan bucket)
            $disability,
            (string) ($c->reporter_job ?? ''),
            (string) ($c->province_name ?: $c->province_code ?: ''),
            (string) ($c->regency_name  ?: $c->regency_code  ?: ''),
            (string) ($c->district_name ?: $c->district_code ?: ''),
            (string) ($c->reporter_address ?? ''),
            (string) ($c->perpetrator_name ?? ''),
            (string) ($c->perpetrator_age ?? ''),
            (string) ($c->perpetrator_job ?? ''),
            (string) (optional($c->user)->name ?? ''),
            (string) (optional($c->user)->email ?? ''),
            (string) ($statusLabels[$c->status] ?? \Illuminate\Support\Str::headline($c->status)),
            optional($c->created_at)?->format('d-m-Y H:i') ?? '',
            optional($c->closed_at)?->format('d-m-Y H:i') ?? '',
        ];
    }

    /** Otomatis bold header */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /** Format kolom agar tidak “diubah” Excel (mis. nomor telp jadi notasi ilmiah) */
    public function columnFormats(): array
    {
        // A=Kode, B=Kategori, C=Deskripsi, D=Nama, E=HP, F=Umur, ...
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Kode
            'E' => NumberFormat::FORMAT_TEXT, // No HP
            'F' => NumberFormat::FORMAT_TEXT, // Umur pelapor (string)
            'N' => NumberFormat::FORMAT_TEXT, // Umur pelaku (string)
        ];
    }
}
