<?php

namespace App\Chat\Services;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    private const MAX_IMAGE_SIZE = 10 * 1024 * 1024;  // 10MB
    private const MAX_VIDEO_SIZE = 50 * 1024 * 1024;  // 50MB
    private const MAX_AUDIO_SIZE = 20 * 1024 * 1024;  // 20MB
    private const MAX_FILE_SIZE  = 50 * 1024 * 1024;  // 50MB

    private const ALLOWED_IMAGE = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_VIDEO = ['mp4', 'mov', 'avi', 'webm', 'mkv'];
    private const ALLOWED_AUDIO = ['mp3', 'wav', 'ogg', 'webm', 'm4a', 'aac'];
    private const ALLOWED_VOICE = ['webm', 'ogg', 'wav', 'mp4'];

    public function upload(UploadedFile $file, int $userId, int $conversationId, string $type): array
    {
        $this->validateFile($file, $type);

        $folder    = "chat/{$conversationId}/{$type}";
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path      = $file->storeAs($folder, $filename, 'public');
        $url       = Storage::disk('public')->url($path);

        $media =    MediaFile::create([
            'user_id' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $meta = [
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'extension'     => $file->getClientOriginalExtension(),
        ];
        // Add duration for audio/video if possible
        if (in_array($type, ['audio', 'voice', 'video'])) {
            $meta['path'] = $path; // For processing duration later
        }

        return ['url' => $url, 'meta' => $meta, 'media' => $media];


        // // Add duration for audio/video if possible
        // if (in_array($type, ['audio', 'voice', 'video'])) {
        //     $meta['path'] = $path; // For processing duration later
        // }

        // return ['url' => $url, 'meta' => $meta, 'path' => $path];
    }

    private function validateFile(UploadedFile $file, string $type): void
    {
        $ext = strtolower($file->getClientOriginalExtension());

        match ($type) {
            'image' => $this->check($file, self::ALLOWED_IMAGE, self::MAX_IMAGE_SIZE, $ext),
            'video' => $this->check($file, self::ALLOWED_VIDEO, self::MAX_VIDEO_SIZE, $ext),
            'audio' => $this->check($file, self::ALLOWED_AUDIO, self::MAX_AUDIO_SIZE, $ext),
            'voice' => $this->check($file, self::ALLOWED_VOICE, self::MAX_AUDIO_SIZE, $ext),
            'file'  => abort_if($file->getSize() > self::MAX_FILE_SIZE, 422, 'File too large (max 50MB)'),
            default => null,
        };
    }

    private function check(UploadedFile $file, array $allowed, int $maxSize, string $ext): void
    {
        abort_if(!in_array($ext, $allowed), 422, "File type .{$ext} not allowed.");
        abort_if($file->getSize() > $maxSize, 422, 'File size exceeds limit.');
    }

    public function delete(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
