<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\Theme;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class ThemeController extends Controller
{
    /**
     * Display a listing of the themes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $themes = Theme::all();
        $active_theme = Theme::getActive();
        
        return view('admin.themes.index', compact('themes', 'active_theme'));
    }
    
    /**
     * Show the form for creating a new theme.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.themes.create');
    }
    
    /**
     * Activate the specified theme.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $theme
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request, $theme)
    {
        if (Theme::exists($theme) && Theme::activate($theme)) {
            $notify_message = "Theme '{$theme}' has been activated.";
            $notify_message = array('message'=>$notify_message,'alert-type'=>'success');
        } else {
            $notify_message = "Failed to activate theme '{$theme}'.";
            $notify_message = array('message'=>$notify_message,'alert-type'=>'error');
        }
        
        return redirect()->route('admin.themes.index')->with($notify_message);
    }
    
    /**
     * Show the theme details.
     *
     * @param  string  $theme
     * @return \Illuminate\Http\Response
     */
    public function show($theme)
    {
        if (!Theme::exists($theme)) {
            $notify_message = "Theme '{$theme}' does not exist.";
            $notify_message = array('message'=>$notify_message,'alert-type'=>'error');
            
            return redirect()->route('admin.themes.index')->with($notify_message);
        }
        
        $themeInfo = Theme::loadThemeInfo($theme);
        $screenshot = file_exists(base_path("cms/themes/{$theme}/screenshot.png")) ? 
            asset("cms/themes/{$theme}/screenshot.png") : 
            asset('backend/img/placeholder-image.jpg');
            
        return view('admin.themes.show', compact('theme', 'themeInfo', 'screenshot'));
    }
    
    /**
     * Remove the specified theme from storage.
     *
     * @param  string  $theme
     * @return \Illuminate\Http\Response
     */
    public function destroy($theme)
    {
        if ($theme === Theme::getActive()) {
            $notify_message = "Cannot delete the active theme.";
            $notify_message = array('message'=>$notify_message,'alert-type'=>'error');
            
            return redirect()->route('admin.themes.index')->with($notify_message);
        }
        
        if (Theme::exists($theme)) {
            File::deleteDirectory(base_path("cms/themes/{$theme}"));
            
            $notify_message = "Theme '{$theme}' has been deleted.";
            $notify_message = array('message'=>$notify_message,'alert-type'=>'success');
        } else {
            $notify_message = "Theme '{$theme}' does not exist.";
            $notify_message = array('message'=>$notify_message,'alert-type'=>'error');
        }
        
        return redirect()->route('admin.themes.index')->with($notify_message);
    }
} 