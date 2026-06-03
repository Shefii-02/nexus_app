<?php

namespace App\Http\Controllers\API\Admin;

use App\DTOs\PaymentDTO;
use App\DTOs\RenewalDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\AdmissionRequest;
use App\Http\Requests\RenewalRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\RenewalResource;
use App\Models\CourseRenewal;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function storeAdmission(AdmissionRequest $req)
    {
        $dto = PaymentDTO::fromArray($req->validated());

        return response()->json([
            'data' => $this->service->createAdmission($dto)
        ]);
    }

    public function admissionList()
    {
        return PaymentResource::collection(
            $this->service->admissionList()
        );
    }

    public function storeRenewal(RenewalRequest $req)
    {
        $dto = RenewalDTO::fromArray($req->validated());

        return response()->json([
            'data' => $this->service->createRenewal($dto)
        ]);
    }

    public function renewalList()
    {
        return RenewalResource::collection(
            $this->service->renewalList()
        );
    }

    public function transactions()
    {
        return response()->json(
            $this->service->transactions()
        );
    }

    // 📄 Invoice
    public function invoice(string $type, int $id)
    {
        if ($type === 'admission') {
            $data = Payment::with('student.user','course')->findOrFail($id);
        } else {
            $data = CourseRenewal::with('student.user','course')->findOrFail($id);
        }

        $pdf = \PDF::loadView('invoice', compact('data','type'));

        return $pdf->download("invoice_$id.pdf");
    }
}
