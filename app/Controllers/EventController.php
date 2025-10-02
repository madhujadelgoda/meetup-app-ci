<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EventModel;

class EventController extends ResourceController
{
    protected $modelName = EventModel::class;
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $event = $this->model->find($id);
        if (!$event) {
            return $this->failNotFound("Event not found");
        }
        return $this->respond($event);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (!$data || !isset($data['title'], $data['date'])) {
            return $this->failValidationErrors("Title and date required");
        }

        $data['created_by'] = 1; // later replace with JWT user id
        $this->model->insert($data);

        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        if (!$this->model->find($id)) {
            return $this->failNotFound("Event not found");
        }

        $this->model->update($id, $data);
        return $this->respondUpdated($data);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound("Event not found");
        }

        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }

    
    // from here even join and leaving functions
    public function join($eventId)
    {
        // geeting authenticated user from JWT
        $user = $this->currentUser();
        if (!$user) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        // getting user ID from JWT payload (supports 'id' or 'sub')
        $userId = $user['id'] ?? $user['sub'] ?? null;
        if (!$userId) {
            return $this->fail('Token missing user identifier');
        }

        $participantModel = new \App\Models\EventParticipantsModel();

        // here checking if user already joined the event
        $exists = $participantModel->where('event_id', $eventId)
                                  ->where('user_id', $userId)
                                  ->first();

        if ($exists) {
            return $this->fail('Already joined');
        }

        $participantModel->insert([
            'event_id' => $eventId,
            'user_id'  => $userId
        ]);

        return $this->respond(['message' => 'Successfully joined the event']);
    }

    public function leave($eventId)
    {
        // getting authenticated user from JWT
        $user = $this->currentUser();
        if (!$user) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        // getting user ID from JWT payload (supports 'id' or 'sub')
        $userId = $user['id'] ?? $user['sub'] ?? null;
        if (!$userId) {
            return $this->fail('Token missing user identifier');
        }

        $participantModel = new \App\Models\EventParticipantsModel();

        // here finding the participation record
        $participant = $participantModel->where('event_id', $eventId)
                                        ->where('user_id', $userId)
                                        ->first();

        if (!$participant) {
            return $this->respond(['error' => 'You are not part of this event'], 400);
        }

        // here delete participation record
        $participantModel->delete($participant['id']);

        return $this->respond(['message' => 'Left event successfully'], 200);
    }

    private function currentUser()
    {
        helper('jwt');

        // getting the Authorization header
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader) {
            return null;
        }

        // here extracting token from "Bearer TOKEN"
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return null; 
        }

        $token = $matches[1];

        // here verify the token
        $decoded = verifyJWT($token);
        if (!$decoded) {
            return null; 
        }

        // here ensure token contains user identifier
        if (!isset($decoded['sub'])) {
            return null; // this happen when token missing user identifier
        }

        return $decoded; // here return array with sub (user id), email, name, iat, exp
    }
}
