<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFireReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($v) => is_string($v) ? trim(preg_replace('/\s+/', ' ', $v)) : $v;

        $this->merge([
            'place' => $clean($this->input('place')),
            'description' => $clean($this->input('description')),
        ]);
    }

    public function rules(): array
    {
        return [
            'place' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:300'],
            'status' => ['required', Rule::in(array_keys(config('nafasdarurat.status')))],
            // Kotak batas kasar wilayah Indonesia, cukup untuk menolak koordinat asal-asalan.
            'lat' => ['required', 'numeric', 'between:-11.5,6.5'],
            'lng' => ['required', 'numeric', 'between:94.5,141.5'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'place.required' => 'Isi nama lokasi.',
            'lat.required' => 'Tandai lokasi di peta atau gunakan lokasi saya.',
            'lng.required' => 'Tandai lokasi di peta atau gunakan lokasi saya.',
            'lat.between' => 'Lokasi harus berada di wilayah Indonesia.',
            'lng.between' => 'Lokasi harus berada di wilayah Indonesia.',
            'photo.mimes' => 'Foto harus berformat JPG atau PNG.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'photo.file' => 'Foto tidak valid.',
            'status.in' => 'Kondisi api tidak valid.',
        ];
    }
}
