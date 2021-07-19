<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Auth;

class TodosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Todo::where('user_id', auth()->id())->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
           'title' => 'required|string',
           'completed' => 'required|boolean' 
        ]);
        $user = Auth::user();
        $todo = new Todo;
        $todo->title = $request->title;
        $todo->completed = $request->completed;
        $todo1 = $user->todos()->save($todo);
        return response()->json($todo1, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        if($todo->user_id != auth()->id()) {
            return response()->json('Unauthorized', 401);
        }
        
        $data = $request->validate([
            'title' => 'required|string',
            'completed' => 'required|boolean' 
        ]);
        
        $todo->update($data);
        return response()->json($todo, 200);
    }

    public function checkAll(Request $request) {
        $data = $request->validate([
            'completed' => 'required|boolean' 
        ]);
        Todo::where('user_id', auth()->id())->update($data);
        return response()->json('Updated', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        if($todo->user_id != auth()->id()) {
            return response()->json('Unauthorized', 401);
        }
        
        $todo->delete();
        return response()->json('Todo Item Deleted', 200);
    }

    public function deleteCompleted(Request $request) {
        
        // [6,9] todo ids we are passing in and want to delete
        // [5,6,9] all of the users todo ids

        $todosToDelete = $request->todos;

        $userTodoIds = auth()->user()->todos->map(function ($todo) {
            return $todo->id;
        });

        $valid = collect($todosToDelete)->every(function ($value, $key) use ($userTodoIds) {
            return $userTodoIds->contains($value);
        });

        if (!$valid) {
            return response()->json('Unauthorized', 401);
        }

        $request->validate([
            'todos' => 'required|array',
        ]);

        Todo::destroy($request->todos);

        return response()->json('Deleted', 200);

    }
}
