<?php

use App\Http\Controllers\OrderController;
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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/creport', [OrderController::class, 'index'])->name('c.report');
Route::get('/details/{id}', [OrderController::class, 'show'])->name('view.details');


Route::get('/topsellers', [OrderController::class, 'topsellers'])->name('top.sellers');
