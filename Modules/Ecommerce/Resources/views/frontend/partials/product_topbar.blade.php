<div class="tg-listing-box-filter tg-shop-product-filter mb-25">
    <div class="row align-items-center">
        <div class="col-lg-5 col-md-5 mb-15">
            <div class="tg-listing-box-number-found">
                <span>Showing {{ $products->firstItem() }} – {{ $products->lastItem() }} of {{ $products->total() }}
                    results</span>
            </div>
        </div>
        <div class="col-lg-7 col-md-7 mb-15">
            <div class="tg-listing-box-view-type d-flex justify-content-end align-items-center">
                <div class="tg-listing-select-price ml-10">
                    <form action="{{ route('product.shop') }}" method="GET">
                        <select class="select" name="sort" onchange="this.form.submit()">
                            <option @selected(request('sort') == 'default') value="default">Default Sorting</option>
                            <option @selected(request('sort') == 'popularity') value="popularity">Sort popularity</option>
                            <option @selected(request('sort') == 'rating') value="rating">Sort rating</option>
                            <option @selected(request('sort') == 'latest') value="latest">Sort latest</option>
                            <option @selected(request('sort') == 'trending') value="trending">Sort Trending</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
