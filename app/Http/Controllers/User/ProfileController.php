<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Auth, File, Image, Str, Hash;
use App\Http\Controllers\Controller;
use Modules\Coupon\App\Models\Coupon;
use Modules\Wishlist\App\Models\Wishlist;
use App\Http\Requests\PasswordChangeRequest;
use Modules\Coupon\App\Models\CouponHistory;
use App\Http\Requests\BecomeAgencyRequest;
use App\Http\Requests\EditStudentProfileRequest;
use Modules\SupportTicket\App\Models\SupportTicket;
use Modules\SupportTicket\App\Models\MessageDocument;
use Modules\PaymentWithdraw\App\Models\SellerWithdraw;
use Modules\SupportTicket\App\Models\SupportTicketMessage;
use Modules\TourBooking\App\Models\Booking;

class ProfileController extends Controller
{
    public function dashboard()
    {

        $user = Auth::guard('web')->user();

        $wishlists = Wishlist::where('user_id', $user->id)->count();

        $support_tickets = SupportTicket::where('author_id', $user->id)->where('admin_type', 'admin')->latest()->count();

        $bookings = Booking::with(['service:id,title,location'])
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->take(15)
            ->get();

        $booking =  Booking::where('user_id', auth()->user()->id)
            ->where('booking_status', 'confirmed');

        $total_booking = $booking->count();
        $total_transaction = Booking::where('user_id', auth()->user()->id)
            ->where('payment_status', 'success')
            ->sum('total');


        return view('user.dashboard', [
            'wishlists' => $wishlists,
            'support_tickets' => $support_tickets,
            'bookings' => $bookings,
            'total_booking' => $total_booking,
            'total_transaction' => $total_transaction
        ]);
    }

    public function edit_profile()
    {
        $user = Auth::guard('web')->user();

        return view('user.edit_profile', ['user' => $user]);
    }

    public function update_profile(EditStudentProfileRequest $request)
    {

        $user = Auth::guard('web')->user();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->gender = $request->gender;
        $user->save();

        if ($request->file('image')) {
            $old_image = $user->image;
            $user_image = $request->image;
            $extention = $user_image->getClientOriginalExtension();
            $image_name = Str::slug($user->name) . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $extention;
            $image_name = 'uploads/custom-images/' . $image_name;
            Image::make($user_image)->save(public_path() . '/' . $image_name);
            $user->image = $image_name;
            $user->save();
            if ($old_image) {
                if (File::exists(public_path() . '/' . $old_image)) unlink(public_path() . '/' . $old_image);
            }
        }

        $notify_message = trans('translate.Updated successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }

    public function change_password()
    {
        return view('user.change_password');
    }

    public function update_password(PasswordChangeRequest $request)
    {

        $user = Auth::guard('web')->user();

        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            $notify_message = trans('translate.Password changed successfully');
            $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
            return redirect()->back()->with($notify_message);
        } else {
            $notify_message = trans('translate.Current password does not match');
            $notify_message = array('message' => $notify_message, 'alert-type' => 'error');
            return redirect()->back()->with($notify_message);
        }
    }


    public function create_agency(Request $request)
    {

        $user = Auth::guard('web')->user();

        return view('user.create_agency', [
            'user' => $user
        ]);
    }

    public function agency_application(BecomeAgencyRequest $request)
    {
        $user = Auth::guard('web')->user();

        $user->agency_name = $request->agency_name;
        $user->agency_slug = $request->agency_slug;
        $user->about_me = $request->about_me;
        $user->country = $request->country;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->address = $request->address;
        $user->facebook = $request->facebook;
        $user->linkedin = $request->linkedin;
        $user->twitter = $request->twitter;
        $user->instagram = $request->instagram;
        $user->website = $request->website;
        $user->location_map = $request->location_map;
        $user->instructor_joining_request = 'pending';
        $user->save();

        if ($request->hasFile('agency_logo')) {
            $file = $request->file('agency_logo');
            $imageName = 'uploads/custom-images/' . Str::slug($user->agency_name) . '-' . now()->format('YmdHis') . '-' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            Image::make($file)->save(public_path($imageName));

            $user->agency_logo = $imageName;
            $user->save();
        }

        $notify_message = trans('translate.Agency joining request send to admin. please awaiting for approval');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }

    public function account_delete()
    {
        return view('user.account_delete');
    }

    public function confirm_account_delete(Request $request)
    {

        $user = Auth::guard('web')->user();

        $request->validate([
            'current_password' => 'required'
        ], [
            'current_password.required' => trans('translate.Current password is required')
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            $notify_message = trans('translate.Current password does not match');
            $notify_message = array('message' => $notify_message, 'alert-type' => 'error');
            return redirect()->back()->with($notify_message);
        }

        $user_image = $user->image;

        if ($user_image) {
            if (File::exists(public_path() . '/' . $user_image)) unlink(public_path() . '/' . $user_image);
        }

        $user_id = $user->id;
        Coupon::where('seller_id', $user_id)->delete();
        CouponHistory::where('seller_id', $user_id)->delete();
        CouponHistory::where('buyer_id', $user_id)->delete();

        SellerWithdraw::where('seller_id', $user_id)->delete();
        Wishlist::where('user_id', $user_id)->delete();

        $support_tickets = SupportTicket::where('author_id', $user->id)->latest()->get();

        foreach ($support_tickets as $support_ticket) {
            $ticket_messages = SupportTicketMessage::with('documents')->where('support_ticket_id', $support_ticket->id)->get();

            foreach ($ticket_messages as $ticket_message) {

                $documents = MessageDocument::where('message_id', $ticket_message->id)->where('model_name', 'SupportTicketMessage')->get();
                foreach ($documents as $document) {
                    $exist_file_name = $document->file_name;
                    if ($exist_file_name) {
                        if (File::exists(public_path('uploads/custom-images') . '/' . $exist_file_name)) unlink(public_path('uploads/custom-images') . '/' . $exist_file_name);
                    }

                    $document->delete();
                }

                $ticket_message->delete();
            }

            $support_ticket->delete();
        }



        $user->delete();

        Auth::guard('web')->logout();

        $notify_message = trans('translate.Your account deleted successful');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->route('user.login')->with($notify_message);
    }
}
