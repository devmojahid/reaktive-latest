<?php

namespace Modules\Team\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTranslation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'skill_list' => 'array'
    ];

}
