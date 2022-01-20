<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SprintUserAssign extends Model
{
    protected $table = 'sprintassigned_user';
    protected $primaryKey = 'sprintuser_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['sprintuser_sprintid','sprintuser_userid'];
    const CREATED_AT = 'sprintuser_created';
    const UPDATED_AT = 'sprintuser_updated';
}
