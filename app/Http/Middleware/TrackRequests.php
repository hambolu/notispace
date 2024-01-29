<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use App\Models\RequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class TrackRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Validate the authorization header
        $authorizationHeader = $request->header('Authorization');
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Unauthorized Access'], Response::HTTP_UNAUTHORIZED);
        }

        // Validate the authorization key
        if (!$this->isValidAuthorizationKey($authorizationHeader)) {
            return response()->json(['error' => 'Invalid authorization key'], Response::HTTP_UNAUTHORIZED);
        }

        // Log the request details
        $this->logRequest($request);

        return $next($request);
    }

    private function isValidAuthorizationKey($key)
    {
        // Retrieve the hashed key from the database
        $setting = Setting::where('secretkey', $key)->first();
        if (!$setting) {
            return false; // Setting not found
        }

        // Compare the provided key with the hashed key using Laravel's Hash::check method
        return Hash::check($key, $setting->hashedkey);
    }

    private function logRequest(Request $request)
    {
        $log = new RequestLog();
        $log->method = $request->method();
        $log->path = $request->path();
        $log->ip = $request->ip();
        $log->user_agent = $request->header('User-Agent');
        $log->user_id = Auth::id();
        $log->save();
    }
}
