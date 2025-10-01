<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    public function profile()
{
    // here access user info from request
    $user = $this->request->user ?? null;

    if (!$user) {
        return $this->respond(['error' => 'User not found'], 401);
    }

    return $this->respond([
        'message' => 'This is a protected route',
        'user' => $user['name']
    ], 200);
}

}
