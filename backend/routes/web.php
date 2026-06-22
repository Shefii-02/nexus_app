<?php

use App\Http\Controllers\API\PushNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/my-nikah-privacy', function () {
    return view('my_nikah_privacy');
});

Route::get('/my-nikah-delete-account', function () {
    return view('my_nikah_privacy');
});


Route::get('/csae-policy', function () {
    return view('my_nikah_csae_policy');
});



  Route::get('/fcm-call/{token}/{platform}', [PushNotificationController::class, 'sendClassNotification']);


  Route::get('/send-notification/{token}', [PushNotificationController::class, 'sendPush']);
  Route::get('/send-class-notification/{token}', [PushNotificationController::class, 'sendClassAlertTest']);

  Route::get('/send-notification-to-user', [PushNotificationController::class, 'sendToUser']);
  Route::get('/send-notification-to-topic', [PushNotificationController::class, 'sendToTopic']);




//   curl -i -X POST \
//   https://graph.facebook.com/v25.0/1179555001904827/messages \
//   -H 'Authorization: Bearer EAAOU8L2kyGkBRt25gdrVfW84ZCMJjXvZAbMZCMLa0X9QZBVB30L5iGde5Gt4nfZBPsH9nEnmw0UsAn2cPKdIz6FnyhvfgdFyptvOLLgLoLyP8AM2yLc3ImmcUzrN0LFkMXIr56JAoO5duFupeIXIzK2j5hRA2yorzfLrHZAZAqREIXK6JMUDEEZBAjvj0jDn6zOI2j62WEoXvVjf8R51RqDWyxkZBxgZB3nRMIdz4pAedvrjfBfwMgLpnkyiWeTul3ZBJWEkjYSd50RTQhOXZAf7IjdLT2cT' \
//   -H 'Content-Type: application/json' \
//   -d '{
//     "messaging_product": "whatsapp",
//     "to": "918086544828",
//     "type": "template",
//     "template": {
//       "name": "auth",
//       "language": { "code": "en" },
//       "components": [
//         {
//           "type": "body",
//           "parameters": [
//             { "type": "text", "text": "123456" }
//           ]
//         },
//         {
//           "type": "button",
//           "sub_type": "url",
//           "index": "0",
//           "parameters": [
//             { "type": "coupon_code", "coupon_code": "123456" }
//           ]
//         }
//       ]
//     }
//   }'





// curl -i -X POST \
//   https://graph.facebook.com/v25.0/1130141250186556/messages \
//   -H 'Authorization: Bearer EAAOU8L2kyGkBRw9aaPixbJb0ikY1e9ofeRARF2WeVDZBVJnZAv6ZAmCiuwY9fZAxeZB4EC1Ns7J6L85EAxWq8RHGn20P37ACVUWA4BSv6MGvV2WOBnIfb8ZChPv0kcZBMhVRE3py4RoYpZAbhgmjlDOeaeEWz5xrVFfsVJjKk7egbHe7SD2hyYkWdFkBWiC9sYfxgKwbBw4mcz8Ru6WOhk0By1NEHH5sZBlWQaIZAtMWL9C9FMam3iFFgpyTMeJov1EIfxbyUBvOudlJVzZAScYZBZA8rnFQC' \
//   -H 'Content-Type: application/json' \
//   -d '{ "messaging_product": "whatsapp", "to": "919495568482", "type": "template", "template": { "name": "welcome_message_sakinah", "language": { "code": "en" } } }'
