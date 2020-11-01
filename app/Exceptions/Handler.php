<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Log::error('Handler . render');
        
        Log::error($exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine(), [
            'url' => ($request) ? $request->getUri() : null,
            'params' => ($request) ? $request->all() : null,
        ]);
        
        if ( $exception instanceof \Illuminate\Session\TokenMismatchException ) {
            \Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate();
            return redirect('/');
        }

        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 404) {
                return response()->view('errors.' . '404', [], 404);
            }
            if ($exception->getStatusCode() == 405) {
                return response()->view('errors.' . '405', ['message'=>$exception->getMessage()], 405);
            }
        }
        
        return parent::render($request, $exception);
    }

    // /**
    //  * Prepare a JSON response for the given exception.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \Throwable  $e
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // protected function prepareJsonResponse($request, Throwable $e)
    // {
    //     return new JsonResponse(
    //         $this->convertExceptionToArray($e),
    //         $this->isHttpException($e) ? $e->getStatusCode() : 500,
    //         $this->isHttpException($e) ? $e->getHeaders() : [],
    //         JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    //     );
    // }

    protected function invalidJson($request, ValidationException $exception)
    {
        // Log::error($exception);
        return response()->json([
            'status' => $exception->status,
            'success' => false,
            'message' => $this->prepareErrorMessage($exception->errors()),
            // 'errors' => $this->prepareErrorMessage($exception->errors()),
        ], $exception->status);
    }

    private function prepareErrorMessage($errors)
    {
        $message = '';
        try{
            foreach($errors as $k => $val){
                $message .= implode(',', $val) . ' ';
            }
        }catch(Exception $e){}

        return $message;
    }
}
