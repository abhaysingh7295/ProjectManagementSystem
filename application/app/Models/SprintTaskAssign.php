<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SprintTaskAssign extends Model
{
    protected $table = 'sprintassigned_task';
    protected $primaryKey = 'sprinttask_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['sprinttask_sprintid','sprinttask_taskid'];
    const CREATED_AT = 'sprinttask_created';
    const UPDATED_AT = 'sprinttask_updated';
}
