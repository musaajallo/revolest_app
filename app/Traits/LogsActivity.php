<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::log(
                'created',
                $model,
                null,
                $model->getAttributes(),
                class_basename($model) . ' was created'
            );
        });

        static::updated(function ($model) {
            try {
                $oldValues = array_intersect_key(
                    $model->getOriginal(),
                    $model->getDirty()
                );
                $newValues = $model->getDirty();

                // Don't log if nothing meaningful changed
                if (empty($newValues)) {
                    return;
                }

                // Remove sensitive fields
                $sensitiveFields = ['password', 'remember_token'];
                foreach ($sensitiveFields as $field) {
                    unset($oldValues[$field], $newValues[$field]);
                }

                if (!empty($newValues)) {
                    ActivityLog::log(
                        'updated',
                        $model,
                        $oldValues,
                        $newValues,
                        class_basename($model) . ' was updated'
                    );
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error logging activity: ' . $e->getMessage());
                // Silently fail to not prevent the main model from saving
            }
        });

        static::deleted(function ($model) {
            ActivityLog::log(
                'deleted',
                $model,
                $model->getAttributes(),
                null,
                class_basename($model) . ' was deleted'
            );
        });
    }
}
