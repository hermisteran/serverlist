<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:5120'],
            'host' => ['required', 'string', 'max:100'],
            'ip' => ['required', 'string', 'max:45', 'regex:/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/'],
            'description' => ['nullable', 'string', 'max:200'],
            'order' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [

            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe ser de tipo JPG, JPEG, PNG o GIF.',
            'image.max' => 'La imagen no debe superar los 5MB.',

            'host.required' => 'El campo host es obligatorio.',
            'host.string' => 'El host debe ser una cadena de texto.',
            'host.max' => 'El host no puede superar los 100 caracteres.',

            'ip.required' => 'El campo IP es obligatorio.',
            'ip.string' => 'La IP debe ser una cadena de texto.',
            'ip.max' => 'La IP no puede superar los 15 caracteres.',
            'ip.regex' => 'La IP debe tener formato IPv4 válido.',

            'description.max' => 'La descripción no puede superar los 200 caracteres.',

        ];
    }
}
