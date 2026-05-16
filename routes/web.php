<?php

use App\Http\Controllers\PreorderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [PreorderController::class, 'index'])->name('preorder.index');
Route::post('/preorder/store', [PreorderController::class, 'store'])->name('preorder.store');
