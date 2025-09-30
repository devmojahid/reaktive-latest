<div class="tg-blog-sidebar top-sticky mb-30">
  <div class="tg-blog-sidebar-search tg-blog-sidebar-box mb-40">
      <h5 class="tg-blog-sidebar-title mb-15">{{ __('translate.Search') }}</h5>
      <div class="tg-blog-sidebar-form">
          <form action="{{ route('blogs') }}">
              <input type="text" placeholder="{{ __('translate.Type here . . .') }}"
                  name="search" value="{{ request()->get('search') }}">
              <button>
                  <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_497_1336)">
                          <path
                              d="M17 17L13.5247 13.5247M15.681 8.3405C15.681 12.3945 12.3945 15.681 8.3405 15.681C4.28645 15.681 1 12.3945 1 8.3405C1 4.28645 4.28645 1 8.3405 1C12.3945 1 15.681 4.28645 15.681 8.3405Z"
                              stroke="#560CE3" stroke-width="1.6" stroke-linecap="round"
                              stroke-linejoin="round" />
                      </g>
                      <defs>
                          <clipPath id="clip0_497_1336">
                              <rect width="18" height="18" fill="white" />
                          </clipPath>
                      </defs>
                  </svg>
              </button>
          </form>
      </div>
  </div>
  <div class="tg-blog-categories tg-blog-sidebar-box mb-40">
      <h5 class="tg-blog-sidebar-title mb-5">{{ __('translate.Categories') }}</h5>
      <div class="tg-blog-categories-list">
          <ul>
              @foreach ($blog_categories as $blog_category)
                  <li>
                      <span>
                          <a href="{{ route('blogs', ['category' => $blog_category->id]) }}">
                              {{ $blog_category?->name }}
                          </a>
                      </span>
                      <span>
                          <a href="{{ route('blogs', ['category' => $blog_category->id]) }}">
                              ({{ $blog_category->total_blog }})
                          </a>
                      </span>
                  </li>
              @endforeach
          </ul>
      </div>
  </div>
  <div class="tg-blog-post tg-blog-sidebar-box mb-40">
      <h5 class="tg-blog-sidebar-title mb-25">{{ __('translate.Recent Posts') }}</h5>
      @foreach ($latest_blogs as $latest_blog)
          <div class="tg-blog-post-item d-flex align-items-center mb-30">
              <div class="tg-blog-post-thumb mr-15">
                  <img src="{{ asset($latest_blog->image) }}" alt="{{ $latest_blog?->title }}">
              </div>
              <div class="tg-blog-post-content w-100">
                  <h4 class="tg-blog-post-title mb-5"><a
                          href="{{ route('blog', $latest_blog->slug) }}">
                          {{ Str::limit($latest_blog?->title, 28, '') }}
                      </a></h4>
                  <span class="tg-blog-post-date">
                      <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M9.76501 0.777832V3.26675M4.23413 0.777832V3.26675M0.777344 5.75554H13.2218M2.16006 2.02217H11.8391C12.6027 2.02217 13.2218 2.57933 13.2218 3.26662V11.9778C13.2218 12.6651 12.6027 13.2223 11.8391 13.2223H2.16006C1.39641 13.2223 0.777344 12.6651 0.777344 11.9778V3.26662C0.777344 2.57933 1.39641 2.02217 2.16006 2.02217Z"
                              stroke="#560CE3" stroke-width="0.977778" stroke-linecap="round"
                              stroke-linejoin="round" />
                      </svg>
                      {{ $latest_blog->created_at->format('d-m-Y') }}
                  </span>
              </div>
          </div>
      @endforeach
  </div>
  <div class="tg-blog-sidebar-tag tg-blog-sidebar-box">
      <h5 class="tg-blog-sidebar-title mb-25">{{ __('translate.Tags') }}</h5>
      <div class="tg-blog-sidebar-tag-list">
          <ul>
              @foreach ($tags_array as $tag_item)
              <li><a href="{{ route('blogs', ['search' => $tag_item]) }}">{{ $tag_item }}</a></li>
              @endforeach
          </ul>
      </div>
  </div>
</div>
