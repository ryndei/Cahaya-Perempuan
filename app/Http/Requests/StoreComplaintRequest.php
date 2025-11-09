<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'description'       => ['bail','required','string','min:30'],

            'category'          => ['required','string','max:100'],

            // PII (terenkripsi di Model)
            'reporter_name'     => ['required','string','max:190'],
            'reporter_phone'    => ['string','required','max:30', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                $digits = preg_replace('/\D+/', '', (string)$val);
                if (strlen($digits) < 9 || strlen($digits) > 20) {
                    $fail('Nomor telepon tidak valid.');
                }
            }],
            'reporter_address'  => ['required','string','max:500'],
            'reporter_job'      => ['required','string','max:120'],
            'reporter_is_disability' => ['required','boolean'],
            'reporter_age'      => ['required','string','max:3', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                if (!preg_match('/^\d{1,3}$/', (string)$val)) $fail('Umur harus angka 0-999.');
            }],

           
            'province_code'     => ['nullable','string','max:10'],
            'regency_code'      => ['nullable','string','max:10'],
            'district_code'     => ['nullable','string','max:10'],

            'province_name'     => ['nullable','string','max:100'],
            'regency_name'      => ['nullable','string','max:100'],
            'district_name'     => ['nullable','string','max:100'],

            // Pelaku
            'perpetrator_name'  => ['required','string','max:190'],
            'perpetrator_job'   => ['nullable','string','max:120'],
            'perpetrator_age'   => ['nullable','string','max:3', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                if (!preg_match('/^\d{1,3}$/', (string)$val)) $fail('Umur pelaku harus angka 0-999.');
            }],

            // Hardening
            'user_id'           => ['prohibited'],
            'code'              => ['prohibited'],
            'status'            => ['prohibited'],
            'admin_note'        => ['prohibited'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['title','description'] as $k) {
            if (is_string($this->input($k))) {
                $v = trim($this->input($k));
                $v = preg_replace('/\p{C}+/u', '', $v);
                if ($k === 'description') $v = strip_tags($v);
                $this->merge([$k => $v]);
            }
        }

        if (is_string($this->input('reporter_phone')) && $this->input('reporter_phone') !== '') {
            $digits = preg_replace('/\D+/', '', (string)$this->input('reporter_phone'));
            $this->merge(['reporter_phone' => $digits]);
        }

        // Jika kode tersedia, kita abaikan "name" agar tidak dipakai & tidak bikin bingung
        if ($this->filled('province_code')) {
            $this->merge(['province_name' => null]);
        }
        if ($this->filled('regency_code')) {
            $this->merge(['regency_name' => null]);
        }
        if ($this->filled('district_code')) {
            $this->merge(['district_name' => null]);
        }
    }

    public function attributes(): array
{
    return [
        'description'            => 'deskripsi',
        'category'               => 'kategori',
        'reporter_name'          => 'nama pelapor',
        'reporter_phone'         => 'nomor telepon pelapor',
        'reporter_address'       => 'alamat pelapor',
        'reporter_job'           => 'pekerjaan pelapor',
        'reporter_is_disability' => 'status disabilitas pelapor',
        'reporter_age'           => 'umur pelapor',
        'perpetrator_name'       => 'nama pelaku',
        'perpetrator_age'        => 'umur pelaku',
        'province_name'          => 'nama provinsi',
        'regency_name'           => 'nama kab/kota',
        'district_name'          => 'nama kecamatan',
    ];
}

    public function messages(): array
    {
        
        return [
            'required' => ':Attribute wajib diisi.',

            // Aturan umum lain (opsional tapi membantu)
            'string'   => ':Attribute harus berupa teks.',
            'max'      => ':Attribute maksimal :max karakter.',
            'boolean'  => ':Attribute hanya boleh Ya/Tidak.',
            'digits'   => ':Attribute harus :digits digit.',
            'digits_between' => ':Attribute harus :min–:max digit.',
            'min'      => ':Attribute minimal :min karakter.',
            'in'       => 'Pilihan :attribute tidak valid.',

            // Pesan khusus field tertentu
            'description.required' => 'Deskripsi wajib diisi.',
            'category.required'    => 'Kategori wajib dipilih.',
            'reporter_phone.digits_between' => 'Nomor telepon harus 9–20 digit angka.',
            'reporter_age.regex'   => 'Umur pelapor harus angka 0–999.',
            'perpetrator_age.regex'=> 'Umur pelaku harus angka 0–999.',
        ];
    }
}
