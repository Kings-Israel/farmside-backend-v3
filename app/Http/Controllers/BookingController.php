<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {

    }

    /**
     * Handle the booking submission.
     */
    public function submitBooking(Request $request)
    {
        // Validate the request data
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'email|max:255|required_without:phone_number',
            'phone_number' => 'string|max:20|required_without:email',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_type' => 'required|string|max:255',
            'event_details' => 'nullable',
        ]);

        if ($validatedData->fails()) {
            return response()->json($validatedData->messages(), 422);
        }

        // Store the booking in the database
        Booking::create($validatedData->validated());

        // Redirect or return a response
        return response()->json(['message' => 'Your booking was received successfully'], 200);
    }
}
