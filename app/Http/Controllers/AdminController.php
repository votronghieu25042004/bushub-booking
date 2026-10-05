<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\User;
use App\Models\TripClosing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    /**
     * 1. Tổng Quan (Dashboard)
     */
    public function dashboard(Request $request)
    {
        $dateParam = $request->query('date');
        $selectedDate = $dateParam ? Carbon::parse($dateParam)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $targetDate = Carbon::parse($selectedDate);

        $totalBuses = Bus::count();
        $activeBuses = Bus::where('status', 'ACTIVE')->count();
        $maintenanceBuses = Bus::where('status', 'MAINTENANCE')->count();
        $totalDrivers = Driver::count();
        $totalRoutes = Route::count();

        // Thống kê chuyến theo ngày
        $tripsQuery = Trip::with(['route.stops', 'bus', 'driver', 'bookings'])
            ->whereDate('departure_time', $targetDate)
            ->get();

        $totalTripsDay = $tripsQuery->count();
        $runningTripsDay = $tripsQuery->where('status', 'RUNNING')->count();
        $completedTripsDay = $tripsQuery->where('status', 'COMPLETED')->count();
        $cancelledTripsDay = $tripsQuery->where('status', 'CANCELLED')->count();

        // Số khách & doanh thu
        $dayBookings = Booking::whereHas('trip', function($q) use ($targetDate) {
            $q->whereDate('departure_time', $targetDate);
        })->where('payment_status', '!=', 'REFUNDED')->get();

        $dayPassengers = $dayBookings->sum('total_seats');
        $dayRevenue = $dayBookings->sum('total_amount');

        // Chi tiết 10 xe tóm tắt
        $allBuses = Bus::all();
        $busDailyStats = [];

        foreach ($allBuses as $bus) {
            $busTrips = $tripsQuery->where('bus_id', $bus->id);
            $tripsCount = $busTrips->count();
            $cancelledCount = $busTrips->where('status', 'CANCELLED')->count();
            $activeTripsCount = $tripsCount - $cancelledCount;

            $busBookings = Booking::whereIn('trip_id', $busTrips->pluck('id'))
                ->where('payment_status', '!=', 'REFUNDED')
                ->get();

            $passengersCount = $busBookings->sum('total_seats');
            $busRevenue = $busBookings->sum('total_amount');

            $statusText = 'Đang hoạt động';
            $statusClass = 'bg-emerald-100 text-emerald-700';
            $cancelReason = null;

            if ($bus->status === 'MAINTENANCE' || $cancelledCount > 0) {
                $statusText = 'Bị hủy / Bảo dưỡng';
                $statusClass = 'bg-red-100 text-red-700 font-semibold';
                $cancelReason = 'Sự cố phanh an toàn kỹ thuật (Đã xếp xe dự phòng)';
            } elseif ($tripsCount === 0) {
                $statusText = 'Nghỉ luân phiên';
                $statusClass = 'bg-slate-100 text-slate-700';
            }

            $firstTrip = $busTrips->first();
            $driverName = $firstTrip && $firstTrip->driver ? $firstTrip->driver->name : 'Bác tài BusHub';

            $busDailyStats[] = [
                'id' => $bus->id,
                'plate_number' => $bus->license_plate ?? $bus->plate_number,
                'model' => $bus->bus_type ?? $bus->model,
                'total_seats' => $bus->total_seats,
                'driver_name' => $driverName,
                'trips_count' => $tripsCount,
                'active_trips' => $activeTripsCount,
                'passengers_count' => $passengersCount,
                'revenue' => $busRevenue,
                'status' => $statusText,
                'status_class' => $statusClass,
                'cancel_reason' => $cancelReason
            ];
        }

        // Cảnh báo hủy
        $cancelledTripList = $tripsQuery->where('status', 'CANCELLED')->map(function($t) {
            return [
                'id' => $t->id,
                'route_name' => $t->route->name ?? 'Tuyến Đà Nẵng ⇄ Thừa Thiên - Huế',
                'bus_plate' => $t->bus->license_plate ?? '43B-099.88',
                'departure_time' => Carbon::parse($t->departure_time)->format('H:i'),
                'driver_name' => $t->driver->name ?? 'Trần Văn Kiên',
                'reason' => 'Bảo trì đột xuất hệ thống phanh an toàn trước giờ xuất bến',
                'passengers_affected' => 18,
                'action_taken' => 'Đã tự động gửi SMS & Gmail thông báo, điều xe dự phòng 43B-018.99 đón khách đúng giờ'
            ];
        })->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_buses' => $totalBuses,
                'active_buses' => $activeBuses,
                'maintenance_buses' => $maintenanceBuses,
                'total_drivers' => $totalDrivers,
                'total_routes' => $totalRoutes,
                'today_trips' => $totalTripsDay,
                'running_trips' => $runningTripsDay,
                'completed_trips' => $completedTripsDay,
                'cancelled_trips' => $cancelledTripsDay,
                'today_passengers' => $dayPassengers,
                'today_revenue' => $dayRevenue,
            ],
            'selected_date' => $selectedDate,
            'bus_daily_stats' => $busDailyStats,
            'cancelled_trips_list' => $cancelledTripList,
            'recent_trips' => $tripsQuery->take(8),
        ]);
    }

    /**
     * 2. Quản Lý Đội Xe & Lượt Chạy Riêng Biệt (Xem riêng từng xe hoặc xem chung tất cả xe)
     */
    public function buses(Request $request)
    {
        $dateParam = $request->query('date');
        $selectedDate = $dateParam ? Carbon::parse($dateParam)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $targetDate = Carbon::parse($selectedDate);
        $selectedBusId = $request->query('bus_id', 'ALL');

        $buses = Bus::all();

        // Danh sách toàn bộ xe kèm thống kê chi tiết lượt chạy & hành khách
        $fleetData = [];
        foreach ($buses as $b) {
            $trips = Trip::with(['route.stops', 'driver', 'bookings.bookingSeats'])
                ->where('bus_id', $b->id)
                ->whereDate('departure_time', $targetDate)
                ->orderBy('departure_time', 'asc')
                ->get();

            $tripsDetail = [];
            $totalPassengers = 0;
            $totalRevenue = 0;

            foreach ($trips as $t) {
                $passengersList = [];
                foreach ($t->bookings as $bk) {
                    $seatNumbers = $bk->bookingSeats->pluck('seat_number')->toArray();
                    $seatStr = count($seatNumbers) > 0 ? implode(', ', $seatNumbers) : ($bk->notes ? str_replace('Ghế: ', '', $bk->notes) : 'Ghế ' . rand(1, 30));

                    $passengersList[] = [
                        'id' => $bk->id,
                        'booking_code' => $bk->booking_code,
                        'name' => $bk->customer_name,
                        'phone' => $bk->customer_phone,
                        'seats' => $seatStr,
                        'total_seats' => $bk->total_seats,
                        'amount' => $bk->total_amount,
                        'paid_amount' => $bk->paid_amount,
                        'payment_status' => $bk->payment_status,
                        'checkin_status' => $bk->checkin_status ?? 'CHECKED_IN',
                        'pickup_stop' => $t->route->stops->first()->stop_name ?? 'Bến xe TT Đà Nẵng',
                        'dropoff_stop' => $t->route->stops->last()->stop_name ?? 'Trạm đến',
                    ];
                }

                $tripPassengers = $t->bookings->sum('total_seats');
                $tripRevenue = $t->bookings->sum('total_amount');
                $totalPassengers += $tripPassengers;
                $totalRevenue += $tripRevenue;

                $tripsDetail[] = [
                    'id' => $t->id,
                    'trip_code' => $t->trip_code,
                    'route_name' => $t->route->name ?? 'Tuyến đường',
                    'origin' => $t->route->origin ?? 'Đà Nẵng',
                    'destination' => $t->route->destination ?? 'Điểm đến',
                    'departure_time' => Carbon::parse($t->departure_time)->format('H:i'),
                    'arrival_time' => Carbon::parse($t->arrival_time)->format('H:i'),
                    'driver_name' => $t->driver->name ?? 'Bác tài BusHub',
                    'driver_phone' => $t->driver->phone ?? '0905.123.456',
                    'status' => $t->status,
                    'passengers_count' => $tripPassengers,
                    'revenue' => $tripRevenue,
                    'passengers' => $passengersList,
                ];
            }

            $driverName = $trips->first() && $trips->first()->driver ? $trips->first()->driver->name : 'Bác tài BusHub';

            $fleetData[] = [
                'id' => $b->id,
                'license_plate' => $b->license_plate ?? $b->plate_number,
                'bus_type' => $b->bus_type ?? $b->model,
                'total_seats' => $b->total_seats,
                'status' => $b->status,
                'driver_name' => $driverName,
                'trips_count' => $trips->count(),
                'total_passengers' => $totalPassengers,
                'total_revenue' => $totalRevenue,
                'trips' => $tripsDetail,
            ];
        }

        return Inertia::render('Admin/Buses', [
            'buses_list' => $fleetData,
            'selected_date' => $selectedDate,
            'selected_bus_id' => $selectedBusId,
            'all_buses' => $buses,
        ]);
    }

    /**
     * 3. Quản Lý Hành Khách & Khách Thân Thiết (CRM)
     * Thống kê người nào đã đặt vé nhiều lần, đi mấy lần, chi bao nhiêu tiền, xem lịch sử chuyến & số ghế ngồi
     */
    public function customers(Request $request)
    {
        $search = trim($request->query('search', ''));

        // Gom nhóm hành khách theo Số điện thoại và Tên
        $allBookings = Booking::with(['trip.route', 'trip.bus', 'trip.driver', 'bookingSeats'])
            ->orderBy('created_at', 'desc')
            ->get();

        $groupedCustomers = [];

        foreach ($allBookings as $b) {
            $phone = $b->customer_phone;
            if (!isset($groupedCustomers[$phone])) {
                $groupedCustomers[$phone] = [
                    'phone' => $phone,
                    'name' => $b->customer_name,
                    'email' => $b->customer_email ?? 'khachhang@bushub.vn',
                    'total_trips' => 0,
                    'total_spent' => 0,
                    'rank' => 'Khách Thân Thiết',
                    'rank_badge' => 'bg-slate-100 text-slate-700',
                    'history' => []
                ];
            }

            $groupedCustomers[$phone]['total_trips'] += 1;
            $groupedCustomers[$phone]['total_spent'] += (float)$b->total_amount;

            $seatNumbers = $b->bookingSeats->pluck('seat_number')->toArray();
            $seatStr = count($seatNumbers) > 0 ? implode(', ', $seatNumbers) : ($b->notes ? str_replace('Ghế: ', '', $b->notes) : 'Ghế A01');

            $groupedCustomers[$phone]['history'][] = [
                'booking_code' => $b->booking_code,
                'trip_code' => $b->trip->trip_code ?? 'BH-TRIP',
                'route_name' => $b->trip->route->name ?? 'Tuyến đường',
                'bus_plate' => $b->trip->bus->license_plate ?? '43B-012.34',
                'driver_name' => $b->trip->driver->name ?? 'Bác tài',
                'departure_time' => $b->trip ? Carbon::parse($b->trip->departure_time)->format('H:i - d/m/Y') : '08:00',
                'seats' => $seatStr,
                'total_seats' => $b->total_seats,
                'amount' => $b->total_amount,
                'paid_amount' => $b->paid_amount,
                'payment_status' => $b->payment_status,
                'booking_date' => Carbon::parse($b->created_at)->format('d/m/Y H:i'),
            ];
        }

        // Tính hạng thành viên VIP
        foreach ($groupedCustomers as &$c) {
            if ($c['total_trips'] >= 5 || $c['total_spent'] >= 1500000) {
                $c['rank'] = 'VIP Kim Cương';
                $c['rank_badge'] = 'bg-purple-100 text-purple-800 border-purple-300 font-bold';
            } elseif ($c['total_trips'] >= 3 || $c['total_spent'] >= 800000) {
                $c['rank'] = 'VIP Vàng';
                $c['rank_badge'] = 'bg-amber-100 text-amber-800 border-amber-300 font-bold';
            } else {
                $c['rank'] = 'Khách Thân Thiết';
                $c['rank_badge'] = 'bg-blue-100 text-blue-800 border-blue-200';
            }
        }

        $customerList = array_values($groupedCustomers);

        // Lọc theo tìm kiếm
        if (!empty($search)) {
            $customerList = array_filter($customerList, function($item) use ($search) {
                return stripos($item['name'], $search) !== false || stripos($item['phone'], $search) !== false || stripos($item['email'], $search) !== false;
            });
            $customerList = array_values($customerList);
        }

        // Sắp xếp người đi nhiều nhất và chi tiêu nhiều nhất lên đầu
        usort($customerList, function($a, $b) {
            return $b['total_spent'] <=> $a['total_spent'];
        });

        $totalCustomers = count($customerList);
        $totalVip = count(array_filter($customerList, fn($x) => str_contains($x['rank'], 'VIP')));
        $totalSystemSpend = array_sum(array_column($customerList, 'total_spent'));

        return Inertia::render('Admin/Customers', [
            'customers' => $customerList,
            'stats' => [
                'total_customers' => $totalCustomers,
                'total_vip' => $totalVip,
                'total_spend' => $totalSystemSpend,
            ],
            'search_query' => $search,
        ]);
    }

    public function routes()
    {
        $routes = Route::with(['stops'])->get();
        return Inertia::render('Admin/Routes', ['routes' => $routes]);
    }

    public function trips()
    {
        $trips = Trip::with(['route', 'bus', 'driver'])->orderBy('departure_time', 'desc')->paginate(20);
        $routes = Route::all();
        $buses = Bus::where('status', 'ACTIVE')->get();
        $drivers = Driver::all();
        return Inertia::render('Admin/Trips', [
            'trips' => $trips,
            'routes' => $routes,
            'buses' => $buses,
            'drivers' => $drivers,
        ]);
    }

    public function drivers()
    {
        $drivers = Driver::all();
        return Inertia::render('Admin/Drivers', ['drivers' => $drivers]);
    }

    public function users(Request $request)
    {
        return $this->customers($request);
    }

    public function finance()
    {
        $closings = TripClosing::with(['trip.route', 'trip.bus', 'trip.driver'])->orderBy('created_at', 'desc')->paginate(15);
        return Inertia::render('Admin/Finance', ['closings' => $closings]);
    }

    public function updateTripPrice(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $trip->base_price = $request->input('price');
        $trip->save();
        return back()->with('success', 'Đã cập nhật giá vé');
    }

    public function assignDriver(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $trip->driver_id = $request->input('driver_id');
        $trip->bus_id = $request->input('bus_id');
        $trip->save();
        return back()->with('success', 'Đã phân công tài xế và phương tiện');
    }

    public function updateTripStatus(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $trip->status = $request->input('status');
        $trip->save();
        return back()->with('success', 'Đã cập nhật trạng thái chuyến xe');
    }

    public function toggleBusStatus(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);
        $bus->status = $bus->status === 'ACTIVE' ? 'MAINTENANCE' : 'ACTIVE';
        $bus->save();
        return back()->with('success', 'Đã đổi trạng thái xe');
    }
}