<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    function generateJWT($user) {
        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(),                // issued at
            'exp' => time() + 3600,         // expiration: 1 hour
            'uid' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];
        return JWT::encode($payload, $key, 'HS256');
    }
}

if (!function_exists('verifyJWT')) {
    function verifyJWT($token) {
        $key = getenv('JWT_SECRET');
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return (array)$decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
}
