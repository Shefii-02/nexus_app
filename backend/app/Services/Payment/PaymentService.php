<?php

namespace App\Services\Payment;

use App\DTOs\PaymentDTO;
use App\DTOs\RenewalDTO;
use App\Repositories\Payment\PaymentRepository;
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private PaymentRepository $repo) {}

    public function createAdmission(PaymentDTO $dto)
    {
        return $this->repo->createAdmission($dto->toArray());
    }

    public function createRenewal(RenewalDTO $dto)
    {
        return $this->repo->createRenewal($dto->toArray());
    }

    public function admissionList()
    {
        return $this->repo->admissionList();
    }

    public function renewalList()
    {
        return $this->repo->renewalList();
    }

    public function transactions()
    {
        return $this->repo->transactions();
    }
}
