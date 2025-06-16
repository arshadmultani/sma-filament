<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Headquarter;
use Illuminate\Database\Seeder;

class HeadquarterSeeder extends Seeder
{
    public function run(): void
    {
        $headquartersByArea = [
            'Mumbai' => ['Churchgate', 'Dadar', 'Vasai', 'Andheri', 'Borivali'],
            'Pune' => ['Shivajinagar', 'Kothrud', 'Hadapsar', 'Wakad', 'Koregaon Park'],
            'Nagpur' => ['Sitabuldi', 'Dharampeth', 'Sadar', 'Mahal', 'Civil Lines'],
            'Nashik' => ['Panchavati', 'CIDCO', 'Indira Nagar', 'Satpur', 'Ambad'],
            'Aurangabad' => ['CIDCO', 'Nirala Bazar', 'Osmanpura', 'Garkheda', 'Jalna Road'],
            'Bengaluru' => ['MG Road', 'Whitefield', 'Indiranagar', 'Jayanagar', 'Koramangala'],
            'Mysuru' => ['Lakshmipuram', 'Vijayanagar', 'Saraswathipuram', 'Jayalakshmipuram', 'Hebbal'],
            'Chennai' => ['T Nagar', 'Anna Nagar', 'Adyar', 'Velachery', 'Mylapore', 'Porur'],
            'Coimbatore' => ['RS Puram', 'Peelamedu', 'Saibaba Colony', 'Ganapathy', 'Singanallur'],
            'Lucknow' => ['Hazratganj', 'Gomti Nagar', 'Aliganj', 'Indira Nagar', 'Mahanagar'],
            'Kanpur' => ['Civil Lines', 'Swaroop Nagar', 'Kakadeo', 'Kidwai Nagar', 'Govind Nagar'],
            'Ahmedabad' => ['Navrangpura', 'Satellite', 'Vastrapur', 'Bodakdev', 'CG Road'],
            'Surat' => ['Adajan', 'Vesu', 'City Light', 'Athwa', 'Piplod'],
            'Kolkata' => ['Park Street', 'Salt Lake', 'Ballygunge', 'New Town', 'Gariahat'],
            'Howrah' => ['Shibpur', 'Santragachi', 'Salkia', 'Belur', 'Kadamtala'],
            'Jaipur' => ['Malviya Nagar', 'Vaishali Nagar', 'Raja Park', 'C Scheme', 'Mansarovar'],
            'Jodhpur' => ['Sardarpura', 'Shastri Nagar', 'Ratanada', 'Chopasni', 'Pal Road'],
            'Bhopal' => ['New Market', 'MP Nagar', 'Arera Colony', 'Shahpura', 'Kolar Road'],
            'Patna' => ['Gandhi Maidan', 'Boring Road', 'Kankarbagh', 'Raja Bazar', 'Patliputra'],
            'Thiruvananthapuram' => ['Palayam', 'Pattom', 'Kesavadasapuram', 'Vellayambalam', 'Sasthamangalam'],
            'Ludhiana' => ['Model Town', 'Civil Lines', 'Sarabha Nagar', 'BRS Nagar', 'Dugri'],
            'Hyderabad' => ['Banjara Hills', 'Jubilee Hills', 'Gachibowli', 'Madhapur', 'Kukatpally'],
            'Indore' => ['Vijay Nagar', 'Palasia', 'Saket', 'New Palasia', 'LIG Colony'],
            'Kochi' => ['Edappally', 'Kakkanad', 'Fort Kochi', 'Palarivattom', 'Kaloor'],
            'Amritsar' => ['Lawrence Road', 'Ranjit Avenue', 'Green Avenue', 'Putlighar', 'Majitha Road'],
            'Vadodara' => ['Alkapuri', 'Fatehgunj', 'Sayajigunj', 'Gotri', 'Manjalpur'],
            'Guwahati' => ['Zoo Road', 'GS Road', 'Fancy Bazar', 'Maligaon', 'Dispur'],
        ];

        foreach ($headquartersByArea as $areaName => $headquarters) {
            $area = Area::where('name', $areaName)->first();
            if ($area) {
                foreach ($headquarters as $hq) {
                    Headquarter::firstOrCreate([
                        'name' => $hq,
                        'area_id' => $area->id,
                    ]);
                }
            }
        }
    }
}
