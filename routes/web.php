<?php

use App\Http\Controllers\Auth\GoogleCallbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/google/callback', [GoogleCallbackController::class, 'show'])->name('auth.google.callback');
Route::post('/auth/google/callback', [GoogleCallbackController::class, 'handle'])->name('auth.google.callback.post');

Route::get('/home', function () {
    return view('home');
})->name('home');
