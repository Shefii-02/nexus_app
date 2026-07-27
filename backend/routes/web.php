<?php

use App\Http\Controllers\API\PushNotificationController;
use App\Http\Controllers\Web\ConversationRepairController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/privacy', function () {
    return view('privacy');
});


Route::get('/delete-account', function () {
    return view('welcome');
});



