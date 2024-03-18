<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $todos = Todo::whereNull('deleted_at')->get();
        
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => true,
                'message' => 'Todos Fetched successfully.',
            ],
            'data' => [
                'todos' => $todos,
            ],
        ]);
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => true,
                'message' => 'Todo created successfully.',
            ],
            'data' => [
                'todo' => $todo,
            ],
        ]);

    }

    public function show($id)
    {
        $todo = Todo::find($id);
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => true,
                'message' => 'Todo Get successfully.',
            ],
            'data' => [
                'todo' => $todo,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json([
                'status' => false,
                'message' => 'Todo not found',
            ], 404);
        }

        $todo->fill($request->only(['title', 'description', 'status']));
        $todo->save();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => true,
                'message' => 'Todo updated successfully.',
            ],
            'data' => [
                'todo' => $todo,
            ],
        ]);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);
        $todo->delete();

        
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => true,
                'message' => 'Todo deleted successfully.',
            ],
            'data' => [
                'todo' => $todo,
            ],
        ]);
        
    }
}