<?php

namespace Cms\Themes\Theme1\Src\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\Theme;
use Modules\Blog\App\Models\Blog;
use Modules\Course\App\Models\Course;
use Modules\Category\Entities\Category;

class Theme1Controller extends Controller
{
    /**
     * Display the theme homepage
     */
    public function index()
    {
        $data = [
            'page_title' => 'Home',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/']
            ]
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('index', $data);
    }

    /**
     * Display the about page
     */
    public function about()
    {
        $data = [
            'page_title' => 'About Us',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'About Us', 'url' => null]
            ]
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.about', $data);
    }

    /**
     * Display the about page
     */
    public function demo()
    {
        $data = [
            'page_title' => 'About Us',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
            ]
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.demo', $data);
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        $data = [
            'page_title' => 'Contact Us',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Contact Us', 'url' => null]
            ]
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.contact', $data);
    }

    /**
     * Display the courses page
     */
    public function courses()
    {
        $courses = Course::with('category', 'instructor')
            ->where('status', 'published')
            ->paginate(12);

        $data = [
            'page_title' => 'Courses',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Courses', 'url' => null]
            ],
            'courses' => $courses
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.courses', $data);
    }

    /**
     * Display the blogs page
     */
    public function blogs()
    {
        $blogs = Blog::with('author', 'category')
            ->where('status', 1)
            ->paginate(9);

        $data = [
            'page_title' => 'Blog',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Blog', 'url' => null]
            ],
            'blogs' => $blogs
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.blogs', $data);
    }

    /**
     * Display a single blog post
     */
    public function blogSingle($slug)
    {
        $blog = Blog::with('author', 'category')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $data = [
            'page_title' => $blog->title,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Blog', 'url' => route('theme1.blogs')],
                ['label' => $blog->title, 'url' => null]
            ],
            'blog' => $blog
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.blog-single', $data);
    }

    /**
     * Display a single course
     */
    public function courseSingle($slug)
    {
        $course = Course::with('category', 'instructor', 'lessons')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $data = [
            'page_title' => $course->title,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Courses', 'url' => route('theme1.courses')],
                ['label' => $course->title, 'url' => null]
            ],
            'course' => $course
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.course-single', $data);
    }

    /**
     * Display category page
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('status', 'enable')
            ->firstOrFail();

        $courses = Course::with('instructor')
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->paginate(12);

        $data = [
            'page_title' => $category->name,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Categories', 'url' => '/categories'],
                ['label' => $category->name, 'url' => null]
            ],
            'category' => $category,
            'courses' => $courses
        ];

        // Add theme-specific data
        $themeData = $this->getThemeData();
        $data = array_merge($data, $themeData);

        return Theme::view('pages.category', $data);
    }

    /**
     * Get common theme data
     */
    protected function getThemeData()
    {
        return [
            'theme_info' => Theme::loadThemeInfo(Theme::current()),
            'social_links' => getContent('social_links.element'),
            'footer_content' => getContent('footer.content', true),
            'header_content' => getContent('header.content', true),
            'theme_service' => app('Cms\Themes\Theme1\Src\Services\Theme1Service'),
        ];
    }
} 