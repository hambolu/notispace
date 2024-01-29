<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $fromAddress;
    public $fromName;
    public $subject;
    public $templateData;
    public $attachments;

    public function __construct($fromAddress, $fromName, $subject, $templateData, $attachments)
    {
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->subject = $subject;
        $this->templateData = $templateData;
        $this->attachments = $attachments;
    }

    public function build()
    {
        $mail = $this->from($this->fromAddress, $this->fromName)
                    ->subject($this->subject)
                    ->view('emails.custom', ['templateData' => $this->templateData]); // Specify that the email content is HTML
    
        // Attachments from request (if available)
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $mail->attach($attachment->path());
            }
        }
    
        return $mail;
    }
    

}
