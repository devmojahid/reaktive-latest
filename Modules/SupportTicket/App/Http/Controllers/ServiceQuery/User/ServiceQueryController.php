<?php

namespace Modules\SupportTicket\App\Http\Controllers\ServiceQuery\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Course\App\Models\Course;
use Modules\Course\App\Models\CourseEnrollmentList;
use Modules\SupportTicket\App\Models\SupportTicket;
use Modules\SupportTicket\App\Models\MessageDocument;
use Modules\SupportTicket\App\Models\SupportTicketMessage;
use Modules\TourBooking\App\Models\Booking;
use Modules\TourBooking\App\Models\Service;

class ServiceQueryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $user = Auth::guard('web')->user();

        $support_tickets = SupportTicket::with('service')
            ->where('author_id', $user->id)
            ->where('admin_type', 'instructor')
            ->latest()
            ->get();

        return view('supportticket::servicequery.student.index', [
            'support_tickets' => $support_tickets
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $user = Auth::guard('web')->user();

        $enrollments = CourseEnrollmentList::with('course_enrollment', 'course')->whereHas('course_enrollment', function ($query) use ($user) {
            $query->where('payment_status', 'success')->where('student_id', $user->id);;
        })->pluck('course_id');

        $courses = Course::where(['status' => 'enable', 'approved_by_admin' => 'approved'])->whereIn('id', $enrollments)->get();

        $confirm_booking = Booking::select('id', 'service_id', 'booking_status')
            ->where('user_id', $user->id)
            ->with('service:id,title')
            ->where('booking_status', 'confirmed')
            ->get();

        return view('supportticket::servicequery.student.create', [
            'courses' => $courses,
            'confirm_booking' => $confirm_booking
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate(
            [
                'subject' => 'required|max:255',
                'service_id' => 'required|exists:services,id',
                'message' => 'required',
            ],
            [
                'subject.required' => trans('translate.Subject is required'),
                'message.required' => trans('translate.Message is required'),
            ]
        );

        $user = Auth::guard('web')->user();
        $support_ticket = new SupportTicket();
        $support_ticket->author_id = $user->id;
        $support_ticket->subject = $request->subject;
        $support_ticket->service_id = $request->service_id;
        $support_ticket->admin_type = 'instructor';
        $support_ticket->ticket_id = substr(rand(0, time()), 0, 10);
        $support_ticket->save();

        $ticket_message = new SupportTicketMessage();
        $ticket_message->support_ticket_id = $support_ticket->id;
        $ticket_message->message = $request->message;
        $ticket_message->message_author_id = $user->id;
        $ticket_message->save();


        if ($request->hasFile('documents')) {
            foreach ($request->documents as $index => $request_file) {
                $extention = $request_file->getClientOriginalExtension();
                $file_name = 'agency-support-' . time() . $index . '.' . $extention;
                $destinationPath = public_path('uploads/custom-images/');
                $request_file->move($destinationPath, $file_name);

                $document = new MessageDocument();
                $document->message_id = $ticket_message->id;
                $document->file_name = $file_name;
                $document->model_name = 'SupportTicketMessage';
                $document->save();
            }
        }


        $notify_message = trans('translate.Ticket created successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->route('user.agency-support.show', $support_ticket->ticket_id)->with($notify_message);
    }

    /**
     * Show the specified resource.
     */
    public function show($ticket_id)
    {

        $user = Auth::guard('web')->user();

        $support_ticket = SupportTicket::where('ticket_id', $ticket_id)
            ->where('admin_type', 'instructor')
            ->where('author_id', $user->id)
            ->firstOrFail();

        $ticket_messages = SupportTicketMessage::with('documents')->where('support_ticket_id', $support_ticket->id)->get();

        $last_message = SupportTicketMessage::with('documents')->where('support_ticket_id', $support_ticket->id)->latest()->first();

        $service = Service::with('seller')->findOrFail($support_ticket->service_id);

        return view('supportticket::servicequery.student.show', [
            'support_ticket' => $support_ticket,
            'ticket_messages' => $ticket_messages,
            'last_message' => $last_message,
            'service' => $service,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function support_ticket_message(Request $request, $id)
    {

        $request->validate(
            [
                'message' => 'required',
            ],
            [
                'message.required' => trans('translate.Message is required'),
            ]
        );

        $user = Auth::guard('web')->user();

        $support_ticket = SupportTicket::where('id', $id)->where('author_id', $user->id)->firstOrFail();

        $ticket_message = new SupportTicketMessage();
        $ticket_message->support_ticket_id = $support_ticket->id;
        $ticket_message->message = $request->message;
        $ticket_message->message_author_id = $user->id;
        $ticket_message->save();


        if ($request->hasFile('documents')) {
            foreach ($request->documents as $index => $request_file) {
                $extention = $request_file->getClientOriginalExtension();
                $file_name = 'agency-support-' . time() . $index . '.' . $extention;
                $destinationPath = public_path('uploads/custom-images/');
                $request_file->move($destinationPath, $file_name);

                $document = new MessageDocument();
                $document->message_id = $ticket_message->id;
                $document->file_name = $file_name;
                $document->model_name = 'SupportTicketMessage';
                $document->save();
            }
        }


        $notify_message = trans('translate.Message send successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }
}
