<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('task_list');
// });

Route::get('/', [TaskController::class,'task_list'])->name('task_list');
Route::post('/add_task', [TaskController::class, 'add_task'])->name('add_task');
Route::put('/update_task', [TaskController::class, 'update_task'])->name('update_task');
Route::delete('/delete_task', [TaskController::class, 'delete_task'])->name('delete_task');

