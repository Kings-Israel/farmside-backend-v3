<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Get Bookings
     **/
    public function index(Request $request)
    {
        $per_page = $request->query('per_page');

        $query = Booking::query();

        $limit = min((int) $request->get('limit', 50), 200);
        $bookings = $query->paginate($limit, ['*'], 'page', $request->get('page', 1));

        return response()->json($bookings);
    }

    /**
     * Store a booking
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
}
