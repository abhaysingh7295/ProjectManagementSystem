<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sprint;
use App\Models\SprintTaskAssign;
use App\Models\SprintUserAssign;
use App\Models\Task;
use App\Models\User;
use Validator;

class SprintController extends Controller
{
    /**
     * Display a listing of projects
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //get team projects
        $sprint = Sprint::where('sprintproject_id',$id)->with(['assigntask','assignuser'])->orderBy('created_at', 'desc')->get();

        $payload = [
            'message' => 'success',
            'sprint' => $sprint,

        ];
        return response()->json($payload, 200);
    }
    public function create(Request $request)
    {
        //validate the form
        $validator = Validator::make(request()->all(), [
            'sprint_title' => [
                'required',
            ],
            'sprint_description' => [
                'required',
            ],
            'sprint_statedate' => [
                'required',

            ],
            'sprint_enddate' => [
                'required',

            ],
            'sprintproject_id' => [
                'required',

            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $sprint = new Sprint();
        $sprint->sprint_title = $request->sprint_title;
        $sprint->sprint_description = $request->sprint_description;
        $sprint->sprint_statedate = $request->sprint_statedate;
        $sprint->sprint_enddate = $request->sprint_enddate;
        $sprint->sprintproject_id = $request->sprintproject_id;
        $sprint->sprint_status = 'Not_started';
        $sprint->sprint_active_state = 'active';
        $sprint->save();
        return response()->json(['message' => 'Sprint created Successfully', 'sprint' => $sprint], 201);
    }
    public function show($id)
    {
        $sprint = Sprint::with(['assigntask','assignuser'])->find($id);
        if (empty($sprint)) {
            return response()->json(['message' => 'Sprint Not Exist!!!'], 409);
        } else {
            return response()->json(['message' => 'Sucess', 'sprint' => $sprint], 201);
        }
    }
    public function update(Request $request ,$id)
    {
        //validate the form
        $validator = Validator::make(request()->all(), [
            'sprint_title' => [
                'required',
            ],
            'sprint_description' => [
                'required',
            ],
            'sprint_statedate' => [
                'required',

            ],
            'sprint_enddate' => [
                'required',

            ],
            'sprintproject_id' => [
                'required',

            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $sprint = Sprint::find($id);
        if (empty($sprint)) {
            return response()->json(['message' => 'Sprint Not Exist!!!'], 409);
           
        } else {
            $sprint->sprint_title = $request->sprint_title;
            $sprint->sprint_description = $request->sprint_description;
            $sprint->sprint_statedate = $request->sprint_statedate;
            $sprint->sprint_enddate = $request->sprint_enddate;
            $sprint->sprintproject_id = $request->sprintproject_id;
            $sprint->save();
            return response()->json(['message' => 'Sprint Updated Successfully', 'sprint' => $sprint], 200);
        }
    }
    public function delete($id){
        $sprint=Sprint::find($id);
        $sprint->delete();
        return response()->json(['message' => 'Sprint deleted Successfully'], 200);
    }
    public function changeStatus(Request $request ,$id){
        //validate the form
        $validator = Validator::make(request()->all(), [
            'sprint_status' => [
                'required',
            ],
            
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $sprint = Sprint::find($id);
        if (empty($sprint)) {
            return response()->json(['message' => 'Sprint Not Exist!!!'], 409);
           
        } else {
            $sprint->sprint_status = $request->sprint_status;
            $sprint->save();
            return response()->json(['message' => 'Sprint Status Successfully', 'sprint' => $sprint], 200);
        }
    }
    public function getStatus(){
        $status=array('not_started', 'in_progress', 'on_hold', 'cancelled','completed');
        return response()->json(['message' => 'Success', 'status' => $status], 200);
    }

    public function assignSprintToTask(){
        //validate the form
        $validator = Validator::make(request()->all(), [
            'sprinttask_sprintid' => [
                'required',
            ],
            'sprinttask_taskid' => [
                'required',
            ],
            
        ]);
         //validation errors
         if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $sprint = Sprint::find(request('sprinttask_sprintid'));
        if (empty($sprint)) {
            return response()->json(['message' => 'Sprint Not Exist!!!'], 409);
           
        }
        $task = Task::find(request('sprinttask_taskid'));
        if (empty($task)) {
            return response()->json(['message' => 'task Not Exist!!!'], 409);
           
        }
        $assignTask=SprintTaskAssign::where('sprinttask_sprintid',request('sprinttask_sprintid'))->where('sprinttask_taskid',request('sprinttask_taskid'))->first();
        if(empty($assignTask)){
            $assignTaskNew=NEW SprintTaskAssign();
            $assignTaskNew->sprinttask_sprintid=request('sprinttask_sprintid');
            $assignTaskNew->sprinttask_taskid=request('sprinttask_taskid');
            $assignTaskNew->save();
            return response()->json(['message' => 'Assign sprint to task Successfully', 'sprint' => $assignTaskNew], 200);
           
        }else{
            $assignTask->sprinttask_sprintid=request('sprinttask_sprintid');
            $assignTask->sprinttask_taskid=request('sprinttask_taskid');
            $assignTask->save();
            return response()->json(['message' => 'Assign sprint to task successfully', 'sprint' => $assignTask], 200);
        }
    }
    public function assignSprintToUser(){
        //validate the form
        $validator = Validator::make(request()->all(), [
            'sprintuser_sprintid' => [
                'required',
            ],
            'sprintuser_userid' => [
                'required',
            ],
            
        ]);
         //validation errors
         if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $sprint = Sprint::find(request('sprintuser_sprintid'));
        if (empty($sprint)) {
            return response()->json(['message' => 'Sprint Not Exist!!!'], 409);
           
        }
        $user = user::find(request('sprintuser_userid'));
        if (empty($user)) {
            return response()->json(['message' => 'User Not Exist!!!'], 409);
           
        }
        $assignUser=SprintUserAssign::where('sprintuser_sprintid',request('sprintuser_sprintid'))->where('sprintuser_userid',request('sprintuser_userid'))->first();
        if(empty($assignUser)){
            $assignUserNew=NEW SprintUserAssign();
            $assignUserNew->sprintuser_sprintid=request('sprintuser_sprintid');
            $assignUserNew->sprintuser_userid=request('sprintuser_userid');
            $assignUserNew->save();
            return response()->json(['message' => 'Assign sprint to user Successfully', 'sprint' => $assignUserNew], 200);
            
        }else{
            $assignUser->sprintuser_sprintid=request('sprintuser_sprintid');
            $assignUser->sprintuser_userid=request('sprintuser_userid');
            $assignUser->save();
            return response()->json(['message' => 'Assign sprint to user  successfully', 'sprint' => $assignUser], 200);
        }
        
    }
}
