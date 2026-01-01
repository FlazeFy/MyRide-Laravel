<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Models\ErrorModel;

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
            $this->storeError($e);
        });
    }

    private function storeError(Throwable $exception)
    {
        ErrorModel::createError($exception->getMessage(), $exception->getTraceAsString(), $exception->getFile(), $exception->getLine());
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'you need to include the authorization token from login' 
            ], 401);
        }

        return redirect()->guest(route('login')); 
    }
}
