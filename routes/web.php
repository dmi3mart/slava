<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import', [ImportController::class, 'showImportForm'])->name('import.form');

Route::post('/import', [ImportController::class, 'import'])->name('import.process');

Route::get('/import/progress', [ImportController::class, 'getImportProgress'])->name('import.progress');
