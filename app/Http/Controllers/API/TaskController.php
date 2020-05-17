<?php

namespace App\Http\Controllers\API;

use App\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        return $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user', Auth::user()->id)->get(['date_added', 'date_completed','id', 'status', 'task', 'user' ]);
        return $this->sendResponse($tasks, 'Tasks list');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'task' => 'required|min:5'
        ];

        $validator = Validator::make( $request->all(), $rules);

        if($validator->fails())
        {
            return $this->sendError('Valildation error', $validator->errors());
        }

        $task = [
            'task' => $request->task,
            'user' => Auth::user()->id,
            'status' => 0,
            'date_added' => date('Y-m-d H:i:s'),
            'date_completed' => date('Y-m-d H:i:s')
        ];

        $task = Task::create($task);

        return $this->sendResponse($task, 'Task created successfully');
    }


    /**
     * Show the form for show the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::find($id);

        if(is_null($task)){
            return $this->sendError('Task not Found');
        }

        return $this->sendResponse($task, 'Task found');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $rules = [
            'task' => 'required|min:5',
        ];

        // 'status' => 'required|numeric',
        // 'date_added' => 'required',
        // 'date_completed' => 'required'

        $validator = Validator::make( $request->all(), $rules);

        if($validator->fails())
        {
            return $this->sendError('Valildation error', $validator->errors());
        }

        $update = $task->update($request->all());

        return $this->sendResponse($task, "Task updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }

    /* ------------------------------------------------------------------------------ */

    public function sendResponse($result, $messageSuccess)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $messageSuccess,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 400)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
