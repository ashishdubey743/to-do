<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;
use Log;

class TaskController extends Controller
{
    //

    public function task_list()
    {
        $tasks = Task::orderBy("id", "asc")->get();
        return view("task_list", compact("tasks"));
    }
    public function add_task(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_name' => [
                    'required',
                    'string',
                    'max:50',
                    'min:4',
                    'unique:tasks,task_name',
                    function ($attribute, $value, $fail) {
                        if (preg_match('/^\d+$/', $value)) {
                            $fail('Task name cannot contain numbers only.');
                        }
                        if(preg_match('/[^a-zA-Z0-9\s]/', $value)) {
                            $fail('Task name cannot contain special Characters.');
                        }
                    },
                ],
            ], [
                "task_name.required" => "Task name is required.",
                "task_name.string" => "Task name must be a string.",
                "task_name.max" => "Task name must not be greater than 255 characters.",
                "task_name.min" => "Task name must be atleast 3 characters long.",
                "task_name.unique" => "Task already exist.",
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $validated = $validator->validated();
            try {
                $task = Task::create([
                    "task_name" => $validated["task_name"],
                    "task_status" => "Pending",
                ]);
            } catch (\Exception $e) {
                Log::error("Error during task create : " . $e->getMessage());
                throw new \Exception("Task cannot created!", 500);
            }
            return response()->json([
                "code" => 200,
                "data" => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            ]);
        }

    }
    public function update_task(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "task_id" => "required",
            ], [
                "task_id.required" => "Task id is required",
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $validated = $validator->validated();
            try {
                Task::where("id", $validated["task_id"])->update([
                    "task_status" => "Done",
                ]);
            } catch (\Exception $e) {
                Log::error("Error during task update : " . $e->getMessage());
                throw new \Exception("Task cannot complete!", 500);
            }

            return response()->json([
                "code" => 200,
                "message" => "Task done"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            ]);

        }
    }

    public function delete_task(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "task_id" => "required",
            ], [
                "task_id.required" => "Task id is required",
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $validated = $validator->validated();
            try {
                Task::where("id", $validated["task_id"])->delete();
            } catch (\Exception $e) {
                Log::error("Error during task delete : " . $e->getMessage());
                throw new \Exception("Task cannot delete!", 500);
            }

            return response()->json([
                "code" => 200,
                "message" => "Task deleted"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            ]);
        }
    }
}
