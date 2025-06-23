<?php

namespace Database\Seeders;

use App\Models\Chemist;
use App\Models\Doctor;
use App\Models\KofolEntry;
use App\Models\Product;
use App\Models\User;
use App\Models\Campaign;
use App\Models\CampaignEntry;
use Illuminate\Database\Seeder;

class KofolEntrySeeder extends Seeder
{
    public function run(): void
    {
        // Only users with DSA role
        $users = User::role('DSA')->get();
        $products = Product::whereHas('brand', function ($query) {
            $query->where('name', 'Kofol');
        })->get();
        $campaigns = Campaign::where('is_active', true)
            ->where('allowed_entry_type', 'kofol_entry')
            ->get();
        $status = 'Pending';
        $usedCouponCodes = [];

        foreach ($users as $user) {
            // Get doctors and chemists from the same headquarter as the user
            $doctors = Doctor::where('headquarter_id', $user->location_id)->get();
            $chemists = Chemist::where('headquarter_id', $user->location_id)->get();

            for ($i = 0; $i < 10; $i++) {
                $numProducts = rand(1, 3);
                $entryProducts = [];
                $invoiceAmount = 0;
                $selectedProducts = $products->random($numProducts);
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 10);
                    $price = $product->price * $quantity;
                    $entryProducts[] = [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ];
                    $invoiceAmount += $price;
                }

                // Randomly pick Doctor or Chemist as customer from the same headquarter
                if (rand(0, 1) === 0 && $doctors->count() > 0) {
                    $customer = $doctors->random();
                    $customerType = Doctor::class;
                } elseif ($chemists->count() > 0) {
                    $customer = $chemists->random();
                    $customerType = Chemist::class;
                } else {
                    // If neither available, skip this entry
                    continue;
                }

                // Unique coupon code or null
                // $couponCode = (rand(0, 1) === 1) ? null : $this->generateUniqueCoupon($usedCouponCodes);
                // if ($couponCode) {
                //     $usedCouponCodes[] = $couponCode;
                // }

                $campaign = $campaigns->random();

                $kofolEntry = KofolEntry::create([
                    'user_id' => $user->id,
                    'invoice_image' => 'https://picsum.photos/200/300?random=1',
                    'products' => $entryProducts,
                    'customer_type' => $customerType,
                    'customer_id' => $customer->id,
                    'invoice_amount' => (int) $invoiceAmount,
                    'status' => $status,
                    // 'coupon_code' => $couponCode,
                ]);

                // Create the CampaignEntry link
                $kofolEntry->campaignEntry()->create([
                    'campaign_id'   => $campaign->id,
                    'customer_id'   => $customer->id,
                    'customer_type' => $customerType,
                ]);
            }
        }
    }

   
}
