<?php

namespace Rh36\EmailApiPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    // Disable Laravel's mass assignment protection
    protected $guarded = [];

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
