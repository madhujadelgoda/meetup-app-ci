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
}
