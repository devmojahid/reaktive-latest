<input type="hidden" name="customer_name" class="form_customer_name" value="{{ auth()->user()->name ?? '' }}">
<input type="hidden" name="customer_email" class="form_customer_email" value="{{ auth()->user()->email ?? '' }}">
<input type="hidden" name="customer_phone" class="form_customer_phone" value="{{ auth()->user()->phone ?? '' }}">
<input type="hidden" name="customer_address" class="form_customer_address" value="{{ auth()->user()->address ?? '' }}">
