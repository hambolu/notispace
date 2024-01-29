<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    //
    public function sendSMS(Request $request)
    {
        // Validate request data
        $request->validate([
            'authorization' => 'required',
            'to' => 'required|array',
            'subject' => 'required',
            'body' => 'required',
            'from' => 'required',
        ]);

        // Authenticate user with authorization key

        // Send SMS using Nexmo API
        $response = Http::post('https://rest.nexmo.com/sms/json', [
            'api_key' => env('NEXMO_API_KEY'),
            'api_secret' => env('NEXMO_API_SECRET'),
            'from' => $request->from,
            'to' => implode(',', $request->to),
            'text' => $request->body,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            return response()->json(['message' => 'SMS sent successfully']);
        } else {
            return response()->json(['error' => 'Failed to send SMS'], $response->status());
        }
    }
}
