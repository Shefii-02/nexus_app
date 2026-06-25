<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MyCourseResource;
use App\Models\Course;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->acc_type == 'teacher') {
            $myCourses = Course::with(['teachers', 'classes'])
                ->whereHas('teachers', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })->get();
        } else {
            $myCourses = Course::with(['students', 'classes'])
                ->whereHas('students', function ($q) use ($user) {
                    $q->where('student_id', $user->id); // was teacher_id — bug
                })->get();
        }

        return response()->json([
            'success' => true,
            'data'    => MyCourseResource::collection($myCourses),
        ]);
    }
}
