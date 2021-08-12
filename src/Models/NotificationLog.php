<?php

namespace Zenegal\NotificationLogs\Models;

use App\Models\NotificationType;
use App\Models\Store;
use App\Traits\ScopeStoreId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Zenegal\NotificationLogs\Support\Str;

class NotificationLog extends Model
{
    use ScopeStoreId;

    protected $guarded = [];

    protected $casts = [
        'modal_id' => 'integer',
        'recipients' => 'array',
        'sent_at' => 'datetime',
    ];

    public function scopeFilter(object $query, array $filters)
    {
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
        return $this->modal::find($this->modal_id);
    }

    public function resend(): void
    {
        if ($this->is_notification) {
            (unserialize($this->notifiable))->notify(unserialize($this->notification));
        } else {
            $mailable = unserialize($this->mailable);

            Mail::send($mailable->with([
                '__notification_logs' => [
                    'id' => $this->id,
                    'uuid' => $this->uuid
                ]
            ]));
        }
    }

    public function getMailableNameStringAttribute(): string
    {
        $mailableName = Str::afterLast($this->mailable_name, '\\');

        return Str::studlyWords(Str::snake($mailableName));
    }
}
