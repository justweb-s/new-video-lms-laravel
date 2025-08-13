<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Il nome completo è obbligatorio.',
            'name.max' => 'Il nome completo non può superare i 255 caratteri.',
            'first_name.required' => 'Il nome è obbligatorio.',
            'first_name.max' => 'Il nome non può superare i 255 caratteri.',
            'last_name.required' => 'Il cognome è obbligatorio.',
            'last_name.max' => 'Il cognome non può superare i 255 caratteri.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'L\'email deve essere un indirizzo valido.',
            'email.unique' => 'Questa email è già registrata.',
            'email.max' => 'L\'email non può superare i 255 caratteri.',
            'phone.max' => 'Il numero di telefono non può superare i 20 caratteri.',
            'phone.regex' => 'Il numero di telefono non è in un formato valido.',
            'password.required' => 'La password è obbligatoria.',
            'password.min' => 'La password deve essere di almeno 8 caratteri.',
            'password.confirmed' => 'La conferma password non corrisponde.',
            'password.regex' => 'La password deve contenere almeno una lettera minuscola, una maiuscola e un numero.'
        ];
    }
}
