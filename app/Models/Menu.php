<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
        'status'
    ];

    /**
     * Get the menu items for this menu
     */
    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    /**
     * Get only the root level menu items (no parents)
     */
    public function rootItems()
    {
        return $this->hasMany(MenuItem::class)
            ->where('parent_id', 0)
            ->orderBy('order');
    }
    
    /**
     * Get only active menu items
     */
    public function activeItems()
    {
        return $this->hasMany(MenuItem::class)
            ->where('status', 1)
            ->orderBy('order');
    }
    
    /**
     * Get only active root level menu items
     */
    public function activeRootItems()
    {
        return $this->hasMany(MenuItem::class)
            ->where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('order');
    }
}
