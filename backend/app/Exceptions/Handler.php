<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * JSON responses never include exception traces or internal paths, even when APP_DEBUG is true.
     * Machine-readable `error` codes let clients show appropriate fallback screens.
     */
    protected function convertExceptionToArray(Throwable $e): array
    {
        if ($e instanceof NotFoundHttpException) {
            return [
                'error' => 'not_found',
                'message' => 'The requested resource was not found.',
            ];
        }

        if ($e instanceof AccessDeniedHttpException) {
            $msg = $e->getMessage();
            if ($msg === '' || $this->looksLikeInternalMessage($msg)) {
                $msg = 'You do not have permission to perform this action.';
            }

            return [
                'error' => 'forbidden',
                'message' => $msg,
            ];
        }

        if ($e instanceof TooManyRequestsHttpException) {
            return [
                'error' => 'rate_limited',
                'message' => 'Too many requests. Please wait and try again.',
            ];
        }

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();

            if ($status === 503) {
                return [
                    'error' => 'service_unavailable',
                    'message' => 'The service is temporarily unavailable. Please try again shortly.',
                ];
            }

            if ($status === 404) {
                $msg = $e->getMessage();
                if ($msg === '' || $this->looksLikeInternalMessage($msg)) {
                    $msg = 'The requested resource was not found.';
                }

                return [
                    'error' => 'not_found',
                    'message' => $msg,
                ];
            }

            if ($status >= 500) {
                return [
                    'error' => 'server_error',
                    'message' => 'Something went wrong. Please try again later.',
                ];
            }

            $msg = $e->getMessage();
            if ($msg === '' || $this->looksLikeInternalMessage($msg)) {
                $msg = 'Request could not be completed.';
            }

            return [
                'error' => 'http_error',
                'message' => $msg,
            ];
        }

        return [
            'error' => 'server_error',
            'message' => 'Something went wrong. Please try again later.',
        ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->shouldReturnJson($request, $exception)
            ? response()->json([
                'error' => 'unauthenticated',
                'message' => $exception->getMessage() ?: 'Authentication required.',
            ], 401)
            : redirect()->guest($exception->redirectTo($request) ?? route('login'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'error' => 'validation_failed',
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    protected function looksLikeInternalMessage(string $message): bool
    {
        return (bool) preg_match('/\\\\|\/[a-z]+\.php|SQLSTATE|PDO|Stack trace/i', $message);
    }
}
