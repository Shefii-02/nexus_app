<?php

namespace App\DTOs;

class CourseClassDTO
{
    public function __construct(
        public int $course_id,
        public int $teacher_id,
        public string $title,
        public string $description,
        public string $class_number,
        public string $scheduled_date,
        public string $ended_at,
        public string $started_at,
        public int $duration_minutes = 60,
        public string $room_location = '',
        public string $status = 'scheduled',
        public ?string $class_link = null,
        public ?string $record_link = null,
        public ?string $source = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            course_id: $data['course_id'],
            teacher_id: $data['teacher_id'],
            title: $data['title'],
            description: $data['description'] ?? '',
            class_number: $data['class_number'] ?? '',
            scheduled_date: $data['scheduled_date'],
            ended_at: $data['ended_at'],
            started_at: $data['started_at'],
            duration_minutes: $data['duration_minutes'] ?? 60,
            room_location: $data['room_location'] ?? '',
            status: $data['status'] ?? 'scheduled',
            class_link: $data['class_link'] ?? null,
            record_link: $data['record_link'] ?? null,
            source: $data['source'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,
            'title' => $this->title,
            'description' => $this->description,
            'class_number' => $this->class_number,
            'scheduled_date' => $this->scheduled_date,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'duration_minutes' => $this->duration_minutes,
            'room_location' => $this->room_location,
            'status' => $this->status,
            'class_link' => $this->class_link,
            'record_link' => $this->record_link,
            'source' => $this->source,
        ];
    }
}
