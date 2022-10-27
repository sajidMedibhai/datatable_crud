<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::POST('/task-history-list', [HomeController::class, 'task_history_list'])->name('task-history-list');
Route::post('/add-task', [HomeController::class, 'add_task'])->name('add-task');
Route::post('/delete-task', [HomeController::class, 'delete_task'])->name('delete-task');
Route::post('/chnage-task-status', [HomeController::class, 'chnage_task_status'])->name('chnage-task-status');
