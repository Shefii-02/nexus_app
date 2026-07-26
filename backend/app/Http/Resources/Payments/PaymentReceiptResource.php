<?php
namespace App\Http\Resources\Payments;
use Illuminate\Http\Resources\Json\JsonResource;
// ─── PaymentReceiptResource.php ────────────────────────────────────────────

class PaymentReceiptResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'receipt_url'   => $this['receipt_url'],
            'filename'      => $this['filename'],
            'whatsapp_url'  => $this['whatsapp_url'],
            'preview_base64'=> $this['preview_base64'] ?? null,
        ];
    }
}
