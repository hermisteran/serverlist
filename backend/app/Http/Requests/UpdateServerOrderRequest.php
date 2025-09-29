<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServerOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'servers' => ['required', 'array'],
            'servers.*.id' => ['required', 'integer', 'exists:servers,id'],
            'servers.*.order' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'servers.required' => 'Debes enviar el array de servidores.',
            'servers.array' => 'El campo servidores debe ser un array.',
            'servers.*.id.required' => 'Cada servidor debe tener un ID.',
            'servers.*.id.integer' => 'El ID del servidor debe ser un número entero.',
            'servers.*.id.exists' => 'El ID del servidor no existe en la base de datos.',
            'servers.*.order.required' => 'Cada servidor debe tener un orden.',
            'servers.*.order.integer' => 'El orden debe ser un número entero.',
            'servers.*.order.min' => 'El orden no puede ser negativo.',
        ];
    }
}
