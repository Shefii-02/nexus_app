<?php

use App\Http\Controllers\API\PushNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


  Route::get('/fcm-test/{token}/{platform}', [PushNotificationController::class, 'sendClassNotification']);


  Route::get('/send-notification/{token}', [PushNotificationController::class, 'sendPush']);
  Route::get('/send-class-notification/{token}', [PushNotificationController::class, 'sendClassAlertTest']);

  Route::get('/send-notification-to-user', [PushNotificationController::class, 'sendToUser']);
  Route::get('/send-notification-to-topic', [PushNotificationController::class, 'sendToTopic']);
