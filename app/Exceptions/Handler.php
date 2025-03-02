<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Response;

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

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            if ($exception->getStatusCode() == 403) {
                return response()->view('error.403', [
                    'exception' => $exception
                ], Response::HTTP_FORBIDDEN);
            }

            if ($exception->getStatusCode() == 404) {
                // Check if the user came from an "admin" route
                $redirectRoute = $request->is('admin/*') ? 'admin.dashboard' : 'dashboard';

                return response()->view('error.404', [
                    'exception' => $exception,
                    'redirectRoute' => $redirectRoute
                ], Response::HTTP_NOT_FOUND);
            }
        }

        return parent::render($request, $exception);
    }
}
