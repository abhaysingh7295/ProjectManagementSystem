<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\V1\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Users & User 
Route::group(['prefix' => '/v1/user'], function () {
    Route::post('/login', 'API\V1\UserController@login');
    Route::get('/logout', 'API\V1\UserController@logout');
    Route::post('/forgotPassword', 'API\V1\UserController@forgotPassword');
    Route::post('/resetPassword', 'API\V1\UserController@resetPassword');
    Route::get('/getProfile/{id}', 'API\V1\UserController@userProfile');
    Route::post('/updatePassword/{id}', 'API\V1\UserController@updatePassword');
    Route::post('/updateUserInfo/{id}', 'API\V1\UserController@updateUserInfo');
    Route::post('/updatePicture', 'API\V1\UserController@updatePicture');
    Route::get('/getPicture/{id}', 'API\V1\UserController@getPicture');
});


//PROJECTS & PROJECT
Route::group(['prefix' => '/v1/project'], function () {
    Route::get("/userwiseprojectlist", "API\V1\ProjectController@index");
    Route::get("/getAllProjectUserWise/{id}", "API\V1\ProjectController@getAllProjectUserWise");
    Route::get("/getAllprojects", "API\V1\ProjectController@getAllprojects");
    Route::get("/getprojectDetails/{id}", "API\V1\ProjectController@getprojectDetails");
    Route::get("/getUserTask/{id}", "API\V1\ProjectController@getUserTask");
    Route::get("/getProjectFeed/{id}", "API\V1\TaskController@getProjectFeed");
});

//SPRINTS & SPRINT
Route::group(['prefix' => '/v1/sprint'], function () {
    Route::get("/list/{id}", "API\V1\SprintController@index");
    Route::post("/create", "API\V1\SprintController@create");
    Route::get("/show/{id}", "API\V1\SprintController@show");
    Route::post("/update/{id}", "API\V1\SprintController@update");
    Route::get("/delete/{id}", "API\V1\SprintController@delete");
    Route::post("/changeStatus/{id}", "API\V1\SprintController@changeStatus");
    Route::post("/assignSprintToTask", "API\V1\SprintController@assignSprintToTask");
    Route::post("/assignSprintToUser", "API\V1\SprintController@assignSprintToUser");
});

//Tasks & Task
Route::group(['prefix' => '/v1/task'], function () {
    Route::get("/details/{id}", "API\V1\TaskController@taskDetail");
    Route::get("/list", "API\V1\TaskController@list");
    Route::get("/comments/{id}", "API\V1\TaskController@commentList");
    Route::post("/createcomment", "API\V1\TaskController@createComment");
    Route::get("/destroyComment/{id}", "API\V1\TaskController@destroyComment");
    Route::post("/createTask","API\V1\TaskController@createTask");
    Route::get("/getTaskStatus","API\V1\TaskController@getTaskStatus");
    Route::get("/getProjectMilestone/{id}","API\V1\TaskController@getProjectMilestone");
    Route::get("/taskPriority","API\V1\TaskController@taskPriority");
    Route::post("/updateTask/{id}","API\V1\TaskController@updateTask");
    Route::post("/updatetaskstatusflag","API\V1\TaskController@updatetaskflag");
});
Route::group(['prefix' => '/v1/kb'], function () {
    Route::get("/details/{id}", "API\V1\TaskController@getkbByID");
    Route::get("/list", "API\V1\TaskController@getkblist");
    Route::post("/update/{id}", "API\V1\TaskController@updatekb");
    Route::post("/create", "API\V1\TaskController@createkb");
    Route::get("/delete/{id}", "API\V1\TaskController@deletekb");
    Route::get("/getKbCategory","API\V1\TaskController@getKbCategory");
    
});
Route::get('/v1/getStatusFlag',"API\V1\SprintController@getStatus");
