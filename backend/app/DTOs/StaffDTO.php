<?php

namespace App\DTOs;

class StaffDTO
{
    public function __construct(
        public string $department,
        public string $designation,
        public string $name,
        public string $email,
        public string $password,
        public string $phone,
        public string $address,
        public string $acc_type = 'staff',
        public string $status = 'active',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            department: $data['department'] ?? '',
            designation: $data['designation'] ?? '',
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            phone: $data['phone'] ?? '',
            address: $data['address'] ?? '',
            acc_type: $data['acc_type'] ?? 'staff',
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

    public function toStaffArray(int $userId): array
    {
        return [
            'user_id' => $userId,
            'department' => $this->department,
            'designation' => $this->designation,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
        ];
    }
}
