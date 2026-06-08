<?php

namespace App\Http\Controllers\API\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ApiResponse;

    public function allUsers(Request $request): JsonResponse
    {
        $filters = request()->query('filters', []);
        $users = User::get();
        $q = $request->q;

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    public function RegisterDevice(Request $request)
    {
        $user = $request->user();


        UserPlatform::updateOrCreate(
            ['device_id' => $request->device_id, 'user_id' => $user->id],
            [
                'fcm_token' => $request->fcm_token,
                'voip_token' => $request->voip_token,
                'platform' => $request->platform,
                'ip_address' => $request->ip(),
                'device_info' => $request->device_info,
                'app_version' => $request->app_version,
                'last_active_at' => $request->last_active_at,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'district' => $request->district,
                'city'    => $request->city,
                'status' => 'active'
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Device Registered'
        ]);
    }


    public function visitorStore(Request $request)
    {
        $currentUser = $request->user();

        UserPlatform::updateOrCreate(
            [
                'device_id' => $request->device_id,
                'user_id'   => $currentUser->id
            ],
            [
                'last_active_at' => now(),
                'status'         => 'active'
            ]
        );

        $user = User::find($currentUser->id);

        $user->last_activation = now();

        if (!$user->last_login) {
            $user->last_login = now();
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Time Updated'
        ]);
    }
}
