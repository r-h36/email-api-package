<?php

namespace Rh36\EmailApiPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rh36\EmailApiPackage\Events\EmailWasCreated;

class EmailLog extends Model
{
    use HasFactory;

    // Disable Laravel's mass assignment protection
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => EmailWasCreated::class,
    ];

    protected static function newFactory()
    {
        return \Rh36\EmailApiPackage\Database\Factories\EmailLogFactory::new();
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
