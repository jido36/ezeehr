<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;



class ApplicationConfirmationOld extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $application;
    public function __construct($application)
    {
        $this->application = $application;
    }

    // public function envelope()
    // {
    //     return new Envelope(
    //         from: new Address('jeffrey@example.com', 'Jeffrey Way'),
    //         subject: 'Order Shipped',
    //     );
    // }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // from(new Address('fabien@example.com'));
        return $this->view('emails.applicationconfirmation');
    }
}
