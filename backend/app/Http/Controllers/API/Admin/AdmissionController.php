<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\AdmissionDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdmissionResource;
use App\Http\Requests\AdmissionRequest;
use App\Models\Admission;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Services\Admission\AdmissionService;
use App\Services\Notification\FcmNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    use ApiResponse;
    public function __construct(
        private AdmissionService $admissionService
    ) {}

    /**
     * Admission List
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $filters = $request->all();

            $admissions = $this->admissionService
                ->all($filters);

            return $this->paginatedResponse(
                AdmissionResource::collection($admissions),
                'Admissions retrieved successfully'
            );
        } catch (\Exception $e) {

            return $this->errorResponse(
                'Failed to retrieve admissions',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Create Admission
     */
    public function store(
        AdmissionRequest $request
    ): JsonResponse {

        try {

            $dto = AdmissionDTO::fromArray(
                $request->validated()
            );

            $admission = $this->admissionService
                ->create($dto);


            $admission->loadMissing(['student', 'course']);
            if ($admission->student_id && $admission->course) {
                (new FcmNotificationService())->sendAdmissionNotification(
                    $admission->student_id,
                    [
                        'course_name'  => $admission->course->name ?? 'your course',
                        'status'       => $admission->status ?? 'pending',
                        'admission_id' => $admission->id,
                    ]
                );
            }


            $conversation = Conversation::query()
                ->where('type', 'group')
                ->where('module_id', $request->course_id)
                ->first();


            if ($conversation) {
                ConversationParticipant::firstOrCreate([
                    'conversation_id' => $conversation->id,
                    'user_id' =>  $request->student_id
                ]);
            }


            return $this->successResponse(
                AdmissionResource::make($admission),
                'Admission created successfully',
                201
            );
        } catch (\Exception $e) {

            return $this->errorResponse(
                'Failed to create admission',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * View Admission
     */
    public function show(
        int $id
    ): JsonResponse {

        try {

            $admission = $this->admissionService
                ->findWithRelations(
                    $id,
                    [
                        'student',
                        'course',
                        'teacher'
                    ]
                );

            if (!$admission) {

                return $this->errorResponse(
                    'Admission not found',
                    null,
                    404
                );
            }

            return $this->successResponse(
                AdmissionResource::make($admission),
                'Admission retrieved successfully'
            );
        } catch (\Exception $e) {

            return $this->errorResponse(
                'Failed to retrieve admission',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Update Admission
     */
    public function update(
        AdmissionRequest $request,
        int $id
    ): JsonResponse {

        try {

            if (
                !$this->admissionService->find($id)
            ) {

                return $this->errorResponse(
                    'Admission not found',
                    null,
                    404
                );
            }

            // $current =
            //     $this->admissionService
            //     ->find($id);

            // $data = array_merge(
            //     $current->toArray(),
            //     $request->validated()
            // );

            // $dto = AdmissionDTO::fromArray(
            //     $data
            // );

            // $this->admissionService
            //     ->update(
            //         $id,
            //         $dto
            //     );

            // $updated =
            //     $this->admissionService
            //     ->findWithRelations(
            //         $id,
            //         [
            //             'student',
            //             'course',
            //             'teacher'
            //         ]
            //     );

            $current = $this->admissionService->find($id);
            $oldStatus = $current->status;

            $data = array_merge($current->toArray(), $request->validated());
            $dto  = AdmissionDTO::fromArray($data);
            $this->admissionService->update($id, $dto);

            $updated = $this->admissionService->findWithRelations($id, ['student', 'course', 'teacher']);

            // Notify only when status changes
            if (isset($request->validated()['status']) && $request->validated()['status'] !== $oldStatus) {
                if ($updated->student_id && $updated->course) {
                    (new FcmNotificationService())->sendAdmissionNotification(
                        $updated->student_id,
                        [
                            'course_name'  => $updated->course->name ?? 'your course',
                            'status'       => $updated->status,
                            'admission_id' => $updated->id,
                        ]
                    );
                }
            }

            return $this->successResponse(
                AdmissionResource::make($updated),
                'Admission updated successfully'
            );
        } catch (\Exception $e) {

            return $this->errorResponse(
                'Failed to update admission',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete Admission
     */
    public function destroy(
        int $id
    ): JsonResponse {

        try {

            if (
                !$this->admissionService
                    ->find($id)
            ) {

                return $this->errorResponse(
                    'Admission not found',
                    null,
                    404
                );
            }

            $current = Admission::with(['student', 'course', 'teacher'])->find($id);

            $this->admissionService
                ->delete($id);

            $conv = Conversation::where('module_id', $current->course_id)->first();
            $convId = $conv->id;
            ConversationParticipant::where('conversation_id', $convId)->where('user_id', $current->id)->delete();

            return $this->successResponse(
                null,
                'Admission deleted successfully'
            );
        } catch (\Exception $e) {

            return $this->errorResponse(
                'Failed to delete admission',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Admission Payments
     */
    public function payments(
        int $id
    ): JsonResponse {

        try {

            $payments =
                $this->admissionService
                ->payments($id);

            return $this->successResponse(
                $payments,
                'Admission payments retrieved successfully'
            );
        } catch (\Exception $e) {

            return $this->errorResponse(
                'Failed to retrieve payments',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    public function admissionStatus(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'status' => [
                    'required',
                    'string',
                    'in:active,pending,completed,cancelled,expired',
                ],
            ]);

            $current = Admission::with([
                'student',
                'course',
                'teacher',
            ])->find($id);

            if (!$current) {
                return $this->errorResponse(
                    'Admission not found',
                    [],
                    404
                );
            }

            $oldStatus = $current->status;
            $newStatus = $validated['status'];

            // Update admission status
            $current->status = $newStatus;
            $current->save();

            /*
        |--------------------------------------------------------------------------
        | Update Conversation Participant
        |--------------------------------------------------------------------------
        */

            $conv = Conversation::where(
                'module_id',
                $current->course_id
            )->first();

            if ($conv) {
                $participantStatus = in_array(
                    $newStatus,
                    ['expired', 'cancelled']
                )
                    ? 'suspended'
                    : 'active';

                ConversationParticipant::where(
                    'conversation_id',
                    $conv->id
                )
                    ->where(
                        'user_id',
                        $current->student_id
                    )
                    ->update([
                        'status' => $participantStatus,
                    ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Send Notification Only When Status Changes
        |--------------------------------------------------------------------------
        */

            if (
                $oldStatus !== $newStatus &&
                $current->student_id &&
                $current->course
            ) {
                (new FcmNotificationService())
                    ->sendAdmissionNotification(
                        $current->student_id,
                        [
                            'course_name' => $current->course->name
                                ?? 'your course',

                            'status' => $current->status,

                            'admission_id' => $current->id,
                        ]
                    );
            }

            return $this->successResponse(
                AdmissionResource::make($current->fresh([
                    'student',
                    'course',
                    'teacher',
                ])),
                'Admission updated successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update admission',
                [
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }
}
