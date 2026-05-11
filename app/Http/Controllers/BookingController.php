<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Get Bookings
     **/
    public function index(Request $request)
    {
        $per_page = $request->query('per_page');

        $query = Booking::query()->latest();

        $limit = min((int) $request->get('limit', 50), 200);
        $bookings = Booking::query()
            ->orderBy('event_date', 'desc')
            ->paginate($limit, ['*'], 'page', $request->get('page', 1));

        $calendarBookings = Booking::query()
            ->select('id', 'event_name', 'event_date', 'status')
            ->get();

        return Inertia::render('Bookings', [
            'bookings' => $bookings,
            'calendarBookings' => $calendarBookings,
        ]);
    }

    /**
     * Store a booking via the public API (status defaults to pending)
     **/
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => ['required'],
            'email' => ['required', 'email'],
            'phone_number' => ['required'],
            'event_date' => ['required', 'date'],
            'location' => ['required'],
            'event_type' => ['required'],
            'event_details' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = collect($request->all())->map(function ($value, $key) {
            if ($key === 'event_date') {
                return Carbon::parse($value);
            } else {
                return $value;
            }
        });

        $booking = Booking::create($data->toArray());

        return response()->json($booking, 201);
    }

    /**
     * Store a booking created by admin (auto-confirmed)
     **/
    public function adminStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => ['required'],
            'email' => ['required', 'email'],
            'phone_number' => ['required'],
            'event_date' => ['required', 'date'],
            'location' => ['required'],
            'event_type' => ['required'],
            'description' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['event_name', 'email', 'phone_number', 'event_date', 'location', 'event_type', 'description']);
        $data['event_date'] = Carbon::parse($data['event_date']);
        $data['status'] = 'confirmed';
        $data['confirmed_at'] = now();

        Booking::create($data);

        return redirect()->route('bookings.index');
    }

    public function confirm(Booking $booking)
    {
        if (is_null($booking->confirmed_at)) {
            $booking->forceFill([
                'confirmed_at' => now(),
                'status' => 'confirmed'
            ])->save();
        }

        return back();
    }

    public function analyze(Request $request)
    {
        $response = \App\AiAgents\BookingAnalyzer::ask($request->input('description'));

        return response()->json($response);
    }
}
