<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'is_read', 'ip_address'])]
class ContactMessage extends Model
{
    use LogsActivity;

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
