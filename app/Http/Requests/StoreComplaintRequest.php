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

            // Opsional
            'category'          => ['nullable','string','max:100'],

            // PII (terenkripsi di Model)
            'reporter_name'     => ['nullable','string','max:190'],
            'reporter_phone'    => ['nullable','string','max:30', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                $digits = preg_replace('/\D+/', '', (string)$val);
                if (strlen($digits) < 9 || strlen($digits) > 20) {
                    $fail('Nomor telepon tidak valid.');
                }
            }],
            'reporter_address'  => ['nullable','string','max:500'],
            'reporter_job'      => ['nullable','string','max:120'],
            'reporter_is_disability' => ['nullable','boolean'],
            'reporter_age'      => ['nullable','string','max:3', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                if (!preg_match('/^\d{1,3}$/', (string)$val)) $fail('Umur harus angka 0-999.');
            }],

            // Wilayah: dukung dua mode (codes atau names)
            'province_code'     => ['nullable','string','max:10'],
            'regency_code'      => ['nullable','string','max:10'],
            'district_code'     => ['nullable','string','max:10'],

            'province_name'     => ['nullable','string','max:100'],
            'regency_name'      => ['nullable','string','max:100'],
            'district_name'     => ['nullable','string','max:100'],

            // Pelaku
            'perpetrator_name'  => ['nullable','string','max:190'],
            'perpetrator_job'   => ['nullable','string','max:120'],
            'perpetrator_age'   => ['nullable','string','max:3', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                if (!preg_match('/^\d{1,3}$/', (string)$val)) $fail('Umur pelaku harus angka 0-999.');
            }],

            // Attachment (jika nanti kamu aktifkan upload)
            'attachment'        => ['nullable','file','max:10240','mimes:jpg,jpeg,png,pdf,doc,docx'],

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
            'description'    => 'deskripsi',
            'reporter_phone' => 'nomor telepon pelapor',
            'reporter_age'   => 'umur pelapor',
            'perpetrator_age'=> 'umur pelaku',
            'attachment'     => 'lampiran',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Deskripsi wajib diisi.',
            'attachment.max'  => 'Ukuran lampiran maksimal 10 MB.',
            'attachment.mimes'=> 'Tipe lampiran harus jpg, jpeg, png, pdf, doc, atau docx.',
        ];
    }
}
