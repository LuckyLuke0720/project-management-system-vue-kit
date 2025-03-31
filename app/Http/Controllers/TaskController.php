<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function updateTaskOrder(Request $request, Project $project){

        $taskOrders = $request->input('task_order');

        foreach ($taskOrders as $taskOrderData){
            Task::where('id', $taskOrderData['id'])->update(['order' => $taskOrderData['order']]);
        }

        return response()->json(['message'=> 'Task order updated!']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $taskId)
    {
        
        $task = Task::findOrFail($taskId);

        // Validate request
        $validated = $request->validate([
            'status' => 'required|in:To Do,In Progress,Under Review,Completed',
        ]);
        
        $task->update($validated);
        
        return response()->json($task->load('assignee'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
