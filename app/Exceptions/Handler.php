<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{

public function render($request, Throwable $exception)
{
    // Custom response for validation errors
    if ($exception instanceof ValidationException) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $exception->errors()
        ], 422);
    }

    // Fallback for all other exceptions (API only)
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 500);
    }

    // Default behavior for web requests
    return parent::render($request, $exception);
}

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
