<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'title',
        'url',
        'target',
        'icon_class',
        'parent_id',
        'order',
        'type',
        'type_id',
        'css_class',
        'status'
    ];

    /**
     * Get the menu that owns the item
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the parent menu item
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get the children menu items
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }
    
    /**
     * Scope a query to only include active menu items
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    
    /**
     * Get only active children
     */
    public function activeChildren()
    {
        return $this->children()->where('status', 1);
    }
    
    /**
     * Check if this menu item has children
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if this menu item has active children
     */
    public function hasActiveChildren()
    {
        return $this->activeChildren()->count() > 0;
    }
}
