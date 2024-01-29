<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Mail\CustomEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function sendEmail(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'authorization' => 'required',
                'from' => 'required|email',
                'from_name' => 'required',
                'to' => 'required|email',
                'subject' => 'required',
                'template' => 'required',
                'attachments' => 'array', // Validate attachments as an array
            ]);

            // Get template data from JSON file
            $templateName = $request->template;
            $templateData = $this->getTemplateData($templateName);
            $attachments = $request->attachments ?? [];

            // Check if template exists
            if (!$templateData) {
                return response()->json(['error' => 'Template not found'], 400);
            }

            // Create CustomEmail Mailable instance
            $fromAddress = $request->from ?? config('mail.from.address');
            $fromName = $request->from_name ?? config('mail.from.name');
            $mail = new CustomEmail($fromAddress, $fromName, $request->subject, $templateData, $attachments);

            // Attach files to the email
            foreach ($attachments as $attachment) {
                $mail->attach($attachment);
            }

            // Send email
            Mail::to($request->to)->send($mail);

            return response()->json(['message' => 'Email sent successfully'], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            // Log::error($e->getMessage());

            return response()->json(['error' => 'Failed to send email'], 500);
        }
    }

    protected function getTemplateData($templateName)
    {
        $filePath = base_path('app/Http/Controllers/templates/templates.json');

        if (!file_exists($filePath)) {
            return null; // File not found
        }

        $fileContents = file_get_contents($filePath);
        $templates = json_decode($fileContents, true);

        return $templates['templates'][$templateName] ?? null;
    }
}
