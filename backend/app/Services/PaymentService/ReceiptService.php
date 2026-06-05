<?php

namespace App\Services;

use App\Models\AdmissionPayment;
use App\Models\AdmissionRenewal;
use App\Models\TeacherPayment;
use App\Models\StaffPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReceiptService
{
    /**
     * Generate a PDF receipt and return metadata including a WhatsApp share URL.
     *
     * @param  string  $type   admission | renewal | teacher | staff
     * @param  int     $id
     * @return array|null
     */
    public function generate(string $type, int $id): ?array
    {
        [$model, $view, $data] = match ($type) {
            'admission' => $this->admissionData($id),
            'renewal'   => $this->renewalData($id),
            'teacher'   => $this->teacherData($id),
            'staff'     => $this->staffData($id),
            default     => [null, null, null],
        };

        if (!$model) return null;

        // Generate PDF using DomPDF (laravel-dompdf package)
        $pdf = Pdf::loadView("receipts.{$view}", $data)
            ->setPaper('a5', 'portrait')
            ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true]);

        // Store to a temporary public path
        $filename  = "receipt_{$type}_{$id}_" . Str::random(6) . ".pdf";
        $storagePath = "receipts/{$filename}";
        Storage::disk('public')->put($storagePath, $pdf->output());

        $receiptUrl = Storage::disk('public')->url($storagePath);

        // WhatsApp URL — deep link that opens WhatsApp with a pre-filled message.
        // The user then picks the contact manually (no API key required).
        $orgName    = config('app.name', 'Institute');
        $amountLine = $this->amountLine($type, $model);
        $message    = urlencode(
            "📄 *{$orgName} Payment Receipt*\n" .
            "{$amountLine}\n" .
            "Download: {$receiptUrl}\n\n" .
            "_This is an automated receipt._"
        );
        $whatsappUrl = "https://wa.me/?text={$message}";

        return [
            'receipt_url'  => $receiptUrl,
            'filename'     => $filename,
            'whatsapp_url' => $whatsappUrl,
        ];
    }

    // ── Loaders ──────────────────────────────────────────────────────────────

    private function admissionData(int $id): array
    {
        $model = AdmissionPayment::with(['student', 'course', 'receivedBy'])->find($id);
        return [$model, 'admission', compact('model')];
    }

    private function renewalData(int $id): array
    {
        $model = AdmissionRenewal::with(['student', 'course'])->find($id);
        return [$model, 'renewal', compact('model')];
    }

    private function teacherData(int $id): array
    {
        $model = TeacherPayment::with(['teacher', 'items.course', 'releasedBy'])->find($id);
        return [$model, 'teacher', compact('model')];
    }

    private function staffData(int $id): array
    {
        $model = StaffPayment::with(['staff', 'releasedBy'])->find($id);
        return [$model, 'staff', compact('model')];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function amountLine(string $type, $model): string
    {
        return match ($type) {
            'admission' => "Amount Paid: ₹{$model->amount} | {$model->course?->name}",
            'renewal'   => "Renewal: ₹{$model->final_amount} | {$model->renewal_from} → {$model->renewal_to}",
            'teacher'   => "Salary Released: ₹{$model->amount} | {$model->period_start} – {$model->period_end}",
            'staff'     => "Salary Paid: ₹{$model->final_amount} | {$model->salary_month}",
            default     => '',
        };
    }
}
