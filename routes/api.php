<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartController;

Route::get('/parts/search', [PartController::class, 'searchParts']);