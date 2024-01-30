<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected array $response;

    public function __construct()
    {
        $this->response = [
            'type'    => 'success',
            'code'    => 0,
            'message' => null,
            'data'    => null,
        ];
    }

    protected function buildResponseError($exception, int $codeStatus): JsonResponse
    {
        $field = null;

        if (method_exists($exception, 'getMessageBag')) {
            $message = $exception->getMessageBag()->first();
            $field   = $exception->getMessageBag()->keys()[0];
        } else {
            $message = $exception->getMessage();
        }

        $data = method_exists($exception, 'getParams') ? $exception->getParams() : null;
        $code = (int)($exception->getCode() == 0) ? -1 : (int)$exception->getCode();

        $this->response = [
            'type'    => 'error',
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
            'field'   => $field,
        ];

        return Response::json($this->response, $codeStatus);
    }
}
