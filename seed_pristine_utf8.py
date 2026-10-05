import sqlite3
import random
from datetime import datetime, timedelta

db_path = r'E:\bushub-booking\database\database.sqlite'
conn = sqlite3.connect(db_path)
cur = conn.cursor()

# 1. Truncate tables
tables_to_clear = ['booking_seats', 'bookings', 'trips', 'route_stops', 'routes', 'drivers', 'buses']
for t in tables_to_clear:
    cur.execute(f"DELETE FROM {t}")
    cur.execute(f"DELETE FROM sqlite_sequence WHERE name='{t}'")

now_str = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

# 2. Insert 10 Buses
buses_data = [
    ('43B-012.34', 'Thaco Mobihome VIP 34 Phòng', 34, 2, 'ACTIVE'),
    ('43B-018.99', 'Hyundai Universe Luxury 36 Chỗ', 36, 2, 'ACTIVE'),
    ('43B-023.45', 'Tracomeco Limousine 22 Phòng Cung Điện', 22, 2, 'ACTIVE'),
    ('43B-034.56', 'Thaco King Long 36 Giường', 36, 2, 'ACTIVE'),
    ('43B-045.67', 'Samco Universe VIP 34 Giường', 34, 2, 'ACTIVE'),
    ('43B-056.78', 'Thaco Mobihome Premium 34 Giường', 34, 2, 'ACTIVE'),
    ('43B-067.89', 'Hyundai Tracomeco 36 Giường', 36, 2, 'ACTIVE'),
    ('43B-078.90', 'Thaco Auto Limousine 24 Phòng', 24, 2, 'ACTIVE'),
    ('43B-089.01', 'Hyundai Universe Express 34 Chỗ', 34, 2, 'ACTIVE'),
    ('43B-099.88', 'Thaco Mobihome Deluxe 36 Giường', 36, 2, 'MAINTENANCE'),
]

bus_ids = []
for plate, b_type, seats, floors, status in buses_data:
    cur.execute("""
        INSERT INTO buses (license_plate, bus_type, total_seats, floors, status, current_km, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 125000, ?, ?)
    """, (plate, b_type, seats, floors, status, now_str, now_str))
    bus_ids.append(cur.lastrowid)

# 3. Insert 10 Drivers
drivers_data = [
    ('Nguyễn Văn Hùng', '0905.123.456', 12, 4.9),
    ('Trần Đình Trọng', '0905.234.567', 10, 4.8),
    ('Lê Hoàng Nam', '0905.345.678', 8, 4.9),
    ('Phạm Quốc Bảo', '0905.456.789', 15, 5.0),
    ('Hoàng Minh Tuấn', '0905.567.890', 9, 4.7),
    ('Đặng Văn Lâm', '0905.678.901', 11, 4.9),
    ('Bùi Tiến Dũng', '0905.789.012', 7, 4.8),
    ('Hồ Tấn Tài', '0905.890.123', 6, 4.9),
    ('Vũ Văn Thanh', '0905.901.234', 13, 5.0),
    ('Trần Văn Kiên', '0905.012.345', 14, 4.8),
]

driver_ids = []
for idx, (name, phone, exp, rating) in enumerate(drivers_data):
    cur.execute("""
        INSERT INTO drivers (name, phone, license_number, license_class, years_experience, avg_rating, created_at, updated_at)
        VALUES (?, ?, ?, 'E', ?, ?, ?, ?)
    """, (name, phone, f"GPLX-4300{idx+1:04d}", exp, rating, now_str, now_str))
    driver_ids.append(cur.lastrowid)

# 4. Insert 10 Routes (2-way khứ hồi)
routes_data = [
    ('Đà Nẵng ⇄ Thừa Thiên - Huế', 'Đà Nẵng', 'Thừa Thiên - Huế', 100, 2.5, [
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng'),
        ('Trạm Thu Phí Phú Bài', 'Thị xã Hương Thủy, TT-Huế'),
        ('Bến xe Phía Nam Huế', '57 An Dương Vương, TP. Huế')
    ]),
    ('Thừa Thiên - Huế ⇄ Đà Nẵng', 'Thừa Thiên - Huế', 'Đà Nẵng', 100, 2.5, [
        ('Bến xe Phía Nam Huế', '57 An Dương Vương, TP. Huế'),
        ('Trạm Thu Phí Phú Bài', 'Thị xã Hương Thủy, TT-Huế'),
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng')
    ]),
    ('Đà Nẵng ⇄ Quảng Trị (Đông Hà)', 'Đà Nẵng', 'Quảng Trị', 165, 3.5, [
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng'),
        ('Trạm dừng Hải Lăng', 'QL1A, Hải Lăng, Quảng Trị'),
        ('Bến xe Đông Hà', '425 Lê Duẩn, TP. Đông Hà')
    ]),
    ('Quảng Trị (Đông Hà) ⇄ Đà Nẵng', 'Quảng Trị', 'Đà Nẵng', 165, 3.5, [
        ('Bến xe Đông Hà', '425 Lê Duẩn, TP. Đông Hà'),
        ('Trạm dừng Hải Lăng', 'QL1A, Hải Lăng, Quảng Trị'),
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng')
    ]),
    ('Đà Nẵng ⇄ Quảng Bình (Đồng Hới)', 'Đà Nẵng', 'Quảng Bình', 270, 5.5, [
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng'),
        ('Trạm dừng Lệ Thủy', 'QL1A, Lệ Thủy, Quảng Bình'),
        ('Bến xe Đồng Hới', 'Trần Hưng Đạo, TP. Đồng Hới')
    ]),
    ('Quảng Bình (Đồng Hới) ⇄ Đà Nẵng', 'Quảng Bình', 'Đà Nẵng', 270, 5.5, [
        ('Bến xe Đồng Hới', 'Trần Hưng Đạo, TP. Đồng Hới'),
        ('Trạm dừng Lệ Thủy', 'QL1A, Lệ Thủy, Quảng Bình'),
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng')
    ]),
    ('Đà Nẵng ⇄ Quy Nhơn (Bình Định)', 'Đà Nẵng', 'Quy Nhơn', 315, 6.0, [
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng'),
        ('Bến xe Quảng Ngãi', 'QL1A, TP. Quảng Ngãi'),
        ('Bến xe Quy Nhơn', '71 Tây Sơn, TP. Quy Nhơn')
    ]),
    ('Quy Nhơn (Bình Định) ⇄ Đà Nẵng', 'Quy Nhơn', 'Đà Nẵng', 315, 6.0, [
        ('Bến xe Quy Nhơn', '71 Tây Sơn, TP. Quy Nhơn'),
        ('Bến xe Quảng Ngãi', 'QL1A, TP. Quảng Ngãi'),
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng')
    ]),
    ('Đà Nẵng ⇄ Buôn Ma Thuột (Đắk Lắk)', 'Đà Nẵng', 'Buôn Ma Thuột', 480, 9.5, [
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng'),
        ('Bến xe Pleiku', '43 Lý Nam Đế, TP. Pleiku'),
        ('Bến xe Liên Tỉnh Đắk Lắk', 'Nguyễn Tất Thành, TP. Buôn Ma Thuột')
    ]),
    ('Buôn Ma Thuột (Đắk Lắk) ⇄ Đà Nẵng', 'Buôn Ma Thuột', 'Đà Nẵng', 480, 9.5, [
        ('Bến xe Liên Tỉnh Đắk Lắk', 'Nguyễn Tất Thành, TP. Buôn Ma Thuột'),
        ('Bến xe Pleiku', '43 Lý Nam Đế, TP. Pleiku'),
        ('Bến xe Trung tâm Đà Nẵng', '185 Tôn Đức Thắng, TP. Đà Nẵng')
    ]),
]

route_id_map = {}
route_stops_map = {}

for r_name, r_orig, r_dest, r_km, r_hours, stops in routes_data:
    cur.execute("""
        INSERT INTO routes (name, origin, destination, distance_km, estimated_hours, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    """, (r_name, r_orig, r_dest, r_km, r_hours, now_str, now_str))
    r_id = cur.lastrowid
    route_id_map[r_name] = r_id
    route_stops_map[r_id] = []
    
    for order, (s_name, s_addr) in enumerate(stops, 1):
        cur.execute("""
            INSERT INTO route_stops (route_id, stop_name, address, stop_order, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?)
        """, (r_id, s_name, s_addr, order, now_str, now_str))
        route_stops_map[r_id].append(cur.lastrowid)

# 5. Trips Schedule Matrix
schedule_template = [
    (0, 0, 'Đà Nẵng ⇄ Thừa Thiên - Huế', '06:30', '09:00', 120000, 'COMPLETED'),
    (0, 0, 'Thừa Thiên - Huế ⇄ Đà Nẵng', '12:30', '15:00', 120000, 'RUNNING'),
    (0, 0, 'Đà Nẵng ⇄ Thừa Thiên - Huế', '17:30', '20:00', 120000, 'SCHEDULED'),

    (1, 1, 'Đà Nẵng ⇄ Thừa Thiên - Huế', '08:00', '10:30', 120000, 'COMPLETED'),
    (1, 1, 'Thừa Thiên - Huế ⇄ Đà Nẵng', '14:30', '17:00', 120000, 'SCHEDULED'),

    (2, 2, 'Đà Nẵng ⇄ Quảng Trị (Đông Hà)', '07:00', '10:30', 140000, 'COMPLETED'),
    (2, 2, 'Quảng Trị (Đông Hà) ⇄ Đà Nẵng', '13:30', '17:00', 140000, 'RUNNING'),

    (3, 3, 'Đà Nẵng ⇄ Quảng Bình (Đồng Hới)', '06:00', '11:30', 190000, 'COMPLETED'),
    (3, 3, 'Quảng Bình (Đồng Hới) ⇄ Đà Nẵng', '14:00', '19:30', 190000, 'SCHEDULED'),

    (4, 4, 'Đà Nẵng ⇄ Quy Nhơn (Bình Định)', '07:30', '13:30', 220000, 'RUNNING'),
    (4, 4, 'Quy Nhơn (Bình Định) ⇄ Đà Nẵng', '15:30', '21:30', 220000, 'SCHEDULED'),

    (5, 5, 'Đà Nẵng ⇄ Buôn Ma Thuột (Đắk Lắk)', '20:00', '05:30', 290000, 'SCHEDULED'),

    (6, 6, 'Đà Nẵng ⇄ Thừa Thiên - Huế', '09:00', '11:30', 120000, 'COMPLETED'),
    (6, 6, 'Thừa Thiên - Huế ⇄ Đà Nẵng', '16:00', '18:30', 120000, 'SCHEDULED'),

    (7, 7, 'Đà Nẵng ⇄ Quy Nhơn (Bình Định)', '10:00', '16:00', 220000, 'RUNNING'),
    (7, 7, 'Quy Nhơn (Bình Định) ⇄ Đà Nẵng', '18:00', '23:59', 220000, 'SCHEDULED'),

    (8, 8, 'Đà Nẵng ⇄ Quảng Trị (Đông Hà)', '09:30', '13:00', 140000, 'COMPLETED'),
    (8, 8, 'Quảng Trị (Đông Hà) ⇄ Đà Nẵng', '16:30', '20:00', 140000, 'SCHEDULED'),

    (9, 9, 'Đà Nẵng ⇄ Thừa Thiên - Huế', '09:30', '12:00', 120000, 'CANCELLED'),
]

# Loyal Customers List (Some booked 3-6 times with high spend for CRM)
loyal_customers = [
    ('Nguyễn Thị Thu Hà', '0912.345.678', 'thuha.nguyen@gmail.com', 'VIP Kim Cương'),
    ('Trần Văn Mạnh', '0913.456.789', 'manh.tran@gmail.com', 'VIP Vàng'),
    ('Lê Thị Mỹ Linh', '0914.567.890', 'mylinh.le@gmail.com', 'VIP Vàng'),
    ('Phạm Hoàng Long', '0915.678.901', 'hoanglong.pham@gmail.com', 'Khách Thân Thiết'),
    ('Đặng Minh Quân', '0916.789.012', 'minhquan.dang@gmail.com', 'Khách Thân Thiết'),
    ('Bùi Thanh Thảo', '0917.890.123', 'thanhthao.bui@gmail.com', 'VIP Kim Cương'),
    ('Võ Quốc Hưng', '0918.901.234', 'quochung.vo@gmail.com', 'Khách Thân Thiết'),
    ('Hoàng Ngọc Ánh', '0919.012.345', 'ngocanh.hoang@gmail.com', 'VIP Vàng'),
    ('Đỗ Gia Bảo', '0911.223.344', 'giabao.do@gmail.com', 'Khách Mới'),
    ('Phan Yến Nhi', '0912.334.455', 'yennhi.phan@gmail.com', 'Khách Thân Thiết'),
    ('Nguyễn Hải Đăng', '0913.556.677', 'haidang.nguyen@gmail.com', 'VIP Kim Cương'),
    ('Trương Mỹ Duyên', '0914.778.899', 'myduyen.truong@gmail.com', 'VIP Vàng'),
]

seat_pool = [
    'Ghế A01', 'Ghế A02', 'Ghế A03', 'Ghế A04', 'Ghế A05',
    'Ghế B01', 'Ghế B02', 'Ghế B03', 'Ghế B04', 'Ghế B05',
    'Giường T1-01', 'Giường T1-02', 'Giường T1-03', 'Giường T1-04', 'Giường T1-05',
    'Giường T2-01', 'Giường T2-02', 'Giường T2-03', 'Giường T2-04', 'Giường T2-05',
]

today = datetime.now().date()
trip_counter = 1
booking_counter = 1001

for day_offset in range(-2, 3):
    current_date = today + timedelta(days=day_offset)
    date_str = current_date.strftime('%Y-%m-%d')
    
    for b_idx, d_idx, r_name, dep_t, arr_t, price, status in schedule_template:
        b_id = bus_ids[b_idx]
        d_id = driver_ids[d_idx]
        r_id = route_id_map[r_name]
        
        dep_dt = f"{date_str} {dep_t}:00"
        arr_dt = f"{date_str} {arr_t}:00"
        
        trip_status = status
        if day_offset < 0 and trip_status != 'CANCELLED':
            trip_status = 'COMPLETED'
        elif day_offset > 0 and trip_status != 'CANCELLED':
            trip_status = 'SCHEDULED'
            
        trip_code = f"BH-TRIP-{trip_counter:04d}"
        trip_counter += 1
        
        cur.execute("""
            INSERT INTO trips (trip_code, route_id, bus_id, driver_id, departure_time, arrival_time, base_price, floor1_price, floor2_price, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        """, (trip_code, r_id, b_id, d_id, dep_dt, arr_dt, price, price, price - 10000, trip_status, now_str, now_str))
        t_id = cur.lastrowid
        
        # Add realistic bookings for non-cancelled trips
        if trip_status != 'CANCELLED':
            stops = route_stops_map[r_id]
            pickup_id = stops[0]
            dropoff_id = stops[-1]
            
            num_passengers = random.randint(6, 12)
            used_seats = random.sample(seat_pool, min(num_passengers * 2, len(seat_pool)))
            seat_idx = 0
            
            for p_idx in range(num_passengers):
                cust = loyal_customers[(p_idx + b_idx) % len(loyal_customers)]
                seats_for_p = random.randint(1, 2)
                assigned_seats = used_seats[seat_idx:seat_idx+seats_for_p]
                seat_idx += seats_for_p
                
                total_amt = price * seats_for_p
                is_deposit = random.choice([True, False])
                paid_amt = round(total_amt * 0.3) if is_deposit else total_amt
                pay_status = 'PARTIALLY_PAID' if is_deposit else 'PAID'
                checkin_stat = 'CHECKED_IN' if trip_status == 'COMPLETED' or (trip_status == 'RUNNING' and p_idx < 4) else 'PENDING'
                
                b_code = f"BH-BK-{booking_counter:06d}"
                booking_counter += 1
                
                notes_text = f"Ghế: {', '.join(assigned_seats)}"
                
                cur.execute("""
                    INSERT INTO bookings (booking_code, trip_id, user_id, customer_name, customer_phone, customer_email,
                                         pickup_stop_id, dropoff_stop_id, total_seats, total_amount, paid_amount,
                                         payment_status, payment_method, checkin_status, qr_token, notes, created_at, updated_at)
                    VALUES (?, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ONLINE_VNPAY', ?, ?, ?, ?, ?)
                """, (b_code, t_id, cust[0], cust[1], cust[2], pickup_id, dropoff_id, seats_for_p, total_amt, paid_amt,
                      pay_status, checkin_stat, f"QR_{b_code}_{random.randint(1000,9999)}", notes_text, now_str, now_str))
                b_id_new = cur.lastrowid
                
                for s_num in assigned_seats:
                    cur.execute("""
                        INSERT INTO booking_seats (booking_id, trip_id, seat_number, floor, price, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    """, (b_id_new, t_id, s_num, 1 if 'T1' in s_num or 'A' in s_num else 2, price, now_str, now_str))

conn.commit()
conn.close()
print("Clean seeding completed successfully with 100% UTF-8 and seating records!")
