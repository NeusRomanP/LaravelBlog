<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\PostController::class, 'index'])->name('home');
Route::get('/posts/create', [App\Http\Controllers\PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'])->name('post');
Route::get('/posts/{id}', [App\Http\Controllers\PostController::class, 'show'])->name('show');
Route::post('/posts/{id}', [App\Http\Controllers\PostController::class, 'update'])->name('post.update');
Route::get('/posts/edit/{id}', [App\Http\Controllers\PostController::class, 'edit'])->name('posts.edit');
Route::get('/posts/delete/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.delete');
