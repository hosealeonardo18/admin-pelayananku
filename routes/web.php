<?php

use App\Http\Controllers\DashboardControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardControllers::class, 'index']);
