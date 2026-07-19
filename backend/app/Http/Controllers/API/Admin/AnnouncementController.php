<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\AnnouncementDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\AnnouncementUser;
use App\Services\Announcement\AnnouncementService;
use App\Services\Media\MediaService;
use App\Services\Notification\FcmNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AnnouncementService $service,

        private MediaService $mediaService
    ) {}

    public function store(AnnouncementRequest $request)
    {

        $data = $request->validated();
        if ($request->hasFile('thumbnail')) {
            $media = $this->mediaService->upload($request->file('thumbnail'), auth()->id(), 'announcements');
            $data['image'] = $media->id;
        }

        $dto = AnnouncementDTO::fromArray($data);

        $announcement = $this->service->create($dto);

        // Fetch the users this announcement targets
        $targetUserIds = AnnouncementUser::where('announcement_id', $announcement->id)
            ->pluck('user_id')
            ->all();

        if (!empty($targetUserIds)) {
            (new FcmNotificationService())->sendAnnouncement($targetUserIds, [
                'title'           => $announcement->title,
                'body'            => \Illuminate\Support\Str::limit(strip_tags($announcement->content ?? ''), 100),
                'announcement_id' => $announcement->id,
                'course_name'     => '',
            ]);
        }

        return response()->json(['data' => $announcement]);

        // return response()->json([
        //     'data' => $this->service->create($dto)
        // ]);
    }

    public function index(Request $request)
    {
        $filter = $request->all();
        return AnnouncementResource::collection(
            $this->service->forAll($filter)
        );
    }

    public function show(int $id)
    {

        return new AnnouncementResource(
            $this->service->findWithRelations($id, ['users'])
        );
    }

    public function update(AnnouncementRequest $request, int $id)
    {
        $data = $request->validated();

        Log::info( $data );

        if ($request->hasFile('thumbnail')) {
            $current = $this->service->find($id);
            // 1. delete old media
            if ($current->thumbnail && is_int($current->thumbnail)) {
                $this->mediaService->delete($current->thumbnail);
            }
            $media = $this->mediaService->upload($request->file('thumbnail'), auth()->id(), 'announcements');
            $data['thumbnail'] = $media->id;
        }


        dd($data );
        $dto = AnnouncementDTO::fromArray($request->validated());

        return response()->json([
            'data' => $this->service->update($id, $dto)
        ]);
    }

    public function destroy(int $id)
    {
        if (!$this->service->exists($id)) {
            return $this->errorResponse('Course not found', null, 404);
        }

        $current = $this->service->find($id);
        if ($current->thumbnail && is_int($current->thumbnail)) {
            $this->mediaService->delete($current->thumbnail);
        }

        $this->service->delete($id);

        return response()->json(['message' => 'Deleted']);
    }



    /**
     * GET /api/announcements
     * Returns all active, non-expired announcements for the authenticated user,
     * along with that user's read/click status from announcement_user pivot.
     */
    public function appIndex(Request $request)
    {
        $userId = $request->user()->id;
        $now    = now();

        $announcements = Announcement::query()
            ->where('status', 'published')
            // ->where(function ($q) use ($now) {
            //     $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            // })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->where(function ($q) use ($userId) {
                // target_type: 'all' means everyone; 'user' means only via pivot
                $q
                    // ->orWhere('target_type', 'all_users')
                    ->WhereHas('users', fn($u) => $u->where('user_id', $userId));
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->get();



        // Fetch this user's pivot rows in one query
        $pivotMap = AnnouncementUser::where('user_id', $userId)
            ->whereIn('announcement_id', $announcements->pluck('id'))
            ->get()
            ->keyBy('announcement_id');

        $data = $announcements->map(function (Announcement $a) use ($pivotMap, $userId) {
            $pivot = $pivotMap->get($a->id);

            return [
                'id'         => $a->id,
                'title'      => $a->title,
                'content'    => $a->content,
                'thumbnail'      =>
                // $a->image ? asset('storage/' . $a->image) : null,
                $a->thumbnailMedia
                    ? asset('storage/' . $a->thumbnailMedia->file_path)
                    : null,
                'priority'   => $a->priority,
                'is_pinned'  => (bool) $a->is_pinned,
                'position'   => $a->position,
                'start_date' => $a->start_date?->toDateString(),
                'end_date'   => $a->end_date?->toDateString(),
                'created_at' => $a->created_at->toIso8601String(),
                // User-specific fields
                'is_read'    => $pivot ? !is_null($pivot->read_at) : false,
                'is_clicked' => $pivot ? !is_null($pivot->clicked_at) : false,
                'read_at'    => $pivot?->read_at?->toIso8601String(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/announcements/{id}
     * Returns a single announcement and marks it as read.
     */
    public function AppShow(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $now    = now();

        // Log::info("message");
        // Log::info($userId);

        $announcement = Announcement::where('status', 'published')
            // ->where(function ($q) use ($now) {
            //     $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            // })
            // ->where(function ($q) use ($now) {
            //     $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            // })
            // ->where(function ($q) use ($userId) {
            //     $q
            //     // ->where('target_type', 'all_users')
            //         ->WhereHas('users', fn($u) => $u->where('user_id', $userId));
            // })
            ->findOrFail($id);

        // Log::info($announcement);

        // Upsert pivot: mark delivered + read
        $pivot = AnnouncementUser::firstOrNew([
            'announcement_id' => $announcement->id,
            'user_id'         => $userId,
        ]);

        if (is_null($pivot->delivered_at)) {
            $pivot->delivered_at = now();
        }
        if (is_null($pivot->read_at)) {
            $pivot->read_at = now();
            $pivot->status  = 'read';
        }
        $pivot->save();

        return response()->json([
            'data' => [
                'id'         => $announcement->id,
                'title'      => $announcement->title,
                'content'    => $announcement->content,
                'thumbnail'      =>
                // $announcement->image ? asset('storage/' . $announcement->image) : null,
                $announcement->thumbnailMedia
                    ? asset('storage/' . $announcement->thumbnailMedia->file_path)
                    : null,
                'priority'   => $announcement->priority,
                'is_pinned'  => (bool) $announcement->is_pinned,
                'position'   => $announcement->position,
                'start_date' => $announcement->start_date?->toDateString(),
                'end_date'   => $announcement->end_date?->toDateString(),
                'created_at' => $announcement->created_at->toIso8601String(),
                'is_read'    => true,
                'read_at'    => $pivot->read_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * POST /api/announcements/{id}/click
     * Marks the announcement as clicked (e.g. user tapped Call or WhatsApp).
     */
    public function markClicked(Request $request, int $id)
    {
        $userId = $request->user()->id;

        $pivot = AnnouncementUser::firstOrNew([
            'announcement_id' => $id,
            'user_id'         => $userId,
        ]);

        if (is_null($pivot->clicked_at)) {
            $pivot->clicked_at = now();
            $pivot->status     = 'clicked';
        }
        $pivot->save();

        return response()->json(['success' => true]);
    }
}
