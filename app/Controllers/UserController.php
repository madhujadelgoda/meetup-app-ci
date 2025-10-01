<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
  protected $userModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
  }

  /**
   * GET /profile
   * Return the authenticated user's profile
   */
  public function profile()
  {
    helper('jwt');

    $header = $this->request->getHeaderLine('Authorization');
    if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
      return $this->respond(['error' => 'Unauthorized: Token missing'], 401);
    }

    $token = $matches[1];
    $userData = verifyJWT($token);

    if (!$userData) {
      return $this->respond(['error' => 'Unauthorized: Invalid token'], 401);
    }

    // fetching user from DB to get latest data
    $user = $this->userModel->find($userData['uid']);

    if(!$user) {
      return $this->respond(['error' => 'User not found'], 404);
    }

    // remove password before returning
    unset($user['password']);

    return $this->respond([
      'message' => 'User profile fetched successfully',
      'user' => $user
    ], 200);
  }
  
  /**
   * PUT /profile
   * Update the authenticated user's profile
   */
  public function updateProfile()
{
    helper('jwt');

    $header = $this->request->getHeaderLine('Authorization');
    if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
        return $this->respond(['error' => 'Unauthorized: Token missing'], 401);
    }

    $token = $matches[1];
    $user = verifyJWT($token);

    if (!$user) {
        return $this->respond(['error' => 'Unauthorized: Invalid token'], 401);
    }

    $data = $this->request->getJSON(true);

    if (!$data || !isset($data['name'], $data['email'])) {
        return $this->respond(['error' => 'Invalid input'], 400);
    }

    $userModel = new \App\Models\UserModel();
    $userModel->update($user['uid'], [
        'name' => $data['name'],
        'email' => $data['email']
    ]);

    return $this->respond([
        'message' => 'Profile updated successfully',
        'user' => $data
    ], 200);
}
}