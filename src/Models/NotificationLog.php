<?php

namespace Zenegal\NotificationLogs\Models;

use App\Models\NotificationType;
use App\Models\Store;
use App\Traits\ScopeStoreId;
use Illuminate\Database\Eloquent\Model;
use Zenegal\NotificationLogs\Support\Str;

class NotificationLog extends Model
{
    use ScopeStoreId;

    protected $guarded = [];

    protected $casts = [
        'model_id' => 'integer',
        'recipients' => 'array',
        'sent_at' => 'datetime',
    ];

    public function scopeFilter(object $query, array $filters)
    {
        if (isset($filters['object'])) {
            $query->where('model', $filters['object']);
        }

        if (isset($filters['object_id'])) {
            $query->where('model_id', $filters['object_id']);
        }

        if (isset($filters['notification_type'])) {
            $query->where('mailable_name', $filters['notification_type']);
        }

        if (isset($filters['sent_status'])) {
            $query->where('is_sent', $filters['sent_status'] === 'sent');
        }
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function object()
    {
        return $this->model::find($this->model_id);
    }

    public function getMailableNameStringAttribute(): string
    {
        $mailableName = Str::afterLast($this->mailable_name, '\\');

        return Str::studlyWords(Str::snake($mailableName));
    }

    public function getModelNameAttribute(): string
    {
        $modelName = Str::afterLast($this->model, '\\');

        return Str::studlyWords(Str::snake($modelName));
    }
}
