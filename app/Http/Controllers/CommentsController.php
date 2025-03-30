<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;
use App\Models\Task;
use Auth;

class CommentsController extends Controller
{
    /**
     * Get all comments for a specific task.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\Response
     */
    public function index($taskId)
    {

        $comments = Comments::where('task_id', $taskId)
            ->with('user:id,name') 
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($comments);
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
    public function store(Request $request, $taskId)
    {
        
        $validated = $request->validate([
            'content' => 'required|string'
        ]);
        
        $comment = new Comments();
        $comment->content = $validated['content'];
        $comment->task_id = $taskId;
        $comment->user_id = Auth::id();
        $comment->save();
        
        $comment->load('user:id,name');
        
        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comments $comments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comments $comments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comments $comments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comments $comments)
    {
        //
    }
}
