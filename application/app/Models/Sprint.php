<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sprint extends Model
{
    use SoftDeletes;
    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'sprint_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['sprint_id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];

    public function assigntask() {
        return $this->belongsToMany('App\Models\Task', 'sprintassigned_task', 'sprinttask_sprintid', 'sprinttask_taskid');
    }

    public function assignuser() {
        return $this->belongsToMany('App\Models\User', 'sprintassigned_user', 'sprintuser_sprintid', 'sprintuser_userid');
    }
    

}
