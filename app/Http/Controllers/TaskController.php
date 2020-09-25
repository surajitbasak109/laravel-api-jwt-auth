<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{

    protected $user;


    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();

    }//end __construct()


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = $this->user->tasks()->get(['id', 'title', 'details', 'created_by']);
        return $tasks;

    }//end index()


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }//end create()


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'title'   => 'required',
                'details' => 'required',
            ]
        );

        $task          = new Task();
        $task->title   = $request->title;
        $task->details = $request->details;

        if ($this->user->tasks()->save($task)) {
            return response()->json(
                [
                    'status' => true,
                    'task'   => $task,
                ]
            );
        } else {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, task could not be created.',
                ],
                500
            );
        }

    }//end store()


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {

    }//end show()


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {

    }//end edit()


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Task         $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $this->validate(
            $request,
            [
                'title'   => 'required',
                'details' => 'required',
            ]
        );

        $task->title   = $request->title;
        $task->details = $request->details;

        if ($this->user->tasks()->save($task)) {
            return response()->json(
                [
                    'status' => true,
                    'task'   => $task,
                ]
            );
        } else {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, task could not be updated.',
                ],
                500
            );
        }

    }//end update()


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if ($task->delete()) {
            return response()->json(
                [
                    'status' => true,
                    'task'   => $task,
                ]
            );
        } else {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, the task could not be deleted',
                ]
            );
        }

    }//end destroy()


}//end class
