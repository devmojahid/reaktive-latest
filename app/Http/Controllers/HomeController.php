<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Rules\Captcha;
use Illuminate\Http\Request;
use Modules\FAQ\App\Models\Faq;
use Modules\Blog\App\Models\Blog;
use Modules\Page\App\Models\ContactUs;
use Modules\Category\Entities\Category;
use Modules\Page\App\Models\CustomPage;
use Modules\Partner\App\Models\Partner;
use Modules\Blog\App\Models\BlogComment;
use Modules\Blog\App\Models\BlogCategory;
use Modules\Currency\App\Models\Currency;
use Modules\Language\App\Models\Language;
use Modules\Page\App\Models\PrivacyPolicy;
use Modules\Page\App\Models\TermAndCondition;
use Modules\SeoSetting\App\Models\SeoSetting;
use Modules\Testimonial\App\Models\Testimonial;
use Modules\GlobalSetting\App\Models\GlobalSetting;
use App\Facades\Theme;
use Modules\Team\App\Models\Team;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        try {
            $seo_setting = SeoSetting::where('id', 1)->first();

            $breadcrumb_title = trans('translate.Home');

            // Check if there's a theme parameter in the request for theme preview
            if ($request->has('theme')) {
                $requestedTheme = $request->get('theme');
                if (theme()->exists($requestedTheme)) {
                    // Set the temporary theme for this session only
                    theme()->setTemporary($requestedTheme);
                }
            }

            // Load Theme System with current active theme
            $selectedTheme = theme()->current();

            $data = [];

            // Load theme-specific content from settings.json and theme system
            $themeSettings = theme()->getThemeSettings();

            // Process theme-specific content
            foreach ($themeSettings as $key => $section) {
                $contentKey = $key . '.content';
                $contentData = getContent($contentKey, true);
                if ($contentData) {
                    $data[str_replace($selectedTheme . '_', '', $key)] = $contentData;
                }
            }

            // Add common data needed by all themes
            $data['seo_setting'] = $seo_setting;
            $data['breadcrumb_title'] = $breadcrumb_title;
            $data['social_links'] = getContent('social_links.element');
            $data['categories'] = Category::with(['courses' => function ($query) {
                $query->take(20);
            }])->take(3)->get();

            // Add theme information
            $data['theme_info'] = theme()->loadThemeInfo($selectedTheme);
            $data['current_theme'] = $selectedTheme;

            // dd($data);

            // Return the theme view
            return theme()->view('index', $data);
        } catch (\Exception $e) {
            \Log::error('Error in HomeController index: ' . $e->getMessage());
            return redirect('404');
        }
    }

    public function about_us()
    {

        $seo_setting = SeoSetting::where('id', 3)->first();

        $breadcrumb_title = trans('translate.About Us');

        $about_us = getContent('about_page_about_section.content', true);

        $what_we_do = getContent('about_page_what_we_do.content', true);

        $about_cta = getContent('about_page_cta.content', true);

        return view('about_us', [
            'seo_setting' => $seo_setting,
            'breadcrumb_title' => $breadcrumb_title,
            'about_us' => $about_us,
            'what_we_do' => $what_we_do,
            'about_cta' => $about_cta,
        ]);
    }

    public function blogs(Request $request)
    {

        $page_view = 'blogs';
        $paginate_qty = 9;
        if ($request->page_view) {
            if ($request->page_view == 'blogs_with_sidebar') {
                $page_view = 'blogs_with_sidebar';
                $paginate_qty = 6;
            } else {
                $page_view = 'blogs';
            }
        }

        $blogs = Blog::with('author')->where('status', 1);

        if ($request->category) {
            $blogs = $blogs->where('blog_category_id', $request->category);
        }

        if ($request->search) {
            $blogs = $blogs->where(function ($query) use ($request) {
                $query->whereHas('front_translate', function ($subQuery) use ($request) {
                    $subQuery->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                })
                    ->orWhereJsonContains('tags', [['value' => $request->search]]);
            });
        }

        $blogs = $blogs->paginate($paginate_qty);

        $seo_setting = SeoSetting::where('id', 2)->first();

        $breadcrumb_title = trans('translate.Our Blogs');

        $latest_blogs = Blog::with('author')->where('status', 1)->take(5)->get();

        $blog_categories = BlogCategory::where('status', 1)->get();

        $blog_for_tags = Blog::where('status', 1)->select('status', 'tags')->get();

        $tags_array = [];
        foreach ($blog_for_tags as $blog_for_tag) {
            if ($blog_for_tag->tags) {
                foreach (json_decode($blog_for_tag->tags) ?? [] as $blog_tag) {
                    if (!in_array($blog_tag->value, $tags_array)) {
                        $tags_array[] = $blog_tag->value;
                    }
                }
            }
        }

        return view($page_view, [
            'blogs' => $blogs,
            'seo_setting' => $seo_setting,
            'breadcrumb_title' => $breadcrumb_title,
            'latest_blogs' => $latest_blogs,
            'blog_categories' => $blog_categories,
            'tags_array' => $tags_array,
        ]);
    }


    public function blog($slug)
    {
        $blog = Blog::with('author')->where('status', 1)->where('slug', $slug)->firstOrFail();

        $blog->views = $blog->views + 1;
        $blog->save();

        $blog_comments = BlogComment::where('blog_id', $blog->id)->where('status', 1)->latest()->get();

        $breadcrumb_title = trans('translate.Blog Details');

        $latest_blogs = Blog::with('author')->where('status', 1)->take(5)->get();

        $blog_categories = BlogCategory::where('status', 1)->get();

        $blog_for_tags = Blog::where('status', 1)->select('status', 'tags')->get();

        $tags_array = [];
        foreach ($blog_for_tags as $blog_for_tag) {
            if ($blog_for_tag->tags) {
                foreach (json_decode($blog_for_tag->tags) ?? [] as $blog_tag) {
                    if (!in_array($blog_tag->value, $tags_array)) {
                        $tags_array[] = $blog_tag->value;
                    }
                }
            }
        }

        return view('blog_detail', [
            'blog' => $blog,
            'blog_comments' => $blog_comments,
            'breadcrumb_title' => $breadcrumb_title,
            'latest_blogs' => $latest_blogs,
            'blog_categories' => $blog_categories,
            'tags_array' => $tags_array,
        ]);
    }

    public function store_blog_comment(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'comment' => 'required',
            'g-recaptcha-response' => new Captcha()
        ], [
            'name.required' => trans('translate.Name is required'),
            'email.required' => trans('translate.Email is required'),
            'comment.required' => trans('translate.Comment is required'),
        ]);

        $blog_comment = new Blogcomment();
        $blog_comment->blog_id = $id;
        $blog_comment->name = $request->name;
        $blog_comment->email = $request->email;
        $blog_comment->comment = $request->comment;
        $blog_comment->status = 0;
        $blog_comment->save();

        $notify_message = trans('translate.Comment submited successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }

    public function contact_us()
    {
        $contact_us = ContactUs::first();

        $seo_setting = SeoSetting::where('id', 4)->first();

        $breadcrumb_title = trans('translate.Contact Us');

        return view('contact_us', [
            'contact_us' => $contact_us,
            'seo_setting' => $seo_setting,
            'breadcrumb_title' => $breadcrumb_title,
        ]);
    }

    public function faq()
    {
        $faq = getContent('faq_section.content', true);

        $footer_cta = getContent('footer_cta.content', true);

        $seo_setting = SeoSetting::where('id', 4)->first();

        $breadcrumb_title = trans('translate.Frequently Asked Question');

        return view('faq', [
            'faq' => $faq,
            'seo_setting' => $seo_setting,
            'breadcrumb_title' => $breadcrumb_title,
            'footer_cta' => $footer_cta
        ]);
    }
    public function pricing()
    {

        $footer_cta = getContent('footer_cta.content', true);

        $seo_setting = SeoSetting::where('id', 4)->first();

        $breadcrumb_title = trans('translate.Pricing Plan');

        return view('pricing', [
            'seo_setting' => $seo_setting,
            'breadcrumb_title' => $breadcrumb_title,
            'footer_cta' => $footer_cta
        ]);
    }

    public function privacy_policy()
    {
        $privacy_policy = PrivacyPolicy::first();

        $seo_setting = SeoSetting::where('id', 9)->first();

        $breadcrumb_title = trans('translate.Privacy Policy');

        return view('privacy_policy', ['privacy_policy' => $privacy_policy, 'seo_setting' => $seo_setting, 'breadcrumb_title' => $breadcrumb_title]);
    }

    public function terms_conditions()
    {
        $terms_conditions = TermAndCondition::first();

        $seo_setting = SeoSetting::where('id', 6)->first();

        $breadcrumb_title = trans('translate.Terms & Conditions');

        return view('terms_conditions', ['terms_conditions' => $terms_conditions, 'seo_setting' => $seo_setting, 'breadcrumb_title' => $breadcrumb_title]);
    }

    public function custom_page($slug)
    {
        $custom_page = CustomPage::where('slug', $slug)->firstOrFail();

        $breadcrumb_title = $custom_page->page_name;

        return view('custom_page', ['custom_page' => $custom_page, 'breadcrumb_title' => $breadcrumb_title]);
    }

    public function download_file($file)
    {
        $filepath = public_path() . "/uploads/custom-images/" . $file;
        return response()->download($filepath);
    }


    public function language_switcher(Request $request)
    {

        $request_lang = Language::where('lang_code', $request->lang_code)->first();

        Session::put('front_lang', $request->lang_code);
        Session::put('front_lang_name', $request_lang->lang_name);
        Session::put('lang_dir', $request_lang->lang_direction);

        app()->setLocale($request->lang_code);

        $notify_message = trans('translate.Language switched successful');
        if (env('APP_MODE') == 'DEMO') {
            $notify_message = array('message' => $notify_message, 'alert-type' => 'success', 'demo_mode' => 'Demo mode not tranlsate all language');
        } else {
            $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        }


        return redirect()->back()->with($notify_message);
    }


    public function currency_switcher(Request $request)
    {

        $request_currency = Currency::where('currency_code', $request->currency_code ?? 'USD')->first();

        Session::put('currency_name', $request_currency->currency_name);
        Session::put('currency_code', $request_currency->currency_code);
        Session::put('currency_icon', $request_currency->currency_icon);
        Session::put('currency_rate', $request_currency->currency_rate);
        Session::put('currency_position', $request_currency->currency_position);

        $notify_message = trans('translate.Currency switched successful');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }

    /**
     * Switch to a different theme
     */
    public function switchTheme(Request $request, $theme)
    {
        // Validate theme exists
        if (!theme()->exists($theme)) {
            $notify_message = trans('translate.Theme not found');
            return back()->with(['message' => $notify_message, 'alert-type' => 'error']);
        }

        // Set the theme permanently as system default using our enhanced DB-backed approach
        if (theme()->activate($theme)) {
            $notify_message = trans('translate.Theme switched successfully');
            return back()->with(['message' => $notify_message, 'alert-type' => 'success']);
        } else {
            $notify_message = trans('translate.Error switching theme');
            return back()->with(['message' => $notify_message, 'alert-type' => 'error']);
        }
    }

    public function teams()
    {

        $teams = Team::select('id', 'slug', 'image', 'facebook', 'twitter', 'linkedin', 'instagram')
            ->with('translate:id,team_id,lang_code,name,designation')
            ->latest()
            ->get();

        $seo_setting = SeoSetting::where('id', 7)->first();
        $breadcrumb_title = trans('translate.Our Teams');

        return view('teams', [
            'teams' => $teams,
            'seo_setting' => $seo_setting,
            'breadcrumb_title' => $breadcrumb_title
        ]);
    }

    public function teamPerson($slug)
    {
        $team = Team::with('translate')->where('slug', $slug)->firstOrFail();
        $seo_setting = SeoSetting::where('id', 7)->first();
        $breadcrumb_title = trans('translate.Our Teams');

        return view('team_single', [
            'team' => $team,
            'seo_setting' => $seo_setting,
            'breadcrumb_title' => $breadcrumb_title
        ]);
    }

    public function themeVariation(Request $request)
    {
        try {
            $requestedTheme = $request->query('theme');

            if (!$requestedTheme || !theme()->exists($requestedTheme)) {
                return redirect()->route('home');
            }

            // Create temporary theme instance
            $tempTheme = new \App\Themes\Core\Theme($requestedTheme);
            $themePath = $tempTheme->getThemePath($requestedTheme);

            // Prepare data (same as your existing logic)
            $data = [];
            $themeSettings = $tempTheme->getThemeSettings();

            foreach ($themeSettings as $key => $section) {
                $contentKey = $key . '.content';
                $contentData = getContent($contentKey, true);
                if ($contentData) {
                    $data[str_replace($requestedTheme . '_', '', $key)] = $contentData;
                }
            }

            $seo_setting = SeoSetting::where('id', 1)->first();
            $breadcrumb_title = trans('translate.Home');

            $data['seo_setting'] = $seo_setting;
            $data['breadcrumb_title'] = $breadcrumb_title;

            $data['social_links'] = getContent('social_links.element');
            $data['categories'] = Category::with(['courses' => function ($query) {
                $query->take(20);
            }])->take(3)->get();
            $data['theme_info'] = $tempTheme->loadThemeInfo($requestedTheme);
            $data['current_theme'] = $requestedTheme;

            // Load theme functions
            $functionsFile = $themePath . '/functions/functions.php';
            if (file_exists($functionsFile)) {
                include_once $functionsFile;
            }

            // Register the 'theme' namespace that the theme views expect
            $viewFactory = app('view');

            // Add the standard 'theme' namespace that theme views use
            $viewFactory->addNamespace('theme', $themePath . '/views');

            // Also add theme-specific namespace as backup
            // $themeNamespace = 'theme_' . $requestedTheme;
            // $viewFactory->addNamespace($themeNamespace, $themePath . '/views');

            // Use the theme namespace (the one the view expects)
            return $viewFactory->make("theme::index", $data);

        } catch (\Exception $e) {
            \Log::error('Theme variation error: ' . $e->getMessage());
            return redirect()->route('home');
        }
    }
}
