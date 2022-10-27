<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskHistoryModel extends Model
{
    use SoftDeletes;
    protected $table = 'task_history';
    protected $guarded = [];
}
