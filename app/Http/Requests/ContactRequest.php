<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'min:9', 'max:20', 'regex:/^[0-9+\s-]+$/'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'accepted_terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Tu nombre y apellido son requeridos.',
            'full_name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'phone.required' => 'El número de celular es requerido.',
            'phone.min' => 'El celular debe tener al menos 9 dígitos.',
            'phone.regex' => 'Número de celular inválido.',
            'subject.required' => 'El asunto es requerido.',
            'message.required' => 'Por favor escribe tu mensaje.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'accepted_terms.accepted' => 'Debes aceptar los términos y condiciones.',
        ];
    }
}