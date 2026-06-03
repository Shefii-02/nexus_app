<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\AnnouncementDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Services\Announcement\AnnouncementService;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;

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

        return response()->json([
            'data' => $this->service->create($dto)
        ]);
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
            $this->service->findWithRelations($id, ['users', 'roles', 'batches'])
        );
    }

    public function update(AnnouncementRequest $request, int $id)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $current = $this->service->find($id);
            // 1. delete old media
            if ($current->thumbnail && is_int($current->thumbnail)) {
                $this->mediaService->delete($current->thumbnail);
            }
            $media = $this->mediaService->upload($request->file('thumbnail'), auth()->id(), 'announcements');


            $data['thumbnail'] = $media->id;
        }

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
}
