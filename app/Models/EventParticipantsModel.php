<?php

namespace App\Models;

use CodeIgniter\Model;

class EventParticipantsModel extends Model
{
  protected $table = 'event_participants';
  protected $primarykey = 'id';
  protected $allowedFields = ['event_id', 'user_id', 'joined_at'];
  protected $useTimestamps = false;
}