<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

abstract class RequestAbstract extends FormRequest
{
    protected ?string $propertyRoot = null;

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $response['type']    = 'error';
        $response['message'] = $validator->errors()->all()[0];
        $response['field']   = $validator->errors()->keys()[0];

        throw new HttpResponseException(response()->json($response, HttpStatus::HTTP_UNPROCESSABLE_ENTITY));
    }

    protected function prepareForValidation(): void
    {
        if (!empty($this->propertyRoot)) {
            $this->merge((array)$this->input($this->propertyRoot, []));
        }
    }
}
