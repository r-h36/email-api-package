<?php

use Illuminate\Support\Facades\Route;
use Rh36\EmailApiPackage\Http\Controllers\Api\EmailTemplateController;
use Rh36\EmailApiPackage\Http\Controllers\Api\EmailLogController;


Route::get('/templates', [EmailTemplateController::class, 'index'])->name('templates.index');
Route::get('/templates/{template}', [EmailTemplateController::class, 'show'])->name('templates.show');
Route::post('/templates', [EmailTemplateController::class, 'store'])->name('templates.store');
Route::put('/templates/{template}', [EmailTemplateController::class, 'update'])->name('templates.update');
Route::delete('/templates/{template}', [EmailTemplateController::class, 'destroy'])->name('templates.destroy');

Route::get('/emails', [EmailLogController::class, 'index'])->name('emails.index');
Route::get('/emails/{post}', [EmailLogController::class, 'show'])->name('emails.show');
Route::post('/emails', [EmailLogController::class, 'store'])->name('emails.store');
