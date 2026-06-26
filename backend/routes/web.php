<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['message' => 'NEXADOC API - Laravel ' . app()->version()];
});
