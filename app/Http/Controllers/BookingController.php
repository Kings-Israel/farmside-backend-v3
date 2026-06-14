<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'completed', 'forfeited'])],
            'event_date_from' => ['nullable', 'date'],
            'event_date_to' => ['nullable', 'date'],
        ]);

        $status = $filters['status'] ?? null;
        if ($status) {
            match ($status) {
                'pending' => $query->whereNull('confirmed_at'),
                'confirmed' => $query->whereNotNull('confirmed_at')
                    ->whereNull('completed_at')
                    ->whereNull('forfeited_at'),
                'completed' => $query->whereNotNull('completed_at'),
                'forfeited' => $query->whereNotNull('forfeited_at'),
            };
        }

        if (!empty($filters['event_date_from'])) {
            $query->whereDate('event_date', '>=', Carbon::parse($filters['event_date_from'])->toDateString());
        }

        if (!empty($filters['event_date_to'])) {
            $query->whereDate('event_date', '<=', Carbon::parse($filters['event_date_to'])->toDateString());
        }

        $limit = min((int) $request->get('limit', 50), 200);
        $bookings = $query->paginate($limit, ['*'], 'page', $request->get('page', 1));

        return Inertia::render('Bookings', [
            'bookings' => $bookings,
            'filters' => [
                'status' => $status,
                'event_date_from' => $filters['event_date_from'] ?? null,
                'event_date_to' => $filters['event_date_to'] ?? null,
            ],
        ]);
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

    public function confirm(Booking $booking)
    {
        if (is_null($booking->confirmed_at)) {
            $booking->forceFill([
                'confirmed_at' => now(),
            ])->save();
        }

        return back();
    }

    public function complete(Booking $booking)
    {
        abort_if(is_null($booking->confirmed_at) || $booking->completed_at || $booking->forfeited_at, 422);

        $booking->forceFill([
            'completed_at' => now(),
        ])->save();

        return back();
    }

    public function forfeit(Request $request, Booking $booking)
    {
        abort_if(is_null($booking->confirmed_at) || $booking->completed_at || $booking->forfeited_at, 422);

        $validated = $request->validate([
            'forfeiture_reason' => ['required', 'string', 'max:1000'],
        ]);

        $booking->forceFill([
            'forfeited_at' => now(),
            'forfeiture_reason' => $validated['forfeiture_reason'],
        ])->save();

        return back();
    }

    public function analyze(Request $request)
    {
        $response = \App\AiAgents\BookingAnalyzer::ask($request->input('description'));

        return response()->json($response);
    }
}
