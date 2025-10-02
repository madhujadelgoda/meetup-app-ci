<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    function generateJWT($user) {
        $key = getenv('JWT_SECRET');
        $payload = [
            'iat'   => time(),              // issued at timestamp
            'exp'   => time() + 3600,       // expiration timestamp (1 hour)
            'sub'   => $user['id'],         // user identifier
            'email' => $user['email'],   
            'name'  => $user['name']     
        ];
        return JWT::encode($payload, $key, 'HS256');
    }
}

if (!function_exists('verifyJWT')) {
    function verifyJWT($token) {
        $key = getenv('JWT_SECRET');
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
}
