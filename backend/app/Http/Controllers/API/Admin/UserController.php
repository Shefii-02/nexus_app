<?php

namespace App\Http\Controllers\API\Admin;

use App\Chat\Events\UserOnlineStatus;
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

    public function byRole(Request $request)
    {
        $validated = $request->validate(['role' => 'required|in:admin,staff,teacher,student']);
        $meId = $request->user()->id;

        $users = User::where('acc_type', $validated['role'])
            ->where('id', '!=', $meId)
            ->get(['id', 'name', 'email', 'phone', 'avatar', 'acc']);

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
        // return response()->json(['data' => $users]);
    }

    public function RegisterDevice(Request $request)
    {
        $user = $request->user();


        UserPlatform::updateOrCreate(
            [
                // 'device_id' => $request->device_id,
                'user_id' => $user->id
            ],
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

        broadcast(
            new UserOnlineStatus($user->id, true, now())
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
                // 'device_id' => $request->device_id,
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

    public function search(Request $request)
    {
        $q = isset($request->q) ? $request->string('q')->trim() : $request->string('search')->trim();


        if ($q->length() < 2) {
            return response()->json(['data' => []]);
        }

        $users = \App\Models\User::query()
            ->where(function ($query) use ($q) {
                $query->where('name',   'like', "%{$q}%")
                    ->orWhere('email',  'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->where('id', '!=', $request->user()->id)
            ->select('id', 'name', 'email', 'phone', 'avatar')
            ->limit(20)
            ->get();

        return response()->json(['status' => true, 'data' => $users]);
    }



    public function teacherSearch(Request $request): JsonResponse
    {
        $filters = request()->query('filters', []);
        $users = User::get();
        $q = $request->q;

        $users = User::query()
            ->where('acc_type', 'teacher')
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
    public function staffSearch(Request $request): JsonResponse
    {
        $filters = request()->query('filters', []);
        $users = User::get();
        $q = $request->q;

        $users = User::query()
            ->where('acc_type', 'staff')
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
}
