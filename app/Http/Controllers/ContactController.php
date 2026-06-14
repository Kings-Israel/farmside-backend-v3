<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        // Send Mail to Admin
        \Mail::to(config('mail.admin_email'))->send(new \App\Mail\ContactFormEmail($request->name, $request->email, $request->phone_number, $request->message));

        return response()->json(['message' => 'Thank you for contacting us. We will get back to you shortly.'], 200);
    }
}
