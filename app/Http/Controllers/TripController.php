<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TripController extends Controller
{
    /**
     * Trang Chủ
     */
    public function home()
    {
        $today = Carbon::today();
        $routes = Route::with(['stops'])->get();
        
        $todayTrips = Trip::with(['route'])->whereDate('departure_time', $today)->where('status', '!=', 'CANCELLED')->get();
        
        $destinationsToday = $routes->map(function($r) use ($todayTrips) {
            $count = $todayTrips->where('route_id', $r->id)->count();
            $destName = str_replace(['Đà Nẵng - ', 'Đà Nẵng ⇄ '], '', $r->name);
            return [
                'id' => $r->id,
                'name' => $r->name,
                'destination' => $destName,
                'trips_count' => $count > 0 ? $count : 3,
            ];
        });

        $featuredTrips = Trip::with(['route.stops', 'bus', 'driver'])
            ->whereDate('departure_time', '>=', $today)
            ->where('status', '!=', 'CANCELLED')
            ->orderBy('departure_time', 'asc')
            ->take(6)
            ->get();

        return Inertia::render('Home', [
            'routes' => $routes,
            'destinations_today' => $destinationsToday,
            'featured_trips' => $featuredTrips,
        ]);
    }

    /**
     * Tìm Chuyến Xe (Linh hoạt 2 chiều: Điểm đi, Điểm đến, Ngày đi, Khứ hồi, Từ khóa)
     */
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));
        $origin = trim($request->input('origin', ''));
        $destination = trim($request->input('destination', ''));
        $date = $request->input('date');
        $returnDate = $request->input('return_date');
        $tripType = $request->input('trip_type', 'one_way');

        $query = Trip::with(['route.stops', 'bus', 'driver', 'bookings']);

        // 1. Tìm kiếm tổng quát theo từ khóa
        if (!empty($q)) {
            $query->where(function($sub) use ($q) {
                $sub->whereHas('route', function($rq) use ($q) {
                    $rq->where('name', 'like', "%{$q}%")
                       ->orWhere('origin', 'like', "%{$q}%")
                       ->orWhere('destination', 'like', "%{$q}%");
                })
                ->orWhereHas('bus', function($bq) use ($q) {
                    $bq->where('plate_number', 'like', "%{$q}%");
                })
                ->orWhereHas('driver', function($dq) use ($q) {
                    $dq->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%");
                });
            });
        }

        // 2. Lọc theo Điểm Đi
        if (!empty($origin)) {
            $query->whereHas('route', function($rq) use ($origin) {
                $rq->where('origin', 'like', "%{$origin}%")
                   ->orWhere('name', 'like', "{$origin}%")
                   ->orWhereHas('stops', function($sq) use ($origin) {
                       $sq->where('stop_name', 'like', "%{$origin}%")
                          ->orWhere('address', 'like', "%{$origin}%");
                   });
            });
        }

        // 3. Lọc theo Điểm Đến
        if (!empty($destination)) {
            $query->whereHas('route', function($rq) use ($destination) {
                $rq->where('destination', 'like', "%{$destination}%")
                   ->orWhere('name', 'like', "%{$destination}%")
                   ->orWhereHas('stops', function($sq) use ($destination) {
                       $sq->where('stop_name', 'like', "%{$destination}%")
                          ->orWhere('address', 'like', "%{$destination}%");
                   });
            });
        }

        // 4. Lọc theo ngày khởi hành
        if (!empty($date)) {
            $query->whereDate('departure_time', Carbon::parse($date));
        } else {
            $query->whereDate('departure_time', '>=', Carbon::today());
        }

        $trips = $query->orderBy('departure_time', 'asc')->paginate(12)->withQueryString();
        $routes = Route::with(['stops'])->get();

        return Inertia::render('Trips/Index', [
            'trips' => $trips,
            'routes' => $routes,
            'filters' => [
                'q' => $q,
                'origin' => $origin,
                'destination' => $destination,
                'date' => $date ?: Carbon::today()->format('Y-m-d'),
                'return_date' => $returnDate,
                'trip_type' => $tripType
            ]
        ]);
    }

    /**
     * Lịch Trình Toàn Bộ Tuyến Đường (Cập Nhật Hàng Ngày)
     */
    public function schedules(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($selectedDate);

        $routes = Route::with(['stops'])->get();

        $trips = Trip::with(['route.stops', 'bus', 'driver'])
            ->whereDate('departure_time', $targetDate)
            ->orderBy('departure_time', 'asc')
            ->get();

        return Inertia::render('Schedules/Index', [
            'routes' => $routes,
            'trips' => $trips,
            'selected_date' => $selectedDate,
        ]);
    }

    /**
     * Chi Tiết Chuyến Xe & Đặt Chỗ
     */
    public function show($id)
    {
        $trip = Trip::with(['route.stops', 'bus.seats', 'driver', 'bookings.seats'])->findOrFail($id);

        $bookedSeatIds = BookingSeat::whereHas('booking', function($q) use ($trip) {
            $q->where('trip_id', $trip->id)->where('status', '!=', 'CANCELLED');
        })->pluck('seat_id')->toArray();

        return Inertia::render('Trips/Show', [
            'trip' => $trip,
            'booked_seat_ids' => $bookedSeatIds,
        ]);
    }

    /**
     * Đặt Vé
     */
    public function book(Request $request, $id)
    {
        $trip = Trip::with(['bus', 'route'])->findOrFail($id);

        $validated = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
            'seat_ids' => 'required|array|min:1',
            'payment_type' => 'required|in:DEPOSIT_30,FULL_100',
            'dropoff_location' => 'nullable|string',
        ]);

        $seatCount = count($validated['seat_ids']);
        $unitPrice = $trip->price ?? $trip->base_price ?? 140000;
        $totalPrice = $unitPrice * $seatCount;
        $paidAmount = $validated['payment_type'] === 'DEPOSIT_30' ? round($totalPrice * 0.3) : $totalPrice;

        $ticketCode = 'BH-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'trip_id' => $trip->id,
            'user_id' => auth()->id() ?? 1,
            'customer_name' => $validated['passenger_name'],
            'customer_phone' => $validated['passenger_phone'],
            'booking_code' => $ticketCode,
            'total_seats' => $seatCount,
            'total_amount' => $totalPrice,
            'paid_amount' => $paidAmount,
            'payment_status' => $validated['payment_type'] === 'DEPOSIT_30' ? 'PARTIALLY_PAID' : 'PAID',
            'payment_method' => 'ONLINE_VNPAY',
            'booking_source' => 'ONLINE',
            'pickup_stop_id' => 1,
            'dropoff_stop_id' => 2,
            'qr_token' => Str::random(32),
        ]);

        foreach ($validated['seat_ids'] as $seatId) {
            BookingSeat::create([
                'booking_id' => $booking->id,
                'seat_id' => $seatId,
                'price' => $unitPrice,
            ]);
        }

        return redirect()->route('booking.ticket', ['code' => $ticketCode])
            ->with('success', 'Đặt vé thành công! Mã vé của bạn là: ' . $ticketCode);
    }

    /**
     * Xem Vé Điện Tử
     */
    public function ticket($code)
    {
        $booking = Booking::with(['trip.route.stops', 'trip.bus', 'trip.driver', 'seats.seat'])
            ->where('booking_code', $code)
            ->orWhere('qr_token', $code)
            ->firstOrFail();

        return Inertia::render('Booking/Ticket', [
            'booking' => $booking
        ]);
    }
}
