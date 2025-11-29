<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectAccount extends Pivot
{
    use HasFactory;

    protected $table = 'project_accounts';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'bank_account_id',
    ];
}
