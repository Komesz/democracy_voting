<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollController;

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

Route::get('/polls', [PollController::class, 'index']); // List all polls
Route::post('/polls', [PollController::class, 'store']); // Create a new poll
Route::get('/polls/{id}', [PollController::class, 'show']); // Get poll details
Route::post('/polls/{id}/vote', [PollController::class, 'vote']); // Submit a vote
Route::post('/polls/{id}/toggle', [PollController::class, 'toggleactivation']);
