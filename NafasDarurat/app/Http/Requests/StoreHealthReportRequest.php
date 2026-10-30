<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHealthReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($v) => is_string($v) ? trim(preg_replace('/\s+/', ' ', $v)) : $v;

        $this->merge([
            'kota' => $clean($this->input('kota')),
            'kelurahan' => $clean($this->input('kelurahan')),
        ]);
    }

    public function rules(): array
    {
        return [
            'kota' => ['required', 'string', 'max:40'],
            'kelurahan' => ['required', 'string', 'max:40'],
            'gejala' => ['required', 'array', 'min:1', 'max:8'],
            'gejala.*' => ['string', 'distinct', Rule::in(array_column(config('nafasdarurat.symptoms'), 'id'))],
            'durasi' => ['required', Rule::in(array_column(config('nafasdarurat.durations'), 'id'))],
            'kelompok' => ['required', Rule::in(array_column(config('nafasdarurat.groups'), 'id'))],
        ];
    }

    public function messages(): array
    {
        return [
            'kota.required' => 'Isi nama kota.',
            'kelurahan.required' => 'Isi kelurahan atau desa.',
            'gejala.required' => 'Pilih minimal satu gejala.',
            'gejala.min' => 'Pilih minimal satu gejala.',
            '*.in' => 'Pilihan tidak valid.',
            '*.max' => 'Isian terlalu panjang.',
        ];
    }
}
