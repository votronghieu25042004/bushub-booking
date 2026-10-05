<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\BookingSeat;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. TĂ€I KHOáº¢N Há»† THá»NG
        $admin = User::firstOrCreate(['email' => 'admin@bushub.vn'], [
            'name' => 'Tá»•ng Quáº£n Trá»‹ Báº¿n Xe BusHub',
            'phone' => '0901112233',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        $customer = User::firstOrCreate(['email' => 'hieu@gmail.com'], [
            'name' => 'VĂµ Trá»ng Hiáº¿u',
            'phone' => '0905123456',
            'password' => Hash::make('123456'),
            'role' => 'customer',
        ]);

        // 10 Tiáº¿p viĂªn Ä‘iá»u phá»‘i
        $conductorsData = [
            ['name' => 'Tráº§n VÄƒn Äiá»u Phá»‘i', 'phone' => '0904445566', 'email' => 'staff@bushub.vn'],
            ['name' => 'Nguyá»…n VÄƒn PhĂºc', 'phone' => '0905221133', 'email' => 'phuc.staff@bushub.vn'],
            ['name' => 'LĂª Quá»‘c Äáº¡t', 'phone' => '0905334455', 'email' => 'dat.staff@bushub.vn'],
            ['name' => 'Pháº¡m ThĂ nh Long', 'phone' => '0905447788', 'email' => 'long.staff@bushub.vn'],
            ['name' => 'VÅ© Anh Tuáº¥n', 'phone' => '0905558899', 'email' => 'tuan.staff@bushub.vn'],
            ['name' => 'Äá»— Minh TrĂ­', 'phone' => '0905669900', 'email' => 'tri.staff@bushub.vn'],
            ['name' => 'HoĂ ng Háº£i Nam', 'phone' => '0905771122', 'email' => 'nam.staff@bushub.vn'],
            ['name' => 'BĂ¹i Gia Huy', 'phone' => '0905882233', 'email' => 'huy.staff@bushub.vn'],
            ['name' => 'NgĂ´ Tiáº¿n DÅ©ng', 'phone' => '0905993344', 'email' => 'dung.staff@bushub.vn'],
            ['name' => 'DÆ°Æ¡ng VÄƒn KiĂªn', 'phone' => '0905004455', 'email' => 'kien.staff@bushub.vn'],
        ];

        foreach ($conductorsData as $c) {
            User::firstOrCreate(['email' => $c['email']], [
                'name' => $c['name'],
                'phone' => $c['phone'],
                'password' => Hash::make('123456'),
                'role' => 'conductor',
            ]);
        }

        // 2. Táº O 10 XE VĂ€ 10 TĂ€I Xáº¾
        $busesData = [
            ['plate' => '43B-012.34', 'type' => 'Thaco Mobihome VIP 34 PhĂ²ng', 'seats' => 34, 'status' => 'ACTIVE'],
            ['plate' => '43B-018.99', 'type' => 'Hyundai Universe Luxury 36 Chá»—', 'seats' => 36, 'status' => 'ACTIVE'],
            ['plate' => '43B-023.45', 'type' => 'Tracomeco Limousine 22 PhĂ²ng Cung Äiá»‡n', 'seats' => 22, 'status' => 'ACTIVE'],
            ['plate' => '43B-034.56', 'type' => 'Thaco King Long 36 GiÆ°á»ng', 'seats' => 36, 'status' => 'ACTIVE'],
            ['plate' => '43B-045.67', 'type' => 'Samco Universe VIP 34 GiÆ°á»ng', 'seats' => 34, 'status' => 'ACTIVE'],
            ['plate' => '43B-056.78', 'type' => 'Thaco Mobihome Premium 34 GiÆ°á»ng', 'seats' => 34, 'status' => 'ACTIVE'],
            ['plate' => '43B-067.89', 'type' => 'Hyundai Tracomeco 36 GiÆ°á»ng', 'seats' => 36, 'status' => 'ACTIVE'],
            ['plate' => '43B-078.90', 'type' => 'Thaco Auto Limousine 24 PhĂ²ng', 'seats' => 24, 'status' => 'ACTIVE'],
            ['plate' => '43B-089.01', 'type' => 'Hyundai Universe Express 34 Chá»—', 'seats' => 34, 'status' => 'ACTIVE'],
            ['plate' => '43B-099.88', 'type' => 'Thaco Mobihome Deluxe 36 GiÆ°á»ng', 'seats' => 36, 'status' => 'MAINTENANCE'],
        ];

        $driversData = [
            ['name' => 'Nguyá»…n VÄƒn HĂ¹ng', 'phone' => '0905.123.456'],
            ['name' => 'Tráº§n ÄĂ¬nh Trá»ng', 'phone' => '0905.234.567'],
            ['name' => 'LĂª HoĂ ng Nam', 'phone' => '0905.345.678'],
            ['name' => 'Pháº¡m Quá»‘c Báº£o', 'phone' => '0905.456.789'],
            ['name' => 'HoĂ ng Minh Tuáº¥n', 'phone' => '0905.567.890'],
            ['name' => 'Äáº·ng VÄƒn LĂ¢m', 'phone' => '0905.678.901'],
            ['name' => 'BĂ¹i Tiáº¿n DÅ©ng', 'phone' => '0905.789.012'],
            ['name' => 'Há»“ Táº¥n TĂ i', 'phone' => '0905.890.123'],
            ['name' => 'VÅ© VÄƒn Thanh', 'phone' => '0905.901.234'],
            ['name' => 'Tráº§n VÄƒn KiĂªn', 'phone' => '0905.012.345'],
        ];

        $buses = [];
        foreach ($busesData as $b) {
            $buses[] = Bus::firstOrCreate(['license_plate' => $b['plate']], [
                'bus_type' => $b['type'],
                'total_seats' => $b['seats'],
                'status' => $b['status'],
            ]);
        }

        $drivers = [];
        foreach ($driversData as $d) {
            $drivers[] = Driver::firstOrCreate(['phone' => $d['phone']], [
                'name' => $d['name'],
                'license_number' => 'GPLX-' . rand(100000, 999999),
                'license_class' => 'E',
                'years_experience' => rand(5, 15),
                'avg_rating' => 4.9,
            ]);
        }

        // 3. Táº O CĂC TUYáº¾N ÄÆ¯á»œNG 2 CHIá»€U KHá»¨ Há»’I
        // Tuyáº¿n Huáº¿
        $rHueGo = Route::firstOrCreate(['name' => 'ÄĂ  Náºµng â‡„ Thá»«a ThiĂªn - Huáº¿'], [
            'origin' => 'ÄĂ  Náºµng',
            'destination' => 'Thá»«a ThiĂªn - Huáº¿',
            'distance_km' => 100,
            'estimated_hours' => 2.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rHueGo->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 1, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);
        RouteStop::firstOrCreate(['route_id' => $rHueGo->id, 'stop_name' => 'Tráº¡m Thu PhĂ­ PhĂº BĂ i'], ['stop_order' => 2, 'address' => 'Thá»‹ xĂ£ HÆ°Æ¡ng Thá»§y, TT-Huáº¿']);
        RouteStop::firstOrCreate(['route_id' => $rHueGo->id, 'stop_name' => 'Báº¿n xe PhĂ­a Nam Huáº¿'], ['stop_order' => 3, 'address' => '57 An DÆ°Æ¡ng VÆ°Æ¡ng, TP. Huáº¿']);

        $rHueBack = Route::firstOrCreate(['name' => 'Thá»«a ThiĂªn - Huáº¿ â‡„ ÄĂ  Náºµng'], [
            'origin' => 'Thá»«a ThiĂªn - Huáº¿',
            'destination' => 'ÄĂ  Náºµng',
            'distance_km' => 100,
            'estimated_hours' => 2.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rHueBack->id, 'stop_name' => 'Báº¿n xe PhĂ­a Nam Huáº¿'], ['stop_order' => 1, 'address' => '57 An DÆ°Æ¡ng VÆ°Æ¡ng, TP. Huáº¿']);
        RouteStop::firstOrCreate(['route_id' => $rHueBack->id, 'stop_name' => 'Tráº¡m Thu PhĂ­ PhĂº BĂ i'], ['stop_order' => 2, 'address' => 'Thá»‹ xĂ£ HÆ°Æ¡ng Thá»§y, TT-Huáº¿']);
        RouteStop::firstOrCreate(['route_id' => $rHueBack->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 3, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);

        // Quáº£ng Trá»‹
        $rQTGo = Route::firstOrCreate(['name' => 'ÄĂ  Náºµng â‡„ Quáº£ng Trá»‹ (ÄĂ´ng HĂ )'], [
            'origin' => 'ÄĂ  Náºµng',
            'destination' => 'Quáº£ng Trá»‹',
            'distance_km' => 165,
            'estimated_hours' => 3.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rQTGo->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 1, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);
        RouteStop::firstOrCreate(['route_id' => $rQTGo->id, 'stop_name' => 'Tráº¡m dá»«ng Háº£i LÄƒng'], ['stop_order' => 2, 'address' => 'QL1A, Háº£i LÄƒng, Quáº£ng Trá»‹']);
        RouteStop::firstOrCreate(['route_id' => $rQTGo->id, 'stop_name' => 'Báº¿n xe ÄĂ´ng HĂ '], ['stop_order' => 3, 'address' => '425 LĂª Duáº©n, TP. ÄĂ´ng HĂ ']);

        $rQTBack = Route::firstOrCreate(['name' => 'Quáº£ng Trá»‹ (ÄĂ´ng HĂ ) â‡„ ÄĂ  Náºµng'], [
            'origin' => 'Quáº£ng Trá»‹',
            'destination' => 'ÄĂ  Náºµng',
            'distance_km' => 165,
            'estimated_hours' => 3.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rQTBack->id, 'stop_name' => 'Báº¿n xe ÄĂ´ng HĂ '], ['stop_order' => 1, 'address' => '425 LĂª Duáº©n, TP. ÄĂ´ng HĂ ']);
        RouteStop::firstOrCreate(['route_id' => $rQTBack->id, 'stop_name' => 'Tráº¡m dá»«ng Háº£i LÄƒng'], ['stop_order' => 2, 'address' => 'QL1A, Háº£i LÄƒng, Quáº£ng Trá»‹']);
        RouteStop::firstOrCreate(['route_id' => $rQTBack->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 3, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);

        // Quáº£ng BĂ¬nh
        $rQBGo = Route::firstOrCreate(['name' => 'ÄĂ  Náºµng â‡„ Quáº£ng BĂ¬nh (Äá»“ng Há»›i)'], [
            'origin' => 'ÄĂ  Náºµng',
            'destination' => 'Quáº£ng BĂ¬nh',
            'distance_km' => 270,
            'estimated_hours' => 5.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rQBGo->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 1, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);
        RouteStop::firstOrCreate(['route_id' => $rQBGo->id, 'stop_name' => 'Tráº¡m Lá»‡ Thá»§y'], ['stop_order' => 2, 'address' => 'QL1A, Huyá»‡n Lá»‡ Thá»§y']);
        RouteStop::firstOrCreate(['route_id' => $rQBGo->id, 'stop_name' => 'Báº¿n xe Äá»“ng Há»›i'], ['stop_order' => 3, 'address' => 'Tráº§n HÆ°ng Äáº¡o, TP. Äá»“ng Há»›i']);

        $rQBBack = Route::firstOrCreate(['name' => 'Quáº£ng BĂ¬nh (Äá»“ng Há»›i) â‡„ ÄĂ  Náºµng'], [
            'origin' => 'Quáº£ng BĂ¬nh',
            'destination' => 'ÄĂ  Náºµng',
            'distance_km' => 270,
            'estimated_hours' => 5.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rQBBack->id, 'stop_name' => 'Báº¿n xe Äá»“ng Há»›i'], ['stop_order' => 1, 'address' => 'Tráº§n HÆ°ng Äáº¡o, TP. Äá»“ng Há»›i']);
        RouteStop::firstOrCreate(['route_id' => $rQBBack->id, 'stop_name' => 'Tráº¡m Lá»‡ Thá»§y'], ['stop_order' => 2, 'address' => 'QL1A, Huyá»‡n Lá»‡ Thá»§y']);
        RouteStop::firstOrCreate(['route_id' => $rQBBack->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 3, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);

        // Quy NhÆ¡n
        $rQNGo = Route::firstOrCreate(['name' => 'ÄĂ  Náºµng â‡„ Quy NhÆ¡n (BĂ¬nh Äá»‹nh)'], [
            'origin' => 'ÄĂ  Náºµng',
            'destination' => 'Quy NhÆ¡n',
            'distance_km' => 315,
            'estimated_hours' => 6.0,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rQNGo->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 1, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);
        RouteStop::firstOrCreate(['route_id' => $rQNGo->id, 'stop_name' => 'Báº¿n xe Quáº£ng NgĂ£i'], ['stop_order' => 2, 'address' => 'TP. Quáº£ng NgĂ£i']);
        RouteStop::firstOrCreate(['route_id' => $rQNGo->id, 'stop_name' => 'Báº¿n xe Quy NhÆ¡n'], ['stop_order' => 3, 'address' => '71 TĂ¢y SÆ¡n, TP. Quy NhÆ¡n']);

        $rQNBack = Route::firstOrCreate(['name' => 'Quy NhÆ¡n (BĂ¬nh Äá»‹nh) â‡„ ÄĂ  Náºµng'], [
            'origin' => 'Quy NhÆ¡n',
            'destination' => 'ÄĂ  Náºµng',
            'distance_km' => 315,
            'estimated_hours' => 6.0,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rQNBack->id, 'stop_name' => 'Báº¿n xe Quy NhÆ¡n'], ['stop_order' => 1, 'address' => '71 TĂ¢y SÆ¡n, TP. Quy NhÆ¡n']);
        RouteStop::firstOrCreate(['route_id' => $rQNBack->id, 'stop_name' => 'Báº¿n xe Quáº£ng NgĂ£i'], ['stop_order' => 2, 'address' => 'TP. Quáº£ng NgĂ£i']);
        RouteStop::firstOrCreate(['route_id' => $rQNBack->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 3, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);

        // BuĂ´n Ma Thuá»™t
        $rBMTGo = Route::firstOrCreate(['name' => 'ÄĂ  Náºµng â‡„ BuĂ´n Ma Thuá»™t (Äáº¯k Láº¯k)'], [
            'origin' => 'ÄĂ  Náºµng',
            'destination' => 'BuĂ´n Ma Thuá»™t',
            'distance_km' => 480,
            'estimated_hours' => 9.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rBMTGo->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 1, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);
        RouteStop::firstOrCreate(['route_id' => $rBMTGo->id, 'stop_name' => 'Báº¿n xe LiĂªn Tá»‰nh Äáº¯k Láº¯k'], ['stop_order' => 2, 'address' => 'Nguyá»…n Táº¥t ThĂ nh, TP. BuĂ´n Ma Thuá»™t']);

        $rBMTBack = Route::firstOrCreate(['name' => 'BuĂ´n Ma Thuá»™t (Äáº¯k Láº¯k) â‡„ ÄĂ  Náºµng'], [
            'origin' => 'BuĂ´n Ma Thuá»™t',
            'destination' => 'ÄĂ  Náºµng',
            'distance_km' => 480,
            'estimated_hours' => 9.5,
        ]);
        RouteStop::firstOrCreate(['route_id' => $rBMTBack->id, 'stop_name' => 'Báº¿n xe LiĂªn Tá»‰nh Äáº¯k Láº¯k'], ['stop_order' => 1, 'address' => 'Nguyá»…n Táº¥t ThĂ nh, TP. BuĂ´n Ma Thuá»™t']);
        RouteStop::firstOrCreate(['route_id' => $rBMTBack->id, 'stop_name' => 'Báº¿n xe Trung tĂ¢m ÄĂ  Náºµng'], ['stop_order' => 2, 'address' => '185 TĂ´n Äá»©c Tháº¯ng, TP. ÄĂ  Náºµng']);

        // XĂ³a sáº¡ch chuyáº¿n & booking cÅ© Ä‘á»ƒ táº¡o dá»¯ liá»‡u chuáº©n xĂ¡c
        BookingSeat::truncate();
        Booking::truncate();
        Trip::truncate();

        // 4. Táº O CĂC CHUYáº¾N XE 2 CHIá»€U KHá»¨ Há»’I
        $tripsSchedule = [
            ['bus' => $buses[0], 'driver' => $drivers[0], 'route' => $rHueGo, 'dep' => '06:30', 'arr' => '09:00', 'price' => 120000, 'status' => 'COMPLETED'],
            ['bus' => $buses[0], 'driver' => $drivers[0], 'route' => $rHueBack, 'dep' => '12:30', 'arr' => '15:00', 'price' => 120000, 'status' => 'RUNNING'],
            ['bus' => $buses[0], 'driver' => $drivers[0], 'route' => $rHueGo, 'dep' => '17:30', 'arr' => '20:00', 'price' => 120000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[1], 'driver' => $drivers[1], 'route' => $rHueGo, 'dep' => '08:00', 'arr' => '10:30', 'price' => 120000, 'status' => 'COMPLETED'],
            ['bus' => $buses[1], 'driver' => $drivers[1], 'route' => $rHueBack, 'dep' => '14:30', 'arr' => '17:00', 'price' => 120000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[2], 'driver' => $drivers[2], 'route' => $rQTGo, 'dep' => '07:00', 'arr' => '10:30', 'price' => 140000, 'status' => 'COMPLETED'],
            ['bus' => $buses[2], 'driver' => $drivers[2], 'route' => $rQTBack, 'dep' => '13:30', 'arr' => '17:00', 'price' => 140000, 'status' => 'RUNNING'],

            ['bus' => $buses[3], 'driver' => $drivers[3], 'route' => $rQBGo, 'dep' => '06:00', 'arr' => '11:30', 'price' => 190000, 'status' => 'COMPLETED'],
            ['bus' => $buses[3], 'driver' => $drivers[3], 'route' => $rQBBack, 'dep' => '14:00', 'arr' => '19:30', 'price' => 190000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[4], 'driver' => $drivers[4], 'route' => $rQNGo, 'dep' => '07:30', 'arr' => '13:30', 'price' => 220000, 'status' => 'RUNNING'],
            ['bus' => $buses[4], 'driver' => $drivers[4], 'route' => $rQNBack, 'dep' => '15:30', 'arr' => '21:30', 'price' => 220000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[5], 'driver' => $drivers[5], 'route' => $rBMTGo, 'dep' => '20:00', 'arr' => '05:30', 'price' => 290000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[6], 'driver' => $drivers[6], 'route' => $rHueGo, 'dep' => '09:00', 'arr' => '11:30', 'price' => 120000, 'status' => 'COMPLETED'],
            ['bus' => $buses[6], 'driver' => $drivers[6], 'route' => $rHueBack, 'dep' => '16:00', 'arr' => '18:30', 'price' => 120000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[7], 'driver' => $drivers[7], 'route' => $rQNGo, 'dep' => '10:00', 'arr' => '16:00', 'price' => 220000, 'status' => 'RUNNING'],
            ['bus' => $buses[7], 'driver' => $drivers[7], 'route' => $rQNBack, 'dep' => '18:00', 'arr' => '23:59', 'price' => 220000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[8], 'driver' => $drivers[8], 'route' => $rQTGo, 'dep' => '09:30', 'arr' => '13:00', 'price' => 140000, 'status' => 'COMPLETED'],
            ['bus' => $buses[8], 'driver' => $drivers[8], 'route' => $rQTBack, 'dep' => '16:30', 'arr' => '20:00', 'price' => 140000, 'status' => 'SCHEDULED'],

            ['bus' => $buses[9], 'driver' => $drivers[9], 'route' => $rHueGo, 'dep' => '09:30', 'arr' => '12:00', 'price' => 120000, 'status' => 'CANCELLED'],
        ];

        $samplePassengers = [
            ['name' => 'Nguyá»…n Thá»‹ Thu HĂ ', 'phone' => '0912345678', 'email' => 'thuha.nguyen@gmail.com'],
            ['name' => 'Tráº§n VÄƒn Máº¡nh', 'phone' => '0913456789', 'email' => 'manh.tran@gmail.com'],
            ['name' => 'LĂª Thá»‹ Má»¹ Linh', 'phone' => '0914567890', 'email' => 'mylinh.le@gmail.com'],
            ['name' => 'Pháº¡m HoĂ ng Long', 'phone' => '0915678901', 'email' => 'hoanglong.pham@gmail.com'],
            ['name' => 'Äáº·ng Minh QuĂ¢n', 'phone' => '0916789012', 'email' => 'minhquan.dang@gmail.com'],
            ['name' => 'BĂ¹i Thanh Tháº£o', 'phone' => '0917890123', 'email' => 'thanhthao.bui@gmail.com'],
            ['name' => 'VĂµ Quá»‘c HÆ°ng', 'phone' => '0918901234', 'email' => 'quochung.vo@gmail.com'],
            ['name' => 'HoĂ ng Ngá»c Ănh', 'phone' => '0919012345', 'email' => 'ngocanh.hoang@gmail.com'],
            ['name' => 'Äá»— Gia Báº£o', 'phone' => '0911223344', 'email' => 'giabao.do@gmail.com'],
            ['name' => 'Phan Yáº¿n Nhi', 'phone' => '0912334455', 'email' => 'yennhi.phan@gmail.com'],
        ];

        $tripCounter = 1;
        for ($dayOffset = -1; $dayOffset < 3; $dayOffset++) {
            $tripDate = Carbon::today()->addDays($dayOffset);

            foreach ($tripsSchedule as $idx => $ts) {
                $depDateTime = $tripDate->copy()->setTimeFromTimeString($ts['dep']);
                $arrDateTime = $tripDate->copy()->setTimeFromTimeString($ts['arr']);
                if ($arrDateTime->lessThan($depDateTime)) {
                    $arrDateTime->addDay();
                }

                $status = $ts['status'];
                if ($dayOffset < 0 && $status !== 'CANCELLED') {
                    $status = 'COMPLETED';
                } elseif ($dayOffset > 0 && $status !== 'CANCELLED') {
                    $status = 'SCHEDULED';
                }

                $tripCode = 'BH-TRIP-' . str_pad($tripCounter++, 4, '0', STR_PAD_LEFT);

                $trip = Trip::create([
                    'trip_code' => $tripCode,
                    'route_id' => $ts['route']->id,
                    'bus_id' => $ts['bus']->id,
                    'driver_id' => $ts['driver']->id,
                    'departure_time' => $depDateTime,
                    'arrival_time' => $arrDateTime,
                    'base_price' => $ts['price'],
                    'status' => $status,
                ]);

                // Táº¡o booking tháº­t cho cĂ¡c chuyáº¿n khĂ´ng bá»‹ há»§y
                if ($status !== 'CANCELLED') {
                    $numBookings = rand(6, 12);
                    for ($bIdx = 0; $bIdx < $numBookings; $bIdx++) {
                        $p = $samplePassengers[$bIdx % count($samplePassengers)];
                        $seatCount = rand(1, 2);
                        $totalPrice = $ts['price'] * $seatCount;
                        $isDeposit = rand(0, 1) === 1;
                        $paidAmount = $isDeposit ? round($totalPrice * 0.3) : $totalPrice;
                        $bCode = 'BH-' . strtoupper(Str::random(8));

                        $pickupStop = $ts['route']->stops->first();
                        $dropoffStop = $ts['route']->stops->last();

                        Booking::create([
                            'booking_code' => $bCode,
                            'trip_id' => $trip->id,
                            'user_id' => $customer->id,
                            'customer_name' => $p['name'],
                            'customer_phone' => $p['phone'],
                            'customer_email' => $p['email'],
                            'pickup_stop_id' => $pickupStop ? $pickupStop->id : 1,
                            'dropoff_stop_id' => $dropoffStop ? $dropoffStop->id : 2,
                            'pickup_order' => 1,
                            'dropoff_order' => 2,
                            'total_seats' => $seatCount,
                            'total_amount' => $totalPrice,
                            'paid_amount' => $paidAmount,
                            'payment_status' => $isDeposit ? 'PARTIALLY_PAID' : 'PAID',
                            'payment_method' => 'ONLINE_VNPAY',
                            'booking_source' => 'ONLINE',
                            'qr_token' => Str::random(32),
                        ]);
                    }
                }
            }
        }

        echo "Da khoi tao thanh cong 10 xe va du lieu dat ve khach hang that!\n";
    }
}