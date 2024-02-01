<?php

namespace App\Http\Requests\V1\Vehicles;

use App\Http\Requests\RequestAbstract;

class CreateVehicleRequest extends RequestAbstract
{
    protected ?string $propertyRoot = 'vehicle';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand' => ['required', 'string'],
            'model' => ['required', 'string'],
        ];
    }
}
