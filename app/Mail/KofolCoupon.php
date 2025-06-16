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

    public $couponCode;

    public $expiryDate;

    public $redeemUrl;

    public function __construct($customer, $couponCode = null)
    {
        $this->customer = $customer;
        $this->customerName = $customer->name ?? '';
        $this->customerAddress = $customer->address ?? '';
        $this->customerTown = $customer->town ?? '';
        $this->headquarterName = $customer->headquarter->name ?? '';
        $this->couponCode = $couponCode ?? 'KOFOL-'.date('Y').'-'.str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $this->expiryDate = now()->addMonths(3)->format('F d, Y');
        $this->redeemUrl = config('app.url').'/redeem-coupon';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Coupon for Kofol Campaign',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.coupon-email',
        );
    }
}
