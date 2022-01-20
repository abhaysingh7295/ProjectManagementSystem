<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\ProjectValidation;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Projects\ActivateResponse;
use App\Http\Responses\Projects\ArchiveResponse;
use App\Http\Responses\Projects\ChangeCategoryUpdateResponse;
use App\Http\Responses\Projects\ChangeStatusResponse;
use App\Http\Responses\Projects\CommonResponse;
use App\Http\Responses\Projects\CreateCloneResponse;
use App\Http\Responses\Projects\CreateResponse;
use App\Http\Responses\Projects\DestroyResponse;
use App\Http\Responses\Projects\DetailsResponse;
use App\Http\Responses\Projects\EditResponse;
use App\Http\Responses\Projects\IndexResponse;
use App\Http\Responses\Projects\PrefillProjectResponse;
use App\Http\Responses\Projects\ShowDynamicResponse;
use App\Http\Responses\Projects\ShowResponse;
use App\Http\Responses\Projects\StoreCloneResponse;
use App\Http\Responses\Projects\StoreResponse;
use App\Http\Responses\Projects\UpdateDetailsResponse;
use App\Http\Responses\Projects\UpdateResponse;
use App\Permissions\ProjectPermissions;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CloneProjectRepository;
use App\Repositories\CustomFieldsRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\FileRepository;
use App\Repositories\MilestoneCategoryRepository;
use App\Repositories\MilestoneRepository;
use App\Repositories\ProjectAssignedRepository;
use App\Repositories\ProjectManagerRepository;
use App\Repositories\API\ProjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\TimerRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Project;
class ProjectController extends Controller
{

    /**
     * The project repository instance.
     */
    protected $projectrepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The project permission instance.
     */
    protected $projectpermissions;

    /**
     * The file repository instance.
     */
    protected $filerepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    /**
     * The customrepo repository instance.
     */
    protected $customrepo;

    //contruct
    public function __construct(
        ProjectRepository $projectrepo,
        TagRepository $tagrepo
 
    ) {

        //parent
        parent::__construct();

        //vars
        $this->projectrepo = $projectrepo;
        $this->tagrepo = $tagrepo;

    }

    /**
     * Display a listing of projects
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get team projects
        $users = User::where('id','!=',2)->with(['assignedProjects','assignedTasks'])->get();
        
        $payload = [
            'message'=>'success',
            'user' => $users,
           
        ];
        return response()->json($payload, 200);

        
    }
    public function getAllProjectUserWise($id)
    {
        //get all projects User Wise
        $users = User::where('id',$id)->where('id','!=',2)->with(['assignedProjects','assignedTasks'])->get();
        
        $payload = [
            'message'=>'success',
            'user' => $users,
           
        ];
        return response()->json($payload, 200);  
    }
    public function getAllprojects()
    {
        //get all projects
        $projects = Project::where('project_type','project')->with(['assigned','category','tasks','sprints'])->get();
        
        $payload = [
            'message'=>'success',
            'projects' => $projects,
           
        ];
        return response()->json($payload, 200);   
    }
    public function getProjectDetails($id)
    {
        //get  projects detail
        $project = Project::where('project_id',$id)->where('project_type','project')->with(['managers','assigned','category','tasks','sprints'])->first();
        $payload = [
            'message'=>'success',
            'project' => $project,
        ];
        return response()->json($payload, 200);
    }
    
    public function getUserTask($id)
    {
        //get all projects User Wise
        $users = User::where('id',$id)->where('id','!=',2)->with(['assignedTasks'])->get();
        
        $payload = [
            'message'=>'success',
            'user' => $users,
           
        ];
        return response()->json($payload, 200);  
    }
    
}
