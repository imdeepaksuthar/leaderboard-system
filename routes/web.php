<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderboardController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/leaderboard', [LeaderboardController::class, 'index']);
Route::post('/leaderboard/recalculate', [LeaderboardController::class, 'recalculate']);