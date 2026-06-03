<?php

namespace App\Services\Media;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function upload(
        UploadedFile $file,
        int $userId,
        string $folder = 'uploads' // ✅ default
    ): MediaFile {
        $path = $file->store($folder, 'public'); // 🔥 dynamic folder

        return MediaFile::create([
            'user_id' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    public function delete(int $mediaId): bool
    {
        $media = MediaFile::find($mediaId);

        if (!$media) return false;

        // 🔥 delete file from storage
        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
            Log::info('File Deleted');
        }

        // 🔥 delete DB record
        return $media->delete();
    }
}
