<?php

namespace App\Services;

use App\Models\AdmissionPayment;
use App\Models\AdmissionRenewal;
use App\Models\StaffPayment;
use App\Models\TeacherPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReceiptPdfService
{
    /**
     * Generate a PAID receipt PDF for a student's admission payment.
     */
    public static function generateAdmissionReceipt(AdmissionPayment $payment, string $path): string
    {
        $pdf = Pdf::loadView('receipts.admission', [
            'payment' => $payment->loadMissing(['student:id,name', 'course:id,name']),
        ]);

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate a PENDING fee invoice PDF for a student's renewal.
     */
    public static function generateAdmissionInvoice(AdmissionRenewal $renewal, string $path): string
    {
        $pdf = Pdf::loadView('receipts.pending-invoice', [
            'title'   => 'Fee Invoice',
            'name'    => optional($renewal->student)->name,
            'course'  => optional($renewal->course)->name,
            'period'  => "{$renewal->renewal_from} to {$renewal->renewal_to}",
            'amount'  => $renewal->final_amount,
            'dueDate' => $renewal->current_expiry_date,
            'remarks' => $renewal->remarks,
        ]);

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate a PAID/RELEASED receipt PDF for a teacher payment batch.
     */
    public static function generateTeacherReceipt(TeacherPayment $payment, string $path): string
    {
        $pdf = Pdf::loadView('receipts.teacher', [
            'payment' => $payment->loadMissing(['teacher:id,name', 'releasedBy:id,name', 'items.course:id,name']),
        ]);

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate a PENDING invoice PDF for a teacher payment awaiting release.
     */
    public static function generateTeacherInvoice(TeacherPayment $payment, string $path): string
    {
        $pdf = Pdf::loadView('receipts.pending-invoice', [
            'title'   => 'Teacher Payment Invoice',
            'name'    => optional($payment->teacher)->name,
            'course'  => $payment->items->pluck('course.name')->filter()->implode(', '),
            'period'  => "{$payment->period_start} to {$payment->period_end}",
            'amount'  => $payment->amount,
            'dueDate' => null,
            'remarks' => $payment->remarks,
        ]);

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate a PAID/RELEASED receipt PDF for a staff salary payment.
     */
    public static function generateStaffReceipt(StaffPayment $payment, string $path): string
    {
        $pdf = Pdf::loadView('receipts.staff', [
            'payment' => $payment->loadMissing(['staff:id,name', 'releasedBy:id,name']),
        ]);

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate a PENDING invoice PDF for a staff salary payment awaiting release.
     */
    public static function generateStaffInvoice(StaffPayment $payment, string $path): string
    {
        $pdf = Pdf::loadView('receipts.pending-invoice', [
            'title'   => 'Staff Salary Invoice',
            'name'    => optional($payment->staff)->name,
            'course'  => null,
            'period'  => $payment->salary_month,
            'amount'  => $payment->final_amount,
            'dueDate' => null,
            'remarks' => $payment->deduction_reason ?? $payment->remarks,
        ]);

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Build a stable, collision-resistant receipt filename.
     */
    public static function buildFilename(string $prefix, int $id, ?string $salt = null): string
    {
        return "{$prefix}_{$id}_" . substr(md5($id . ($salt ?? '')), 0, 6) . '.pdf';
    }
}
