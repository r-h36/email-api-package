<?php

namespace Rh36\EmailApiPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    // Disable Laravel's mass assignment protection
    protected $guarded = [];

    protected static function newFactory()
    {
        return \Rh36\EmailApiPackage\Database\Factories\EmailTemplateFactory::new();
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
