<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // getting Authorization header
        $header = $request->getHeaderLine('Authorization');

        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'Unauthorized: Token missing'
            ]);
        }

        $token = $matches[1];
        $key = getenv('JWT_SECRET'); // use secret from .env

        try {
            // decode the token
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // attach user info to the request object
            $request->user = (array) $decoded;

        } catch (\Exception $e) {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'Unauthorized: Invalid token',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
