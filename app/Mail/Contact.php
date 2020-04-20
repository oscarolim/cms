<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $email, $body_message;

    public function __construct($values)
    {
        $this->name = $values['name'];
        $this->email = $values['email'];
        $this->body_message = $values['text'];
    }

    public function build()
    {
        return $this->from('me@oscarolim.com')->subject('Contact from your CV website')->view('emails.contact');
    }
}
