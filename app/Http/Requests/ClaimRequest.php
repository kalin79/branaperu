<?php

namespace App\Http\Requests;

use App\Models\Claim;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Tipo
            'claim_type' => ['required', Rule::in(array_keys(Claim::getTypeOptions()))],

            // Consumidor
            'consumer_first_name' => ['required', 'string', 'min:2', 'max:100'],
            'consumer_last_name' => ['required', 'string', 'min:2', 'max:100'],
            'consumer_document_type' => ['required', Rule::in(array_keys(Claim::getDocumentTypeOptions()))],
            'consumer_document_number' => ['required', 'string', 'min:6', 'max:20'],
            'consumer_phone' => ['required', 'string', 'min:9', 'max:20', 'regex:/^[0-9+\s-]+$/'],
            'consumer_email' => ['required', 'email', 'max:150'],

            // Bien contratado
            'product_name' => ['nullable', 'string', 'max:200'],
            'order_number' => ['nullable', 'string', 'max:50'],
            'product_description' => ['nullable', 'string', 'max:2000'],

            // Detalle
            'claim_detail' => ['required', 'string', 'min:10', 'max:3000'],
            'consumer_request' => ['required', 'string', 'min:5', 'max:2000'],

            // Términos
            'accepted_terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'claim_type.required' => 'Selecciona si es un reclamo o una queja.',
            'consumer_first_name.required' => 'Tus nombres son requeridos.',
            'consumer_last_name.required' => 'Tus apellidos son requeridos.',
            'consumer_document_type.required' => 'Selecciona el tipo de documento.',
            'consumer_document_number.required' => 'El número de documento es requerido.',
            'consumer_phone.required' => 'El número de celular es requerido.',
            'consumer_phone.regex' => 'El celular tiene un formato inválido.',
            'consumer_email.required' => 'El correo electrónico es requerido.',
            'consumer_email.email' => 'Ingresa un correo electrónico válido.',
            'claim_detail.required' => 'Por favor describe el detalle del reclamo.',
            'claim_detail.min' => 'El detalle debe tener al menos 10 caracteres.',
            'consumer_request.required' => 'Indica tu pedido o solicitud.',
            'accepted_terms.accepted' => 'Debes aceptar los términos y condiciones.',
        ];
    }
}