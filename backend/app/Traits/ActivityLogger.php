<?php
namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait ActivityLogger
{
    public function logActivity($action, $reference_name = null, $reference_url = null, $notes = null, $company_id = null, $user_id = null)
    {
        ActivityLog::create([
            'action'         => $action,
            'reference_name' => $reference_name,
            'reference_url'  => $reference_url ?? request()->fullUrl(),
            'user_agent'     => request()->userAgent(),
            'ip_address'     => request()->ip(),
            'company_id'     => $company_id ??  1,
            'user_id'        => $user_id ?? Auth::id(),
            'notes'          => $notes,
        ]);
    }
}
