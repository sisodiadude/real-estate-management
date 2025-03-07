<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $redirectRoute = $this->getRedirectRoute($request);

            $view = match ($statusCode) {
                Response::HTTP_FORBIDDEN => 'error.403',
                Response::HTTP_NOT_FOUND => 'error.404',
                Response::HTTP_INTERNAL_SERVER_ERROR => 'error.500',
                default => null,
            };

            if ($view) {
                return response()->view($view, [
                    'exception' => $exception,
                    'redirectRoute' => $redirectRoute
                ], $statusCode);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Get the appropriate redirect route based on request origin.
     */
    private function getRedirectRoute($request): string
    {
        return $request->is('admin/*') ? 'admin.dashboard' : 'dashboard';
    }
}
