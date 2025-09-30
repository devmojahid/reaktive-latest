<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Display a listing of all menus
     */
    public function index()
    {
        $menus = Menu::latest()->get();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new menu
     */
    public function create()
    {
        $locations = get_registered_nav_menus();
        return view('admin.menus.create', compact('locations'));
    }

    /**
     * Store a newly created menu
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:menus',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $menu = new Menu();
        $menu->name = $request->name;
        $menu->slug = Str::slug($request->name);
        $menu->location = $request->location;
        $menu->description = $request->description;
        $menu->status = $request->has('status') ? 1 : 0;
        $menu->save();

        return redirect()
            ->route('admin.menus.edit', $menu->id)
            ->with('success', 'Menu created successfully');
    }

    /**
     * Display the specified menu and its items for editing
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $menuItems = MenuItem::where('menu_id', $id)
            ->where('parent_id', 0)
            ->orderBy('order')
            ->with(['children' => function($query) {
                $query->orderBy('order');
            }])
            ->get();
        
        $locations = get_registered_nav_menus();
        $pages = \App\Models\Frontend::select('id', 'data_keys', 'data_values')->get();
        
        return view('admin.menus.edit', compact('menu', 'menuItems', 'locations', 'pages'));
    }

    /**
     * Update the menu info
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menus')->ignore($menu->id),
            ],
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $menu->name = $request->name;
        $menu->slug = Str::slug($request->name);
        $menu->location = $request->location;
        $menu->description = $request->description;
        $menu->status = $request->has('status') ? 1 : 0;
        $menu->save();

        return redirect()
            ->back()
            ->with('success', 'Menu updated successfully');
    }

    /**
     * Add a new menu item to a menu
     */
    public function addMenuItem(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string',
            'type' => 'required|string',
        ]);

        $menu = Menu::findOrFail($id);

        $menuItem = new MenuItem();
        $menuItem->menu_id = $menu->id;
        $menuItem->title = $request->title;
        $menuItem->url = $request->url;
        $menuItem->icon_class = $request->icon_class;
        $menuItem->target = $request->target;
        $menuItem->type = $request->type;
        $menuItem->type_id = $request->type_id;
        $menuItem->parent_id = 0;
        
        // Get the highest order to append to end
        $highestOrder = MenuItem::where('menu_id', $menu->id)
            ->where('parent_id', 0)
            ->max('order') ?? 0;
            
        $menuItem->order = $highestOrder + 1;
        $menuItem->status = 1;
        $menuItem->save();

        return redirect()
            ->back()
            ->with('success', 'Menu item added successfully');
    }

    /**
     * Edit a menu item
     */
    public function updateMenuItem(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string',
        ]);

        $menuItem->title = $request->title;
        $menuItem->url = $request->url;
        $menuItem->icon_class = $request->icon_class;
        $menuItem->target = $request->target;
        $menuItem->css_class = $request->css_class;
        $menuItem->status = $request->has('status') ? 1 : 0;
        $menuItem->save();

        return redirect()
            ->back()
            ->with('success', 'Menu item updated successfully');
    }

    /**
     * Delete a menu item
     */
    public function deleteMenuItem($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        
        // Delete children too
        MenuItem::where('parent_id', $id)->delete();
        
        $menuItem->delete();

        return redirect()
            ->back()
            ->with('success', 'Menu item deleted successfully');
    }

    /**
     * Delete a menu and all its items
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Delete all menu items
        MenuItem::where('menu_id', $id)->delete();
        
        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully');
    }

    /**
     * Get menu item data for AJAX editing
     */
    public function getMenuItem($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        return response()->json($menuItem);
    }

    /**
     * Update the menu structure (order and parent-child relationships)
     */
    public function updateMenuStructure(Request $request)
    {
        $menuItems = json_decode($request->input('menu_items'), true);
        
        DB::beginTransaction();
        
        try {
            $this->updateMenuItemsOrder($menuItems);
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Helper function to recursively update menu items order
     */
    private function updateMenuItemsOrder($items, $parentId = 0, $order = 0)
    {
        foreach ($items as $item) {
            $menuItem = MenuItem::find($item['id']);
            if ($menuItem) {
                $menuItem->parent_id = $parentId;
                $menuItem->order = $order;
                $menuItem->save();
                
                if (!empty($item['children'])) {
                    $this->updateMenuItemsOrder($item['children'], $menuItem->id, 0);
                }
                
                $order++;
            }
        }
    }
}
