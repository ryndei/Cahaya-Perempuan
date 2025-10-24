<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/\s+/', '', (string) $this->input('reporter_phone'));
        $rawDisability = $this->input('reporter_is_disability', null);
        $disability = null;
        if ($rawDisability !== null && $rawDisability !== '') {
            $truthy = ['1', 1, true, 'true', 'ya', 'Ya', 'YA'];
            $falsy  = ['0', 0, false, 'false', 'tidak', 'Tidak', 'TIDAK'];
            if (in_array($rawDisability, $truthy, true)) $disability = 1;
            elseif (in_array($rawDisability, $falsy, true)) $disability = 0;
        }

        $this->merge([
            'category'               => trim((string) $this->input('category')),
            'description'            => (string) $this->input('description'),

            'reporter_name'          => trim((string) $this->input('reporter_name')),
            'reporter_phone'         => $phone,
            'reporter_is_disability' => $disability,
            'reporter_age'           => $this->filled('reporter_age') ? (int) $this->input('reporter_age') : null,
            'reporter_job'           => trim((string) $this->input('reporter_job')),

            'province_code'          => trim((string) $this->input('province_code')),
            'province_name'          => trim((string) $this->input('province_name')),
            'regency_code'           => trim((string) $this->input('regency_code')),
            'regency_name'           => trim((string) $this->input('regency_name')),
            'district_code'          => trim((string) $this->input('district_code')),
            'district_name'          => trim((string) $this->input('district_name')),
            'reporter_address'       => trim((string) $this->input('reporter_address')),

            'perpetrator_name'       => trim((string) $this->input('perpetrator_name')),
            'perpetrator_job'        => trim((string) $this->input('perpetrator_job')),
            'perpetrator_age'        => $this->filled('perpetrator_age') ? (int) $this->input('perpetrator_age') : null,
        ]);
    }

    public function rules(): array
    {
       
        $allowedCategories = [
            'KDRT Terhadap Anak',
            'KDRT Terhadap Istri',
            'Pelecehan Seksual',
            'Kekerasan Seksual Berbasis Online (KSBO)',
            'Kekerasan dalam Pacaran',
            'Lainnya',
        ];

        return [
            'category'               => ['nullable','string','max:100', Rule::in($allowedCategories)],
            'description'            => ['required','string','min:20'],

            'attachment'             => ['nullable','file','mimes:jpg,jpeg,png,pdf,doc,docx,mp4','max:5120'],

            'reporter_name'          => ['nullable','string','max:120'],
            'reporter_phone'         => ['nullable','string','max:30','regex:/^\+?\d{8,15}$/'],
            'reporter_is_disability' => ['nullable','boolean'],
            'reporter_age'           => ['nullable','integer','min:0','max:120'],
            'reporter_job'           => ['nullable','string','max:100'],

            'province_code'          => ['nullable','string','max:10'],
            'province_name'          => ['nullable','string','max:100'],
            'regency_code'           => ['nullable','string','max:10'],
            'regency_name'           => ['nullable','string','max:100'],
            'district_code'          => ['nullable','string','max:10'],
            'district_name'          => ['nullable','string','max:100'],
            'reporter_address'       => ['nullable','string','max:255'],

            'perpetrator_name'       => ['nullable','string','max:120'],
            'perpetrator_job'        => ['nullable','string','max:100'],
            'perpetrator_age'        => ['nullable','integer','min:0','max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.min'            => 'Deskripsi minimal :min karakter.',
            'attachment.mimes'           => 'Lampiran harus berupa: jpg, jpeg, png, pdf, doc, docx, atau mp4.',
            'attachment.max'             => 'Ukuran lampiran maksimal 5MB.',
            'reporter_phone.regex'       => 'Format nomor telepon tidak valid. Gunakan 8–15 digit dan boleh diawali tanda +.',
            'reporter_is_disability.boolean' => 'Nilai disabilitas harus ya/tidak (1/0).',
            'category.in'                => 'Kategori tidak dikenal.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category'                 => 'kategori',
            'description'              => 'deskripsi',
            'attachment'               => 'lampiran',
            'reporter_name'            => 'nama pelapor',
            'reporter_phone'           => 'nomor telepon pelapor',
            'reporter_is_disability'   => 'status disabilitas pelapor',
            'reporter_age'             => 'umur pelapor',
            'reporter_job'             => 'pekerjaan pelapor',
            'province_code'            => 'kode provinsi',
            'province_name'            => 'nama provinsi',
            'regency_code'             => 'kode kabupaten/kota',
            'regency_name'             => 'nama kabupaten/kota',
            'district_code'            => 'kode kecamatan',
            'district_name'            => 'nama kecamatan',
            'reporter_address'         => 'alamat spesifik',
            'perpetrator_name'         => 'nama pelaku',
            'perpetrator_job'          => 'pekerjaan pelaku',
            'perpetrator_age'          => 'umur pelaku',
        ];
    }
}
