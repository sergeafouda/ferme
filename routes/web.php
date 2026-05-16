<?php

use App\Http\Controllers\PreorderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PreorderController::class, 'index'])->name('preorder.index');
Route::post('/preorder/store', [PreorderController::class, 'store'])->name('preorder.store');
Route::post('/preorder/paydunya', [PreorderController::class, 'paydunya'])->name('preorder.paydunya');
Route::get('/preorder/success', [PreorderController::class, 'success'])->name('preorder.success');
Route::get('/preorder/thank-you', [PreorderController::class, 'thankYou'])->name('preorder.thankYou');
Route::post('/preorder/ipn', [PreorderController::class, 'ipn'])->name('preorder.ipn');
