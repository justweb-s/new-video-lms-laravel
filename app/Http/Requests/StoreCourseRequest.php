<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric|min:0|max:999999.99',
            'duration_weeks' => 'nullable|integer|min:1|max:104',
            'prerequisites' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Il nome del corso è obbligatorio.',
            'name.max' => 'Il nome del corso non può superare i 255 caratteri.',
            'description.max' => 'La descrizione non può superare i 2000 caratteri.',
            'image.image' => 'Il file deve essere un\'immagine.',
            'image.mimes' => 'L\'immagine deve essere in formato JPEG, PNG, JPG, GIF o WebP.',
            'image.max' => 'L\'immagine non può superare i 2MB.',
            'price.required' => 'Il prezzo è obbligatorio.',
            'price.numeric' => 'Il prezzo deve essere un numero.',
            'price.min' => 'Il prezzo non può essere negativo.',
            'price.max' => 'Il prezzo non può superare €999,999.99.',
            'duration_weeks.integer' => 'La durata deve essere un numero intero.',
            'duration_weeks.min' => 'La durata deve essere almeno 1 settimana.',
            'duration_weeks.max' => 'La durata non può superare le 104 settimane (2 anni).',
            'prerequisites.max' => 'I prerequisiti non possono superare i 1000 caratteri.'
        ];
    }
}
