<div class="cart-item-wrapper">
    @forelse ($items as $item)
        <div class="cart-content-wrap d-flex align-items-center justify-content-between">
            <div class="cart-img-info d-flex align-items-center">
                <div class="cart-thumb">
                    <a href="shop.html"> <img src="{{ asset($item['image'] ?? '') }}" alt=""></a>
                </div>
                <div class="cart-content">
                    <h5 class="cart-title"><a href="{{ route('product.view', $item['slug']) }}">{{ $item['title'] }}</a>
                    </h5>
                    <span> {!! $item['price_display'] !!} </span>
                </div>
            </div>
            <div class="cart-del-icon" onclick="removeFromCart({{ $item['cart_id'] }})">
                <span><i class="fa-light fa-trash-can"></i></span>
            </div>
        </div>
    @empty
        <div class="text-center">{{ __('translate.Cart is empty') }}</div>
    @endforelse
</div>

<div class="cart-total-price d-flex align-items-center justify-content-between">
    <span>{{ __('translate.Total') }}:</span>
    <span>{{ currency($total) }}</span>
</div>
<div class="minicart-btn">
    <a class="cart-btn mb-10" href="{{ route('cart.cart') }}"><span>{{ __('translate.Shopping Cart') }}</span></a>
    <a class="cart-btn cart-btn-black"
        href="{{ route('checkout.index') }}"><span>{{ __('translate.Checkout') }}</span></a>
</div>
