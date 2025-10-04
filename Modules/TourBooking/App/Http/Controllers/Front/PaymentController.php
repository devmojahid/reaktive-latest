<?php

namespace Modules\TourBooking\App\Http\Controllers\Front;

use Exception;
use Stripe\Charge;
use Stripe\Stripe;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Currency\App\Models\Currency;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\Coupon\App\Http\Controllers\CouponController;
use Modules\TourBooking\App\Models\Booking;
use Modules\TourBooking\App\Models\Service;

class PaymentController extends Controller
{
    public $payment_setting;

    public function __construct()
    {
        $payment_data = PaymentGateway::all();
        $this->payment_setting = [];
        foreach ($payment_data as $data_item) {
            $payment_setting[$data_item->key] = $data_item->value;
        }
        $this->payment_setting  = (object) $payment_setting;
    }

    /* =======================
     * GATEWAYS
     * ======================= */

    public function stripe_payment(Request $request)
    {
        $auth_user = Auth::guard('web')->user();
        $calculate_price = $this->calculate_price();
        $stripe_currency = Currency::findOrFail($this->payment_setting->stripe_currency_id);

        // curs aplicat corect
        $payable_amount = round((($calculate_price['total_amount'] ?? 0) * ($stripe_currency->currency_rate ?? 1)), 2);

        Stripe::setApiKey($this->payment_setting->stripe_secret);
        $customerInfo = $this->customerInfo($request);

        try {
            $result = Charge::create([
                "amount"      => (int) round($payable_amount * 100),
                "currency"    => $stripe_currency->currency_code ?? 'USD',
                "source"      => $request->stripeToken,
                "description" => env('APP_NAME'),
            ]);
        } catch (Exception $ex) {
            Log::info('Stripe payment : ' . $ex->getMessage());
            return redirect()->back()->with([
                'message' => trans('translate.Something went wrong, please try again') . ' ' . $ex->getMessage(),
                'alert-type' => 'error'
            ]);
        }

        $this->create_order($auth_user, 'Stripe', 'success', $result->balance_transaction, $customerInfo);

        return redirect()->route('user.bookings.index')->with([
            'message' => trans('translate.Your payment has been made successful. Thanks for your new purchase'),
            'alert-type' => 'success'
        ]);
    }

    public function paypal_payment(Request $request)
    {
        $this->setCustomerInfoSession($request);

        $calculate_price = $this->calculate_price();
        $paypal_currency = Currency::findOrFail($this->payment_setting->paypal_currency_id);
        $payable_amount  = round((($calculate_price['total_amount'] ?? 0) * ($paypal_currency->currency_rate ?? 1)), 2);

        config(['paypal.mode' => $this->payment_setting->paypal_account_mode]);

        if ($this->payment_setting->paypal_account_mode == 'sandbox') {
            config(['paypal.sandbox.client_id' => $this->payment_setting->paypal_client_id]);
            config(['paypal.sandbox.client_secret' => $this->payment_setting->paypal_secret_key]);
        } else {
            config(['paypal.live.client_id' => $this->payment_setting->paypal_client_id]);
            config(['paypal.live.client_secret' => $this->payment_setting->paypal_secret_key]);
            config(['paypal.live.app_id' => 'APP-80W284485P519543T']);
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('payment.paypal-success-payment'),
                "cancel_url" => route('payment.paypal-faild-payment'),
            ],
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $paypal_currency->currency_code,
                    "value" => $payable_amount
                ]
            ]]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
        }

        return redirect()->back()->with([
            'message' => trans('translate.Something went wrong, please try again'),
            'alert-type' => 'error'
        ]);
    }

    public function paypal_success_payment(Request $request)
    {
        $customerInfo = Session::get('customer_info');

        config(['paypal.mode' => $this->payment_setting->paypal_account_mode]);
        if ($this->payment_setting->paypal_account_mode == 'sandbox') {
            config(['paypal.sandbox.client_id' => $this->payment_setting->paypal_client_id]);
            config(['paypal.sandbox.client_secret' => $this->payment_setting->paypal_secret_key]);
        } else {
            config(['paypal.live.client_id' => $this->payment_setting->paypal_client_id]);
            config(['paypal.live.client_secret' => $this->payment_setting->paypal_secret_key]);
            config(['paypal.live.app_id' => 'APP-80W284485P519543T']);
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $auth_user = Auth::guard('web')->user();
            $this->create_order($auth_user, 'Paypal', 'success', $request->PayerID, $customerInfo);

            return redirect()->route('user.bookings.index')->with([
                'message' => trans('translate.Your payment has been made successful. Thanks for your new purchase'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->back()->with([
            'message' => trans('translate.Something went wrong, please try again'),
            'alert-type' => 'error'
        ]);
    }

    public function paypal_faild_payment(Request $request)
    {
        return redirect()->back()->with([
            'message' => trans('translate.Something went wrong, please try again'),
            'alert-type' => 'error'
        ]);
    }

    public function razorpay_payment(Request $request)
    {
        $input = $request->all();
        $customerInfo = $this->customerInfo($request);

        $api = new Api($this->payment_setting->razorpay_key, $this->payment_setting->razorpay_secret);
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(['amount' => $payment['amount']]);
                $payId = $response->id;

                $auth_user = Auth::guard('web')->user();
                $this->create_order($auth_user, 'Razorpay', 'success', $payId, $customerInfo);

                return redirect()->route('user.bookings.index')->with([
                    'message' => trans('translate.Your payment has been made successful. Thanks for your new purchase'),
                    'alert-type' => 'success'
                ]);
            } catch (Exception $e) {
                Log::info('Razorpay payment : ' . $e->getMessage());
            }
        }

        return redirect()->back()->with([
            'message' => trans('translate.Something went wrong, please try again'),
            'alert-type' => 'error'
        ]);
    }

    public function flutterwave_payment(Request $request)
    {
        $customerInfo = $this->customerInfo($request);

        $curl = curl_init();
        $tnx_id = $request->tnx_id;
        $url = "https://api.flutterwave.com/v3/transactions/$tnx_id/verify";
        $token = $this->payment_setting->flutterwave_secret_key;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $token"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        if ($response->status == 'success') {
            $auth_user = Auth::guard('web')->user();
            $this->create_order($auth_user, 'Flutterwave', 'success', $tnx_id, $customerInfo);

            return response()->json([
                'status' => 'success',
                'message' => trans('translate.Your payment has been made successful. Thanks for your new purchase')
            ]);
        }

        return response()->json([
            'status' => 'faild',
            'message' => trans('translate.Something went wrong, please try again')
        ]);
    }

    public function paystack_payment(Request $request)
    {
        $customerInfo = $this->customerInfo($request);

        $reference = $request->reference;
        $transaction = $request->tnx_id;
        $secret_key = $this->payment_setting->paystack_secret_key;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $secret_key",
                "Cache-Control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $final_data = json_decode($response);
        if ($final_data->status == true) {
            $auth_user = Auth::guard('web')->user();
            $this->create_order($auth_user, 'Paystack', 'success', $transaction, $customerInfo);

            return response()->json([
                'status' => 'success',
                'message' => trans('translate.Your payment has been made successful. Thanks for your new purchase')
            ]);
        }

        return response()->json([
            'status' => 'faild',
            'message' => trans('translate.Something went wrong, please try again')
        ]);
    }

    public function mollie_payment(Request $request)
    {
        if (env('APP_MODE') == 'DEMO') {
            return redirect()->back()->with([
                'message' => trans('translate.This Is Demo Version. You Can Not Change Anything'),
                'alert-type' => 'error'
            ]);
        }

        $this->customerInfo($request);
        $this->setCustomerInfoSession($request);

        try {
            $calculate_price = $this->calculate_price();
            $mollie_currency = Currency::findOrFail($this->payment_setting->mollie_currency_id);

            $price = ($calculate_price['total_amount'] ?? 0) * ($mollie_currency->currency_rate ?? 1);
            $price = sprintf('%0.2f', $price);

            $mollie_api_key = $this->payment_setting->mollie_key;
            $currency = strtoupper($mollie_currency->currency_code);

            Mollie::api()->setApiKey($mollie_api_key);

            $payment = Mollie::api()->payments()->create([
                'amount' => [
                    'currency' => $currency,
                    'value' => '' . $price . '',
                ],
                'description' => env('APP_NAME'),
                'redirectUrl' => route('payment.mollie-callback'),
            ]);

            $payment = Mollie::api()->payments()->get($payment->id);
            Session::put('payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl(), 303);
        } catch (Exception $e) {
            Log::info('Mollie payment : ' . $e->getMessage());
            return redirect()->back()->with([
                'message' => trans('translate.Please provide valid mollie api key'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function mollie_callback(Request $request)
    {
        $customerInfo = Session::get('customer_info');

        $mollie_api_key = $this->payment_setting->mollie_key;
        Mollie::api()->setApiKey($mollie_api_key);
        $payment = Mollie::api()->payments->get(session()->get('payment_id'));

        if ($payment->isPaid()) {
            $auth_user = Auth::guard('web')->user();
            $this->create_order($auth_user, 'Mollie', 'success', session()->get('payment_id'), $customerInfo);

            return redirect()->route('user.bookings.index')->with([
                'message' => trans('translate.Your payment has been made successful. Thanks for your new purchase'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->back()->with([
            'message' => trans('translate.Something went wrong, please try again'),
            'alert-type' => 'error'
        ]);
    }

    public function instamojo_payment(Request $request)
    {
        if (env('APP_MODE') == 'DEMO') {
            return redirect()->back()->with([
                'message' => trans('translate.This Is Demo Version. You Can Not Change Anything'),
                'alert-type' => 'error'
            ]);
        }

        $this->customerInfo($request);
        $this->setCustomerInfoSession($request);

        $calculate_price = $this->calculate_price();
        $instamojo_currency = Currency::findOrFail($this->payment_setting->instamojo_currency_id);
        $price = ($calculate_price['total_amount'] ?? 0) * ($instamojo_currency->currency_rate ?? 1);
        $price = round($price, 2);

        try {
            $environment = $this->payment_setting->instamojo_account_mode;
            $api_key = $this->payment_setting->instamojo_api_key;
            $auth_token = $this->payment_setting->instamojo_auth_token;

            $url = $environment == 'Sandbox'
                ? 'https://test.instamojo.com/api/1.1/'
                : 'https://www.instamojo.com/api/1.1/';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . 'payment-requests/');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "X-Api-Key:$api_key",
                "X-Auth-Token:$auth_token"
            ]);
            $payload = [
                'purpose' => env("APP_NAME"),
                'amount' => $price,
                'phone' => '918160651749',
                'buyer_name' => Auth::user()->name,
                'redirect_url' => route('payment.instamojo-callback'),
                'send_email' => true,
                'webhook' => 'http://www.example.com/webhook/',
                'send_sms' => true,
                'email' => Auth::user()->email,
                'allow_repeated_payments' => false
            ];
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response);
            return redirect($response->payment_request->longurl);
        } catch (Exception $e) {
            Log::info('Instamojo payment : ' . $e->getMessage());
            return redirect()->back()->with([
                'message' => trans('translate.Something went wrong, please try again'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function instamojo_callback(Request $request)
    {
        $customerInfo = Session::get('customer_info');

        $environment = $this->payment_setting->instamojo_account_mode;
        $api_key = $this->payment_setting->instamojo_api_key;
        $auth_token = $this->payment_setting->instamojo_auth_token;

        $url = $environment == 'Sandbox'
            ? 'https://test.instamojo.com/api/1.1/'
            : 'https://www.instamojo.com/api/1.1/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . 'payments/' . $request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Api-Key:$api_key",
            "X-Auth-Token:$auth_token"
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return redirect()->back()->with([
                'message' => trans('translate.Something went wrong, please try again'),
                'alert-type' => 'error'
            ]);
        }

        $data = json_decode($response);

        if ($data->success == true && $data->payment->status == 'Credit') {
            $auth_user = Auth::guard('web')->user();
            $this->create_order($auth_user, 'Instamojo', 'success', $request->get('payment_id'), $customerInfo);

            return redirect()->route('user.bookings.index')->with([
                'message' => trans('translate.Your payment has been made successful. Thanks for your new purchase'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->back()->with([
            'message' => trans('translate.Something went wrong, please try again'),
            'alert-type' => 'error'
        ]);
    }

    public function bank_payment(Request $request)
    {
        $request->validate([
            'tnx_info' => 'required|max:255'
        ], [
            'tnx_info.required' => trans('translate.Transaction field is required')
        ]);

        $customerInfo = $this->customerInfo($request);
        $auth_user = Auth::guard('web')->user();

        $this->create_order($auth_user, 'Bank Payment', 'pending', $request->tnx_info, $customerInfo);

        return redirect()->route('user.bookings.index')->with([
            'message' => trans('translate.Your payment has been made. please wait for admin payment approval'),
            'alert-type' => 'success'
        ]);
    }

    /* =======================
     * ORDER CREATION
     * ======================= */

    public function create_order($user, $payment_method, $payment_status, $transaction_id, $customerInfo = [])
    {
        $calculate_price = $this->calculate_price();
        $payment_cart = session()->get('payment_cart');
        $service = Service::findOrFail($payment_cart['service_id']);

        // dacă avem age_quantities, ignorăm person_count/child_count
        if (!empty($payment_cart['age_quantities']) && is_array($payment_cart['age_quantities'])) {
            $adults = 0;
            $children = 0;

            $ageQuantities = $payment_cart['age_quantities'];
            $ageConfig     = $payment_cart['age_config'] ?? [];

            foreach ($ageQuantities as $k => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;
                $key = strtolower((string) $k);

                if (str_contains($key, 'adult') || $key === 'person' || str_contains($key, 'people')) {
                    $adults += $qty;
                    continue;
                }
                if (str_contains($key, 'child') || str_contains($key, 'children') || str_contains($key, 'kid') ||
                    str_contains($key, 'infant') || str_contains($key, 'teen') || str_contains($key, 'youth') || str_contains($key, 'baby')) {
                    $children += $qty;
                    continue;
                }

                // fallback interval vârstă
                if (isset($ageConfig[$k]) && (isset($ageConfig[$k]['min_age']) || isset($ageConfig[$k]['max_age']))) {
                    $min = (int)($ageConfig[$k]['min_age'] ?? 0);
                    if ($min >= 18) $adults += $qty; else $children += $qty;
                } else {
                    $children += $qty;
                }
            }
        } else {
            $adults   = (int)($payment_cart['person_count'] ?? 0);
            $children = (int)($payment_cart['child_count'] ?? 0);
        }

        // reconstruim breakdown dacă nu a fost pus în sesiune
        $age_breakdown = $payment_cart['age_breakdown'] ?? null;
        if (empty($age_breakdown) && !empty($payment_cart['age_quantities']) && !empty($payment_cart['age_config'])) {
            $age_breakdown = [];
            foreach ($payment_cart['age_quantities'] as $key => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;
                $cfg   = $payment_cart['age_config'][$key] ?? [];
                $label = $cfg['label'] ?? ucfirst((string)$key);
                $price = (float)($cfg['price'] ?? 0);
                $line  = $qty * $price;
                $age_breakdown[] = [
                    'key'   => $key,
                    'label' => $label,
                    'qty'   => $qty,
                    'price' => $price,
                    'line'  => $line,
                ];
            }
        }

        $order = new Booking();
        $order->is_per_person  = $service->is_per_person ?? 0;
        $order->booking_code   = uniqid();
        $order->service_id     = $service->id;
        $order->user_id        = $user->id;
        $order->check_in_date  = $payment_cart['check_in_date'];
        $order->check_out_date = $payment_cart['check_out_date'];
        $order->check_in_time  = $payment_cart['check_in_time'];
        $order->check_out_time = $payment_cart['check_out_time'];
        $order->adults         = $adults;
        $order->children       = $children;

        // prețuri de referință (fallback)
        $order->service_price  = $service?->discount_price ?? $service?->full_price ?? 0;
        $order->adult_price    = $service->price_per_person ?? 0;
        $order->child_price    = $service->child_price ?? 0;

        $order->extra_charges  = $payment_cart['extra_charges'] ?? 0;
        $order->extra_services = $payment_cart['extra_services'] ?? [];

        // cupon + sume
        $order->discount_amount = $calculate_price['coupon_amount'] ?? 0;
        $order->subtotal        = $calculate_price['sub_total_amount'] ?? 0;
        $order->total           = $calculate_price['total_amount'] ?? 0;
        $order->paid_amount     = ($payment_status === 'success') ? ($calculate_price['total_amount'] ?? 0) : 0;
        $order->due_amount      = max(0, ($order->total - $order->paid_amount));

        $order->payment_method  = $payment_method;
        $order->booking_status  = $payment_status === 'success' ? 'success' : 'pending';
        $order->payment_status  = $payment_status;

        $order->customer_name    = $customerInfo['customer_name'] ?? '';
        $order->customer_email   = $customerInfo['customer_email'] ?? '';
        $order->customer_phone   = $customerInfo['customer_phone'] ?? '';
        $order->customer_address = $customerInfo['customer_address'] ?? '';

        // persistăm distribuția pe vârste
        $order->age_quantities = $payment_cart['age_quantities'] ?? null;
        $order->age_config     = $payment_cart['age_config'] ?? null;
        $order->age_breakdown  = $age_breakdown;

        // Store pickup point information
        $order->pickup_point_id = $payment_cart['pickup_point_id'] ?? null;
        $order->pickup_charge = $payment_cart['pickup_charge'] ?? 0;
        $order->pickup_point_name = $payment_cart['pickup_point_name'] ?? null;

        $order->save();

        // istoric cupon
        if (Session::get('coupon_code') && Session::get('offer_percentage')) {
            $coupon_history = new CouponController();
            $coupon_history->store_coupon_history($user->id, $calculate_price['coupon_amount'] ?? 0, Session::get('coupon_code'));
        }

        // curățăm sesiunea
        session()->forget('payment_cart');
        session()->forget('customer_info');
        session()->forget('coupon_code');
        session()->forget('offer_percentage');

        return $order;
    }

    /* =======================
     * HELPERS
     * ======================= */

    public function calculate_price()
    {
        $payment_cart = session()->get('payment_cart', []);

        // "total" din sesiune = subtotal (înainte de cupon)
        $sub_total_amount = (float)($payment_cart['total'] ?? 0);
        $coupon_amount = 0;

        if (Session::get('coupon_code') && Session::get('offer_percentage')) {
            $offer_percentage = (float) Session::get('offer_percentage');
            $coupon_amount = ($offer_percentage / 100) * $sub_total_amount;
        }

        $total_amount = $sub_total_amount - $coupon_amount;

        return [
            'sub_total_amount' => $sub_total_amount,
            'coupon_amount'    => $coupon_amount,
            'total_amount'     => $total_amount,
        ];
    }

    public function customerInfo($request)
    {
        $auth_user = Auth::guard('web')->user();

        return [
            'customer_name'    => $request->customer_name    ?? ($auth_user->name    ?? ''),
            'customer_email'   => $request->customer_email   ?? ($auth_user->email   ?? ''),
            'customer_phone'   => $request->customer_phone   ?? ($auth_user->phone   ?? ''),
            'customer_address' => $request->customer_address ?? ($auth_user->address ?? '')
        ];
    }

    public function setCustomerInfoSession($request)
    {
        session()->forget('customer_info');

        $auth_user = Auth::guard('web')->user();

        session()->put('customer_info', [
            'customer_name'    => $request->customer_name    ?? ($auth_user->name    ?? ''),
            'customer_email'   => $request->customer_email   ?? ($auth_user->email   ?? ''),
            'customer_phone'   => $request->customer_phone   ?? ($auth_user->phone   ?? ''),
            'customer_address' => $request->customer_address ?? ($auth_user->address ?? '')
        ]);
    }
}
