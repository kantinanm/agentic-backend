<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception){
         // If the request wants JSON (AJAX doesn't always want JSON)
    if ($request->wantsJson()) {

        if ($exception instanceof \Laravel\Passport\Exceptions\MissingScopeException) 
        {
          return response()->json(['error' => 'Unauthenticated'], 403); 
          //abort(401); 
        }
        
        if($exception instanceof MissingScopeException){

            if ($exception instanceof MissingScopeException && $request->wantsJson()){
                return response()->json([
                    'error' => 'Unauthenticated',
                ], 403);
            }
            // Define the response
            $response = [
                'errors' => 'Sorry, something went wrong.'
            ];
    
            // If the app is in debug mode
            if (config('app.debug')) {
                // Add the exception class name, message and stack trace to response
                //$response['exception'] = get_class($exception); // Reflection might be better here
                $response['message'] = $exception->getMessage();
                //$response['trace'] = $exception->getTrace();
            }
    
            // Default response of 401
            $status = 403;//forbidden
    
            // If this exception is an instance of HttpException
            if ($this->isHttpException($exception)) {
                // Grab the HTTP status code from the Exception
                $status = $exception->getStatusCode();
            }
    
            // Return a JSON response with the response array and status code
            return response()->json($response, $status);
        }
    
        }
        return parent::render($request, $exception);
    }


}
