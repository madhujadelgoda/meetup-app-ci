<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Auth extends ResourceController
{
    public function register()
{
    helper('jwt');
    $userModel = new UserModel();

    // here trying the JSON first
    $data = $this->request->getJSON(true);

    // here if there's no JSON, try POST
    if (!$data) {
        $data = $this->request->getPost();
    }

    if (!$data || !isset($data['email'], $data['password'], $data['name'])) {
        return $this->respond(['error' => 'Invalid input'], 400);
    }

    // checking that if the email is already exists....
    if ($userModel->where('email', $data['email'])->first()) {
        return $this->respond(['error' => 'Email already exists'], 400);
    }

    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    $userModel->insert($data);

    return $this->respond(['message' => 'User registered successfully'], 201);
}

    public function login() {
        helper('jwt');
        $userModel = new UserModel();
        $data = $this->request->getJSON(true);

        $user = $userModel->where('email', $data['email'])->first();
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return $this->respond(['error' => 'Invalid credentials'], 401);
        }

        $token = generateJWT($user);

        return $this->respond(['token' => $token], 200);
    }
}

