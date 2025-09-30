<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Ecommerce\Entities\Cart;
use Modules\Ecommerce\Entities\ShippingMethod;
use Auth, Stripe, Mail, Str, Exception, Redirect;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\SeoSetting\App\Models\SeoSetting;
use Modules\Currency\App\Models\Currency;

class CheckoutController extends Controller
{
    public $payment_setting;

    public function __construct()
    {
        $payment_data = PaymentGateway::all();


            $this->payment_setting = array();

            foreach($payment_data as $data_item){
                $payment_setting[$data_item->key] = $data_item->value;
            }

            $this->payment_setting  = (object) $payment_setting;
    }

    public function index()
    {
        if(auth()->user()){
            $seo_setting = SeoSetting::first();
            $methods = ShippingMethod::active()->get();
            $carts = Cart::where('user_id', auth()->user()->id)->get();
            $sub_total = $carts->sum(fn($cart) => $cart->product->finalPrice * $cart->quantity);
            $paypal = PaymentGateway::where(['key' => 'paypal_currency_id'])->first();
            $stripe = PaymentGateway::where(['key' => 'stripe_currency_id'])->first();
            $paypal_status = PaymentGateway::where('key', 'paypal_status')->value('value');

            $breadcrumb_title = trans('translate.Checkout Page');

            return view('ecommerce::frontend.checkout', compact('carts','seo_setting','methods','sub_total', 'paypal', 'stripe', 'paypal_status', 'breadcrumb_title'));
        }else{
            $notification = trans('translate.First You Need To login This Checkout');
            $notification = array('messege' => $notification, 'alert-type' => 'error');
            return redirect()->route('user.login')->with($notification);
        }

    }

    public function processToPayment(Request $request) {

        if (env('APP_MODE') == 'DEMO') {
            $notification = trans('translate.This Is Demo Version. You Can Not Change Anything');
            $notification = array('messege' => $notification, 'alert-type' => 'error');
            return redirect()->back()->with($notification);
        }

        $rules = [
            'shipping_method_id' => 'required',
            'address' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ];
        $customMessages = [
            'shipping_method_id.required' => trans('translate.Shipping Method is required'),
            'address.required' => trans('translate.Address is required'),
            'name.required' => trans('translate.Name is required'),
            'email.required' => trans('translate.Email is required'),
            'phone.required' => trans('translate.Phone is required'),
        ];

        $request->validate($rules, $customMessages);

        $customerDetails = json_encode([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Prepare order data
        $orderData = [
            'subtotal' => $request->subtotal,
            'shipping_charge' => $request->shipping_charge,
            'total' => $request->total,
            'shipping_method_id' => $request->shipping_method_id,
            'address' => json_decode($customerDetails),
        ];

        session([
            'orderData' => $orderData,
        ]);

        // $orderData = session()->get('orderData');
        $breadcrumb = 'Hi';
        $seo_setting = SeoSetting::first();
        $methods = ShippingMethod::active()->get();
        $carts = Cart::where('user_id', auth()->user()->id)->get();
        $sub_total = $carts->sum(fn($cart) => $cart->product->finalPrice * $cart->quantity);
        $paypal = PaymentGateway::where(['key' => 'paypal_currency_id'])->first();
        $paypalStatus = PaymentGateway::where(['key' => 'paypal_status'])->value('value');
        $stripe = PaymentGateway::where(['key' => 'stripe_currency_id'])->first();
        $razorpay = PaymentGateway::where(['key' => 'razorpay_currency_id'])->first();
        $flutterwave = PaymentGateway::where(['key' => 'flutterwave_currency_id'])->first();
        $paystack = PaymentGateway::where(['key' => 'paystack_currency_id'])->first();
        $mollie = $paystack;
        $instamojo = PaymentGateway::where(['key' => 'instamojo_currency_id'])->first();
        $bank = PaymentGateway::where(['key' => 'bank_status'])->first();
        $user = Auth::guard('web')->user();

        $total = $request->total;
        $shipping_charge = $request->shipping_charge;
        $payment_setting = $this->payment_setting;
        $payable_amount = $request->total;

        $razorpay_currency = Currency::findOrFail($this->payment_setting->razorpay_currency_id);
        $flutterwave_currency = Currency::findOrFail($this->payment_setting->flutterwave_currency_id);
        $paystack_currency = Currency::findOrFail($this->payment_setting->paystack_currency_id);

        $seo_setting = SeoSetting::first();
        $breadcrumb_title = trans('translate.Payment Page');

        return view('ecommerce::frontend.payment', compact('breadcrumb','paypalStatus','user','total','shipping_charge','carts','seo_setting','methods','sub_total', 'paypal', 'stripe', 'razorpay', 'flutterwave', 'paystack', 'mollie', 'instamojo', 'bank','payment_setting','razorpay_currency','payable_amount','flutterwave_currency','paystack_currency', 'seo_setting', 'breadcrumb_title'));
    }

}
