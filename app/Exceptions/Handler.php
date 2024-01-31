<?php

namespace App\Exceptions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception): Response
    {
        $code       = method_exists($exception, 'getCode') ? $exception->getCode() : '-1';
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $message    = $statusCode == 500
            ? trans('exception.internal_error')
            : ($exception->getMessage() ?: trans('exception.method_not_allowed'));

        return match (true) {
            $exception instanceof RepositoryException,
            $exception instanceof BindingResolutionException,
            $exception instanceof NotFoundHttpException => response()->json(
                [
                    'code'    => $code,
                    'type'    => 'error',
                    'message' => $message,
                ],
                $statusCode
            ),
            default => parent::render($request, $exception),
        };
    }
}
