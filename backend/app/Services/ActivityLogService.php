<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class ActivityLogService
{
    public static function log(
        string $module,
        string $action,
        ?string $description = null,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {

        $agent = new Agent();

        ActivityLog::create([
            'user_id' => auth()->id(),

            'module' => $module,
            'action' => $action,
            'description' => $description,

            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,

            'old_values' => $oldValues,
            'new_values' => $newValues,

            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),

            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
        ]);
    }
}
