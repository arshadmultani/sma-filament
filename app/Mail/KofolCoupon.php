<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KofolCoupon extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;

    public $customerName;

    public $customerAddress;

    public $customerTown;

    public $headquarterName;

    public $couponCodes;

    public $expiryDate;

    public $redeemUrl;

    public $ccEmails = [];

    public function __construct($customer, $couponCodes = [], $ccEmails = [])
    {
        $this->customer = $customer;
        $this->customerName = $customer->name ?? '';
        $this->customerAddress = $customer->address ?? '';
        $this->customerTown = $customer->town ?? '';
        $this->headquarterName = $customer->headquarter->name ?? '';
        $this->couponCodes = $couponCodes;
        $this->ccEmails = $ccEmails;
        // $this->expiryDate = now()->addMonths(3)->format('F d, Y');
        // $this->redeemUrl = config('app.url').'/redeem-coupon';
    }

    public function envelope(): Envelope
    {
        $envelope = new Envelope(
            subject: 'Your Coupon for Kofol Order',
        );
        if (!empty($this->ccEmails)) {
            $envelope = $envelope->cc($this->ccEmails);
        }
        return $envelope;
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.coupon-email',
        );
    }
}
