<?php

namespace App\Http\Requests\V1\Users;

use App\Http\Requests\RequestAbstract;
use Illuminate\Validation\Rule;

class CreateUserRequest extends RequestAbstract
{
    protected ?string $propertyRoot = 'user';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed'],
        ];
    }
}
