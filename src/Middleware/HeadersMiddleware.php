<?php
namespace MykeOn\Middleware;

class HeadersMiddleware
{
    private $allowedHeaders = [
        'X-Requested-With',
        'Content-Type',
        'Accept',
        'Origin',
        'Authorization'
    ];

    private $allowedMethods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'PATCH',
    ];

    public function __invoke($request, $response, $next)
    {
        return $next($request, $response)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders))
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods));
    }
}
