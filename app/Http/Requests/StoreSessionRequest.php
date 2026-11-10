<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Accès sans authentification préalable
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'age_eleve' => ['required', 'integer', 'min:6', 'max:99'],
            'classe_eleve' => ['required', 'string', 'max:100'],
            'sujet' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    /**
     * Custom validation messages in French.
     */
    public function messages(): array
    {
        return [
            'age_eleve.required' => 'Veuillez renseigner votre âge.',
            'age_eleve.integer' => "L'âge doit être un nombre valide.",
            'age_eleve.min' => "L'âge minimum est de 6 ans.",
            'classe_eleve.required' => 'Veuillez sélectionner ou indiquer votre classe / niveau.',
            'sujet.required' => 'Veuillez décrire le sujet ou la notion que vous souhaitez travailler.',
            'sujet.min' => 'La description du sujet doit comporter au moins 3 caractères.',
        ];
    }
}
