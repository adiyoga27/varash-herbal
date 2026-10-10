<?php

namespace App\Models\Concerns;

use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Otomatis mencatat event created / updated / deleted / restored model ke activity_logs,
 * lengkap dengan nilai sebelum & sesudah perubahan.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => $model->writeActivity('created', [
            'attributes' => $model->filterLoggable($model->getAttributes()),
        ]));

        static::updated(function (Model $model) {
            $changes = $model->filterLoggable($model->getChanges());
            if ($changes === []) {
                return;
            }
            $old = array_intersect_key($model->getOriginal(), $changes);
            $model->writeActivity('updated', ['old' => $model->filterLoggable($old), 'attributes' => $changes]);
        });

        static::deleted(fn (Model $model) => $model->writeActivity('deleted', [
            'attributes' => $model->filterLoggable($model->getAttributes()),
        ]));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $model) => $model->writeActivity('restored', []));
        }
    }

    protected function filterLoggable(array $attributes): array
    {
        $attributes = array_diff_key($attributes, array_flip($this->activityHidden()));

        // Potong teks panjang agar log tetap ringan
        return array_map(
            fn ($value) => is_string($value) && mb_strlen($value) > 300 ? mb_substr($value, 0, 300).'…' : $value,
            $attributes
        );
    }

    protected function activityHidden(): array
    {
        return ['password', 'remember_token', 'token', 'updated_at', 'created_at'];
    }

    protected function writeActivity(string $event, array $properties): void
    {
        $label = $this->activityLabel();
        $type = class_basename($this);
        $verbs = ['created' => 'menambahkan', 'updated' => 'mengubah', 'deleted' => 'menghapus', 'restored' => 'memulihkan'];

        ActivityLogger::log(
            event: $event,
            description: ucfirst($verbs[$event] ?? $event)." {$type}: {$label}",
            subject: $this,
            properties: $properties,
            logName: strtolower($type),
            level: $event === 'deleted' ? 'warning' : 'info',
        );
    }

    public function activityLabel(): string
    {
        foreach (['name', 'title', 'subject', 'code', 'email', 'question', 'key'] as $field) {
            if (! empty($this->{$field})) {
                return (string) $this->{$field};
            }
        }

        return '#'.$this->getKey();
    }
}
