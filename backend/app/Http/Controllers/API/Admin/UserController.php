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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $userData = User::find($user->id);

        return $this->successResponse(UserResource::make($userData), 'User profile retrieved successfully');
    }



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
            ->get();

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



    public function destroyAccount(Request $request)
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $this->deleteUserData($user);

            $userId = $user->id;
            $user->forceDelete(); // ← permanent delete, bypasses SoftDeletes

            DB::commit();

            Log::info("Account permanently deleted", ['user_id' => $userId]);

            return response()->json(['message' => 'Account deleted successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Account deletion failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to delete account',
            ], 500);
        }
    }

    private function deleteUserData(User $user): void
    {
        DB::table('activity_logs')->where('user_id', $user->id)->delete();
        DB::table('announcement_user')->where('user_id', $user->id)->delete();
        DB::table('app_notifications')->where('user_id', $user->id)->delete();
        DB::table('calls')->where('teacher_id', $user->id)->delete();
        DB::table('calls')->where('student_id', $user->id)->delete();
        DB::table('call_recipients')->where('student_id', $user->id)->delete();
        DB::table('conversation_participants')->where('user_id', $user->id)->delete();

        // ── Messages: must clean up dependents BEFORE deleting the messages ──
        $messageIds = DB::table('messages')->where('sender_id', $user->id)->pluck('id');

        if ($messageIds->isNotEmpty()) {
            DB::table('message_reactions')->whereIn('message_id', $messageIds)->delete();
            DB::table('message_reads')->whereIn('message_id', $messageIds)->delete();

            // Polls attached to this user's messages
            $pollIds = DB::table('polls')->whereIn('message_id', $messageIds)->pluck('id');
            if ($pollIds->isNotEmpty()) {
                DB::table('poll_votes')->whereIn('poll_id', $pollIds)->delete();
                DB::table('poll_options')->whereIn('poll_id', $pollIds)->delete();
                DB::table('polls')->whereIn('id', $pollIds)->delete();
            }

            // Other messages replying to this user's messages — don't cascade-delete
            // those messages, just detach the reply link.
            DB::table('messages')->whereIn('reply_to', $messageIds)->update(['reply_to' => null]);
        }

        DB::table('messages')->where('sender_id', $user->id)->delete();
        DB::table('message_reactions')->where('user_id', $user->id)->delete(); // reactions THIS user made elsewhere
        DB::table('message_reads')->where('user_id', $user->id)->delete();
        DB::table('poll_votes')->where('user_id', $user->id)->delete(); // votes THIS user cast elsewhere

        DB::table('refresh_tokens')->where('user_id', $user->id)->delete();
        DB::table('staff')->where('user_id', $user->id)->delete();
        DB::table('staff_payments')->where('staff_id', $user->id)->delete();
        DB::table('students')->where('user_id', $user->id)->delete();
        DB::table('teachers')->where('user_id', $user->id)->delete();
        DB::table('teachers_courses')->where('teacher_id', $user->id)->delete();
        DB::table('teacher_payments')->where('teacher_id', $user->id)->delete();
        DB::table('teacher_payment_items')->where('teacher_id', $user->id)->delete();
        DB::table('user_app_permissions')->where('user_id', $user->id)->delete();
        DB::table('user_devices')->where('user_id', $user->id)->delete();
        DB::table('user_platforms')->where('user_id', $user->id)->delete();

        // ── Courses / conversations this user "owns" — DECIDE how to handle ──
        // (see explanation above — reassign, block, or cascade; nulling shown
        // here only works if these columns are nullable in your schema)
        DB::table('courses')->where('teacher_id', $user->id)->update(['teacher_id' => null]);
        DB::table('conversations')->where('created_by', $user->id)->update(['created_by' => 1]);

        // ── Media files: delete storage files AND their DB rows ──────────────
        $mediaFiles = DB::table('media_files')->where('user_id', $user->id)->get();
        foreach ($mediaFiles as $mediaFile) {
            if ($mediaFile->file_path && Storage::exists($mediaFile->file_path)) {
                Storage::delete($mediaFile->file_path);
            }
        }
        DB::table('media_files')->where('user_id', $user->id)->delete();
    }
}
