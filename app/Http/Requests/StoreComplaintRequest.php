<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

            // PII (akan dienkripsi di Model)
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

            // Umur dienkripsi (string angka), bucket dibuat otomatis di Model
            'reporter_age'      => ['nullable','string','max:3', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                if (!preg_match('/^\d{1,3}$/', (string)$val)) $fail('Umur harus angka 0-999.');
            }],

            // Lokasi (polos)
            'province_code'     => ['nullable','string','max:10'],
            'province_name'     => ['nullable','string','max:100'],
            'regency_code'      => ['nullable','string','max:10'],
            'regency_name'      => ['nullable','string','max:100'],
            'district_code'     => ['nullable','string','max:10'],
            'district_name'     => ['nullable','string','max:100'],

            // Pelaku (sensitif → encrypted)
            'perpetrator_name'  => ['nullable','string','max:190'],
            'perpetrator_job'   => ['nullable','string','max:120'],
            'perpetrator_age'   => ['nullable','string','max:3', function($attr,$val,$fail){
                if ($val === null || $val === '') return;
                if (!preg_match('/^\d{1,3}$/', (string)$val)) $fail('Umur pelaku harus angka 0-999.');
            }],


            // Hardening: user tidak boleh set ini
            'user_id'           => ['prohibited'],
            'code'              => ['prohibited'],
            'status'            => ['prohibited'],
            'admin_note'        => ['prohibited'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Bersihkan judul/deskripsi dari control chars
        foreach (['title','description'] as $k) {
            if (is_string($this->input($k))) {
                $v = trim($this->input($k));
                $v = preg_replace('/\p{C}+/u', '', $v);
                if ($k === 'description') $v = strip_tags($v);
                $this->merge([$k => $v]);
            }
        }

        // Normalisasi nomor HP menjadi digit saja (untuk konsistensi hash)
        if (is_string($this->input('reporter_phone')) && $this->input('reporter_phone') !== '') {
            $digits = preg_replace('/\D+/', '', (string)$this->input('reporter_phone'));
            $this->merge(['reporter_phone' => $digits]);
        }
    }

    public function attributes(): array
    {
        return [
            
            'description' => 'deskripsi',
            'reporter_phone' => 'nomor telepon pelapor',
            'reporter_age' => 'umur pelapor',
            'perpetrator_age' => 'umur pelaku',
            'attachment' => 'lampiran',
        ];
    }

    public function messages(): array
    {
        return [
            
            'description.required' => 'Deskripsi wajib diisi.',
            'attachment.max' => 'Ukuran lampiran maksimal 10 MB.',
            'attachment.mimes' => 'Tipe lampiran harus jpg, jpeg, png, pdf, doc, atau docx.',
        ];
    }
}
