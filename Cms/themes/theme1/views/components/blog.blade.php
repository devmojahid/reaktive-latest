@php
    use Modules\Blog\App\Models\Blog;
    use Illuminate\Support\Str;

    $home1_blog = getContent('theme1_blog.content', true);
    $blogs = Blog::with('translate:id,blog_id,lang_code,title,reading_time')
        ->where('status', true)
        ->latest()
        ->take(3)
        ->get();
@endphp

<!-- blog-area-start -->
<div class="tg-blog-area tg-blog-space tg-grey-bg pt-135 p-relative z-index-1">
    <img class="tg-blog-shape" src="{{ asset('frontend/assets/img/shape/map-shape-3.png') }}" alt="shape">
    <img class="tg-blog-shape-2" src="{{ asset('frontend/assets/img/shape/map-shape-4.png') }}" alt="shape">
    <div class="container">
        <div class="row">
            <!-- Section Title -->
            <div class="col-lg-12">
                <div class="tg-location-section-title text-center mb-30">
                    <h5 class="tg-section-subtitle mb-15 wow fadeInUp" data-wow-delay=".3s" data-wow-duration=".9s">
                        {{ getTranslatedValue($home1_blog, 'sub_title') }}
                    </h5>
                    <h2 class="mb-15 text-capitalize wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".9s">
                        {{ getTranslatedValue($home1_blog, 'title') }}
                    </h2>
                    <p class="text-capitalize wow fadeInUp" data-wow-delay=".5s" data-wow-duration=".9s">
                        {!! strip_tags(clean(getTranslatedValue($home1_blog, 'description')), '<br>') !!}
                    </p>
                </div>
            </div>

            @if ($blogs->count() > 0)
                @php
                    $firstBlog = $blogs->get(0);
                    $restBlogs = $blogs->slice(1);
                @endphp

                <!-- Left Side Big Blog -->
                <div class="col-lg-5 wow fadeInLeft" data-wow-delay=".4s" data-wow-duration=".9s">
                    <div class="tg-blog-item mb-25">
                        <div class="tg-blog-thumb fix left-side-img">
                            <a href="{{ route('blog', ['slug' => $firstBlog->slug]) }}">
                                <img class="w-100" src="{{ asset($firstBlog->image) }}"
                                    alt="{{ $firstBlog?->translate?->title }}">
                            </a>
                        </div>
                        <div class="tg-blog-content p-relative">
                            <span class="tg-blog-tag p-absolute">{{ $firstBlog?->category?->name }}</span>
                            <h3 class="tg-blog-title">
                                <a href="{{ route('blog', ['slug' => $firstBlog->slug]) }}">
                                    {{ $firstBlog?->translate?->title }}
                                </a>
                            </h3>
                            <div class="tg-blog-date">
                                <span class="mr-20"><i class="fa-light fa-calendar"></i>
                                    {{ $firstBlog->created_at->format('jS M, Y') }}</span>
                                @if ($firstBlog?->translate?->reading_time)
                                    <span><i class="fa-regular fa-clock"></i>
                                        {{ $firstBlog?->translate?->reading_time }} </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side Small Blogs -->
                <div class="col-lg-7">
                    <div class="row">
                        @foreach ($restBlogs as $i => $blog)
                            <div class="col-12 wow fadeInRight" data-wow-delay=".{{ $i + 4 }}s"
                                data-wow-duration=".9s">
                                <div class="tg-blog-item mb-20">
                                    <div class="row align-items-center">
                                        <div class="col-lg-5">
                                            <div class="tg-blog-thumb fix right-side-img">
                                                <a href="{{ route('blog', ['slug' => $blog->slug]) }}">
                                                    <img class="w-100" src="{{ asset($blog->image) }}" alt="blog">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="tg-blog-contents">
                                                <span
                                                    class="tg-blog-tag d-inline-block mb-10">{{ $blog?->category?->name }}</span>
                                                <h3 class="tg-blog-title title-2 mb-0">
                                                    <a
                                                        href="{{ route('blog', ['slug' => $blog->slug]) }}">{{ $blog?->translate?->title }}</a>
                                                </h3>
                                                <div class="tg-blog-date">
                                                    <span class="mr-20"><i class="fa-light fa-calendar"></i>
                                                        {{ $blog->created_at->format('jS M, Y') }}</span>
                                                    @if ($blog?->translate?->reading_time)
                                                        <span><i class="fa-regular fa-clock"></i>
                                                            {{ $blog?->translate?->reading_time }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Footer link -->
            <div class="col-12 wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".9s">
                <div class="tg-blog-bottom text-center pt-25">
                    <p>{{ __('translate.Want to see our Recent News & Updates?') }}
                        <a href="{{ route('blogs') }}">
                            {{ __('translate.Click here to View More') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- blog-area-end -->
@push('style_section')
    <style>
        .tg-blog-thumb.fix.left-side-img img {
            height: 260px;
        }

        .tg-blog-thumb.fix.right-side-img img {
            height: 167px;
        }
    </style>
@endpush
