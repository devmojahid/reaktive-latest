<?php

namespace Modules\SupportTicket\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TourBooking\App\Models\Service;

class SupportTicket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];


    public function author(){
        return $this->belongsTo(User::class, 'author_id')->select('id', 'name', 'email', 'phone');
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }
}
