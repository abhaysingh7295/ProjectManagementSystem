<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Models\TaskStatus;
use App\Models\Milestone;
use App\Models\TaskAssigned;
use App\Models\SprintTaskAssign;
use App\Models\Event;
use App\Models\Knowledgebase;
use App\Models\KbCategories;
use Validator;
use Illuminate\Support\Str;

class TaskController extends Controller
{

    //Task Detail 
    public function taskDetail($id)
    {
        //get Task Detail
        $task = Task::where('task_id', $id)->withCount('children')->with(['children', 'assignSprint', 'assigned', 'attachments', 'taskstatus', 'comments'])->orderBy('task_id', 'desc')->first();
        $payload = [
            'message' => 'success',
            'task' => $task,

        ];
        return response()->json($payload, 200);
    }

    //Task Detail 
    public function list()
    {
        //get task list
        $task = Task::withCount('children')->with(['children', 'assignSprint', 'assigned', 'attachments', 'taskstatus', 'comments'])->orderBy('task_id', 'desc')->get();
        $payload = [
            'message' => 'success',
            'task' => $task,

        ];
        return response()->json($payload, 200);
    }

    //Task Detail 
    public function commentList($id)
    {
        //get team Comment
        $comment = Comment::where('commentresource_id', $id)->where('commentresource_type', 'task')->with(['creator'])->orderBy('comment_created', 'desc')->get();
        $payload = [
            'message' => 'success',
            'comment' => $comment,

        ];
        return response()->json($payload, 200);
    }
    public function getComment($id)
    {
        //get team Comment
        $task = Comment::where('task_id', $id)->with(['comments', 'creator'])->first();
        $payload = [
            'message' => 'success',
            'task' => $task,

        ];
        return response()->json($payload, 200);
    }

    public function createComment()
    {
        //validate the form
        $validator = Validator::make(request()->all(), [
            'comment_creatorid' => [
                'required',
            ],
            'comment_text' => [
                'required',
            ],
            'commentresource_id' => [
                'required',

            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $task = Task::find(request('commentresource_id'));
        if (empty($task)) {
            return response()->json(['message' => 'Task id Not Exist!!!'], 409);
        }
        $user = User::find(request('comment_creatorid'));
        if (empty($user)) {
            return response()->json(['message' => 'comment creatorid Not Exist!!!'], 409);
        }
        //save new user
        $comment = new Comment();

        //data
        $comment->comment_creatorid = request('comment_creatorid');
        $comment->comment_text = request('comment_text');
        $comment->commentresource_type = 'task';
        $comment->commentresource_id = request('commentresource_id');

        //save and return id
        if ($comment->save()) {
            return response()->json(['message' => 'Success', 'comment' => $comment], 201);
        } else {
            return response()->json(['message' => 'some thing went wrong'], 409);
        }
    }
    public function destroyComment($comment_id)
    {
        $comment = Comment::find($comment_id);
        if (empty($comment)) {
            return response()->json(['message' => 'Comment not found!!!'], 409);
        } else {
            $comment->delete();
            return response()->json(['message' => 'Comment deleted Successfully'], 200);
        }
    }
    public function createTask()
    {
        //validate the form
        $validator = Validator::make(request()->all(), [
            'task_title' => ['required'],
            'task_status' => ['required'],
            'task_priority' => ['required'],
            'task_parentid' => ['required'],
            'task_creatorid' => ['required'],
            'task_clientid' => ['required'],
            'task_projectid' => ['required'],
            'task_milestoneid' => ['required'],
            'task_date_start' => ['required'],
            'task_date_due' => ['required'],
            'task_billable' => ['required'],
            'assign_userid' => ['required'],
            'sprinttask_sprintid' => ['required']
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $task = new Task();
        $task->task_title = request('task_title');
        $task->task_status = request('task_status');
        $task->task_priority = request('task_priority');
        $task->task_parentid = request('task_parentid');
        $task->task_creatorid = request('task_creatorid');
        $task->task_clientid = request('task_clientid');
        $task->task_projectid = request('task_projectid');
        $task->task_milestoneid = request('task_milestoneid');
        $task->task_date_start = request('task_date_start');
        $task->task_date_due = request('task_date_due');
        $task->task_billable = request('task_billable');
        $task->task_active_state = 'active';
        $task->task_visibility = 'visible';
        if (request()->filled('task_description')) {
            $task->task_description = request('task_description');
        }
        if (request()->filled('task_previous_status')) {
            $task->task_previous_status = request('task_previous_status');
        }
        $task->save();
        if (empty($task)) {

            return response()->json(['message' => 'Something went wrong!!!'], 409);
        } else {
            $taskassign = TaskAssigned::where('tasksassigned_userid', request('assign_userid'))->where('tasksassigned_taskid',$task->task_id)->first();
            if (empty($taskassign)) {
                $taskassign = new TaskAssigned();
                $taskassign->tasksassigned_taskid = $task->task_id;
                $taskassign->tasksassigned_userid = request('assign_userid');
                $taskassign->save();
               
            } else {
                $taskassign->tasksassigned_taskid =  $task->task_id;
                $taskassign->tasksassigned_userid = request('assign_userid');
                $taskassign->save(); 
            }
            $assignTaskSprint=SprintTaskAssign::where('sprinttask_sprintid',request('sprinttask_sprintid'))->where('sprinttask_taskid',$task->task_id)->first();
            if(empty($assignTaskSprint)){
                $assignTaskNew=NEW SprintTaskAssign();
                $assignTaskNew->sprinttask_sprintid=request('sprinttask_sprintid');
                $assignTaskNew->sprinttask_taskid= $task->task_id;
                $assignTaskNew->save();
            }
            return response()->json(['message' => 'Created Successfully', 'task' => $task], 200);
        }
    }
    public function updateTask($id)
    {
        //validate the form
        $validator = Validator::make(request()->all(), [
            'task_title' => ['required'],
            'task_status' => ['required'],
            'task_priority' => ['required'],
            'task_parentid' => ['required'],
            'task_creatorid' => ['required'],
            'task_clientid' => ['required'],
            'task_projectid' => ['required'],
            'task_milestoneid' => ['required'],
            'task_date_start' => ['required'],
            'task_date_due' => ['required'],
            'assign_userid' => ['required'],
            'sprinttask_sprintid' => ['required']
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $task = Task::find($id);
        if (empty($task)) {
            return response()->json(['message' => 'Task not found!!!'], 409);
        }
        $task->task_title = request('task_title');
        $task->task_status = request('task_status');
        $task->task_priority = request('task_priority');
        $task->task_parentid = request('task_parentid');
        $task->task_creatorid = request('task_creatorid');
        $task->task_clientid = request('task_clientid');
        $task->task_projectid = request('task_projectid');
        $task->task_milestoneid = request('task_milestoneid');
        $task->task_date_start = request('task_date_start');
        $task->task_date_due = request('task_date_due');
        if (request()->filled('task_billable')) {
            $task->task_billable = request('task_billable');
        }
        if (request()->filled('task_active_state')) {
            $task->task_active_state = request('task_active_state');
        }
        if (request()->filled('task_visibility')) {
            $task->task_visibility = request('task_visibility');
        }
        if (request()->filled('task_description')) {
            $task->task_description = request('task_description');
        }
        if (request()->filled('task_previous_status')) {
            $task->task_previous_status = request('task_previous_status');
        }
        $task->save();
        if (empty($task)) {
           
            return response()->json(['message' => 'Something went wrong!!!'], 409);
        } else {
            $taskassign = TaskAssigned::where('tasksassigned_userid', request('assign_userid'))->where('tasksassigned_taskid',$task->task_id)->first();
            if (!empty($taskassign)) {
                
                $taskassign->tasksassigned_taskid = $task->task_id;
                $taskassign->tasksassigned_userid = request('assign_userid');
                $taskassign->save();
               
            } else {
                $taskassignD = TaskAssigned::where('tasksassigned_taskid',$task->task_id)->first();
                $taskassignD->delete();
                $taskassign = new TaskAssigned();
                $taskassign->tasksassigned_taskid =  $task->task_id;
                $taskassign->tasksassigned_userid = request('assign_userid');
                $taskassign->save(); 
            }
            $assignTaskSprint=SprintTaskAssign::where('sprinttask_sprintid',request('sprinttask_sprintid'))->where('sprinttask_taskid',$task->task_id)->first();
            if(!empty($assignTaskSprint)){
                $assignTaskSprint->sprinttask_sprintid=request('sprinttask_sprintid');
                $assignTaskSprint->sprinttask_taskid= $task->task_id;
                $assignTaskSprint->save();
            }else{
                $assignTaskNew=NEW SprintTaskAssign();
                $assignTaskNew->sprinttask_sprintid=request('sprinttask_sprintid');
                $assignTaskNew->sprinttask_taskid= $task->task_id;
                $assignTaskNew->save();
            }
            return response()->json(['message' => 'Updated Successfully', 'task' => $task], 200);
        }
    }
    public function getTaskStatus()
    {
        $TaskStatus = TaskStatus::orderBy('taskstatus_position', 'asc')->get();
        if (empty($TaskStatus)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'taskstatus' => $TaskStatus], 200);
        }
    }
    public function getProjectMilestone($id)
    {
        $milestone = Milestone::where('milestone_projectid',$id)->orderBy('milestone_position', 'asc')->get();
        if (empty($milestone)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'milestone' => $milestone], 200);
        }
    }
    public function taskPriority()
    {
        $Priority = array('low', 'normal', 'high', 'urgent');
        return response()->json(['message' => 'Success', 'priority' => $Priority], 200);
    }
    public function updatetaskflag(){
        //validate the form
        $validator = Validator::make(request()->all(), [
            'task_id' => ['required']
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $id=request('task_id');
        $task = Task::find($id);
        if (empty($task)) {
            return response()->json(['message' => 'Task not found!!!'], 409);
        }
        if(request()->filled('task_previous_status')){
            $task->task_previous_status=request('task_previous_status');
        }
        if(request()->filled('task_active_state')){
            $task->task_active_state=request('task_active_state');
        }
        if(request()->filled('task_priority')){
            $task->task_priority=request('task_priority');
        }
        if(request()->filled('task_status')){
            $task->task_status=request('task_status');
        }
        if(request()->filled('task_milestoneid')){
            $task->task_milestoneid=request('task_milestoneid');
        }
        $task->save();
        if(!empty($task)){
            return response()->json(['message' => 'Updated Successfully', 'task' => $task], 200);
        }else{
            return response()->json(['message' => 'something went wrong!!!'], 409);
        }
    }
    public function getProjectFeed($id){
        $event=Event::select('event_parent_title','event_item_content3','event_item_content','event_item')->where('eventresource_type','project')->where('eventresource_id',$id)->orderBy('event_created', 'desc')->get();
        if (empty($event)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'feed' => $event], 200);
        }
    }
    public function getKbCategory(){
        $kb_category=KbCategories::orderBy('kbcategory_position','asc')->get();
        if (empty($kb_category)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'kb_category' => $kb_category], 200);
        }
    }
    public function createkb(){
        //validate the form
        $validator = Validator::make(request()->all(), [
            'knowledgebase_creatorid' => ['required'],
            'knowledgebase_categoryid' => ['required'],
            'knowledgebase_title' => ['required'],
            'knowledgebase_text' => ['required']
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $kb=New Knowledgebase();
        $kb->knowledgebase_slug=$this->createSlug(request('knowledgebase_title'));
        $kb->knowledgebase_creatorid=request('knowledgebase_creatorid');
        $kb->knowledgebase_categoryid=request('knowledgebase_categoryid');
        $kb->knowledgebase_title=request('knowledgebase_title');
        $kb->knowledgebase_text=request('knowledgebase_text');
        $kb->save();
        if (empty($kb)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'knowledgebase' => $kb], 200);
        }
    }
    public function updatekb($id){
        //validate the form
        $validator = Validator::make(request()->all(), [
            'knowledgebase_creatorid' => ['required'],
            'knowledgebase_categoryid' => ['required'],
            'knowledgebase_title' => ['required'],
            'knowledgebase_text' => ['required']
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => $errors], 409);
        }
        $kb= Knowledgebase::find($id);
        if(empty($kb)){
            return response()->json(['message' => 'Record not available !!!'], 409);
        }
        $kb->knowledgebase_slug=$this->createSlug(request('knowledgebase_title'),$id);
        $kb->knowledgebase_creatorid=request('knowledgebase_creatorid');
        $kb->knowledgebase_categoryid=request('knowledgebase_categoryid');
        $kb->knowledgebase_title=request('knowledgebase_title');
        $kb->knowledgebase_text=request('knowledgebase_text');
        $kb->save();
        if (empty($kb)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'knowledgebase' => $kb], 200);
        }
    }
    public function getkbByID($id){
       
        $kb= Knowledgebase::where('knowledgebase_id',$id)->with(['category'])->first();
        if (empty($kb)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'knowledgebase' => $kb], 200);
        }
    }
    public function getkblist(){
       
        $kb= Knowledgebase::with(['category'])->get();
        if (empty($kb)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            return response()->json(['message' => 'Success', 'knowledgebase' => $kb], 200);
        }
    }
    public function deletekb($id){
       
        $kb= Knowledgebase::find($id);

        if (empty($kb)) {
            return response()->json(['message' => 'Record not available !!!'], 409);
        } else {
            $kb->delete();
            return response()->json(['message' => 'Success', 'knowledgebase' => $kb], 200);
        }
    }
    public function createSlug($title, $id = 0)
    {
        $slug = str_slug($title);
        $allSlugs = $this->getRelatedSlugs($slug, $id);
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        $i = 1;
        $is_contain = true;
        do {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                $is_contain = false;
                return $newSlug;
            }
            $i++;
        } while ($is_contain);
    }
    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Knowledgebase::select('knowledgebase_slug')->where('knowledgebase_slug', 'like', $slug.'%')
        ->where('knowledgebase_id', '<>', $id)
        ->get();
    }
}
