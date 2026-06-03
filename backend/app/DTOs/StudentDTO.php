<?php

namespace App\DTOs;

class StudentDTO
{
    public function __construct(
        public string $roll_number,
        public string $name,
        public string $email,
        public string $password,
        public string $phone,
        public string $address,
        public string $guardian_name,
        public string $guardian_phone,
        public string $acc_type = 'student',
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? '',
            phone: $data['phone'],
            roll_number: $data['roll_number'],
            address: $data['address'] ?? '',
            guardian_name: $data['guardian_name'] ?? '',
            guardian_phone: $data['guardian_phone'] ?? '',
            status: $data['status'] ?? 'active',
        );
    }

    public function toUserArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'phone' => $this->phone,
            'acc_type' => $this->acc_type,
            'status' => $this->status,
        ];
    }

    public function toStudentArray($userId): array
    {
        return [
            'user_id' => $userId,
            'roll_number' => $this->roll_number,
            'phone' => $this->phone,
            'address' => $this->address,
            'guardian_name' => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'status' => $this->status,
        ];
    }
}
