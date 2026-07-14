<?php

namespace App\DTOs;

class TeacherDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $phone,
        public string $qualification,
        public string $subject,
        public int $experience_years,
        public string $address,
        public string $acc_type = 'teacher',
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? '',
            phone: $data['phone'],
            qualification: $data['qualification'] ?? "",
            subject: $data['subject'] ?? "",
            experience_years: $data['experience_years'] ?? "",
            address: $data['address'] ?? '',
            acc_type: 'teacher',
            status: $data['status'] ?? 'active',
        );
    }

    public function toUserArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password ?? '123456'),
            'phone' => $this->phone,
            'acc_type' => $this->acc_type,
            'status' => $this->status,
        ];
    }

    public function toTeacherArray(int $userId): array
    {
        return [
            'user_id' => $userId,
            'qualification' => $this->qualification,
            'subject' => $this->subject,
            'experience_years' => $this->experience_years,
            'address' => $this->address ?? '',
        ];
    }
}
