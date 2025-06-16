<?php

namespace Database\Seeders;

use App\Models\Chemist;
use App\Models\Doctor;
use App\Models\KofolCampaign;
use App\Models\KofolEntry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class KofolEntrySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::inRandomOrder()->take(20)->get();
        $products = Product::whereHas('brand', function ($query) {
            $query->where('name', 'Kofol');
        })->get();
        $doctors = Doctor::all();
        $chemists = Chemist::all();
        $campaigns = KofolCampaign::where('is_active', true)->get();
        $status = 'Pending';
        $usedCouponCodes = [];

        foreach ($users as $user) {
            $numProducts = rand(1, 5);
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

            // Randomly pick Doctor or Chemist as customer
            if (rand(0, 1) === 0 && $doctors->count() > 0) {
                $customer = $doctors->random();
                $customerType = Doctor::class;
            } else {
                $customer = $chemists->random();
                $customerType = Chemist::class;
            }

            // Unique coupon code or null
            // $couponCode = (rand(0, 1) === 1) ? null : $this->generateUniqueCoupon($usedCouponCodes);
            // if ($couponCode) {
            //     $usedCouponCodes[] = $couponCode;
            // }

            KofolEntry::create([
                'kofol_campaign_id' => $campaigns->random()->id,
                'user_id' => $user->id,
                'invoice_image' => 'https://picsum.photos/200/300?random=1',
                'products' => $entryProducts,
                'customer_type' => $customerType,
                'customer_id' => $customer->id,
                'invoice_amount' => (int) $invoiceAmount,
                'status' => $status,
                // 'coupon_code' => $couponCode,
            ]);
        }
    }

    private function generateUniqueCoupon(array $used): int
    {
        do {
            $code = rand(100000, 999999);
        } while (in_array($code, $used));

        return $code;
    }
}
