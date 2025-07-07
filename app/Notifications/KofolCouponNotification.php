<?php

namespace App\Notifications;

use App\Mail\KofolCoupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KofolCouponNotification extends Notification
{
    use Queueable;

    public $customer;

    public $customerName;

    public $customerAddress;

    public $customerTown;

    public $headquarterName;

    public $couponCodes;

    public $kofolEntryId;

    /**
     * Create a new notification instance.
     */
    public function __construct($customer, $couponCodes = [], $kofolEntryId = null)
    {
        $this->customer = $customer;
        $this->customerName = $customer->name ?? '';
        $this->customerAddress = $customer->address ?? '';
        $this->customerTown = $customer->town ?? '';
        $this->headquarterName = $customer->headquarter->name ?? '';
        $this->couponCodes = $couponCodes;
        $this->kofolEntryId = $kofolEntryId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): KofolCoupon
    {
        // Determine CC emails based on the record owner role
        $ccEmails = [];
        $owner = $this->customer->user ?? null;
        if ($owner) {
            if ($owner->hasRole('DSA')) {
                $managers = $owner->getManagers();
                if (isset($managers['ASM'])) {
                    $ccEmails[] = $managers['ASM']->email;
                }
                if (isset($managers['RSM'])) {
                    $ccEmails[] = $managers['RSM']->email;
                }
            } elseif ($owner->hasRole('ASM')) {
                $ccEmails[] = $owner->email;
                $managers = $owner->getManagers();
                if (isset($managers['RSM'])) {
                    $ccEmails[] = $managers['RSM']->email;
                }
            } elseif ($owner->hasRole('RSM')) {
                $ccEmails[] = $owner->email;
            }
        }
        return (new KofolCoupon($this->customer, $this->couponCodes, $ccEmails))
            ->to($this->customer->email);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'customer_name' => $this->customerName,
            'customer_address' => $this->customerAddress,
            'customer_town' => $this->customerTown,
            'headquarter_name' => $this->headquarterName,
            'coupon_codes' => $this->couponCodes,
            'kofol_entry_id' => $this->kofolEntryId,
        ];
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
