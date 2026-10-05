<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <AdminHeader title="Quản Lý Đội Xe & Chi Tiết Lượt Chạy" subtitle="Theo dõi 10 phương tiện, lượt chạy trong ngày, danh sách hành khách và số ghế ngồi" />

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      <!-- Header & Bộ Lọc Điều Khiển -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
          <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
            <span>🚌</span>
            <span>Quản Lý Đội Xe & Danh Sách Ghế Ngồi Hành Khách</span>
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Xem chung toàn bộ 10 xe hoặc lọc xem riêng từng xe để kiểm tra số lượng khách và vị trí ghế ngồi
          </p>
        </div>

        <!-- Bộ lọc Ngày + Chọn Chế Độ Xem -->
        <div class="flex flex-wrap items-center gap-3">
          <!-- Chọn ngày -->
          <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <label class="text-xs font-bold text-slate-600 flex items-center gap-1">
              <span>📅</span>
              <span>Ngày xem:</span>
            </label>
            <input
              type="date"
              v-model="currentDate"
              @change="applyFilters"
              class="text-xs font-bold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 focus:outline-indigo-500 cursor-pointer"
            />
          </div>

          <!-- Dropdown Lọc Xe -->
          <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <label class="text-xs font-bold text-slate-600 flex items-center gap-1">
              <span>🚍</span>
              <span>Chọn xe:</span>
            </label>
            <select
              v-model="currentBusId"
              @change="handleBusSelect"
              class="text-xs font-bold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 focus:outline-indigo-500 cursor-pointer"
            >
              <option value="ALL">-- Xem chung tất cả 10 xe --</option>
              <option v-for="b in buses_list" :key="b.id" :value="b.id">
                {{ b.license_plate }} ({{ b.bus_type }})
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Chế độ Switcher: Thẻ chuyển đổi nhanh -->
      <div class="flex items-center gap-2 mt-6">
        <button
          @click="currentBusId = 'ALL'"
          class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
          :class="currentBusId === 'ALL' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
        >
          <span>📊</span>
          <span>Xem chung tất cả 10 xe</span>
        </button>

        <button
          v-if="currentBusId !== 'ALL'"
          class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-sm flex items-center gap-1.5"
        >
          <span>🔍</span>
          <span>Đang xem riêng xe: {{ selectedBusData?.license_plate }}</span>
        </button>
      </div>

      <!-- ==================== 1. CHẾ ĐỘ: XEM CHUNG TOÀN BỘ 10 XE ==================== -->
      <div v-if="currentBusId === 'ALL'" class="mt-6 space-y-6">
        <!-- Thống kê nhanh toàn đội xe -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-400 uppercase">Tổng Xe Hoạt Động</p>
              <p class="text-2xl font-black text-slate-900 mt-1">{{ activeBusesCount }} / {{ buses_list.length }} xe</p>
              <p class="text-xs text-emerald-600 font-semibold mt-0.5">✓ 9 xe sẵn sàng chạy</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">🚌</div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-400 uppercase">Tổng Lượt Chạy Trong Ngày</p>
              <p class="text-2xl font-black text-indigo-600 mt-1">{{ totalTripsCount }} lượt</p>
              <p class="text-xs text-slate-500 font-medium mt-0.5">Chạy 2 chiều khứ hồi</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">🚍</div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-400 uppercase">Tổng Khách Đón</p>
              <p class="text-2xl font-black text-purple-600 mt-1">{{ totalPassengersCount }} khách</p>
              <p class="text-xs text-slate-500 font-medium mt-0.5">Lưu trữ từ DB đặt vé</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">👥</div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-xs flex items-center justify-between bg-emerald-50/20">
            <div>
              <p class="text-xs font-bold text-emerald-700 uppercase">Tổng Doanh Thu Ngày</p>
              <p class="text-xl font-black text-emerald-700 mt-1">{{ formatCurrency(totalRevenueSum) }}</p>
              <p class="text-xs text-emerald-800 font-medium mt-0.5">Đã bao gồm cọc 30%</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">💰</div>
          </div>
        </div>

        <!-- Bảng Danh Sách 10 Xe -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <h2 class="font-bold text-base text-slate-900">Bảng Quản Lý Toàn Bộ 10 Xe (Ngày {{ currentDate }})</h2>
            <span class="text-xs text-slate-500">Bấm nút "Xem hành khách & ghế" để xem chi tiết từng xe</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
              <thead class="bg-slate-100/80 text-slate-600 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
                <tr>
                  <th class="px-5 py-3">Biển Số Xe</th>
                  <th class="px-5 py-3">Loại Xe / Sức Chứa</th>
                  <th class="px-5 py-3">Tài Xế Phụ Trách</th>
                  <th class="px-5 py-3 text-center">Lượt Chạy / Ngày</th>
                  <th class="px-5 py-3 text-center">Khách Đã Đón</th>
                  <th class="px-5 py-3 text-right">Doanh Thu Thu Được</th>
                  <th class="px-5 py-3 text-center">Trạng Thái</th>
                  <th class="px-5 py-3 text-center">Hành Động</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="bus in buses_list"
                  :key="bus.id"
                  class="hover:bg-slate-50/80 transition"
                  :class="bus.status === 'MAINTENANCE' ? 'bg-red-50/30' : ''"
                >
                  <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
                    <div class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full" :class="bus.status === 'ACTIVE' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                      <span>{{ bus.license_plate }}</span>
                    </div>
                  </td>
                  <td class="px-5 py-3.5">
                    <span class="font-medium text-slate-800">{{ bus.bus_type }}</span>
                    <span class="text-xs text-slate-400 block">{{ bus.total_seats }} chỗ</span>
                  </td>
                  <td class="px-5 py-3.5 font-medium text-slate-800">
                    {{ bus.driver_name }}
                  </td>
                  <td class="px-5 py-3.5 text-center">
                    <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded text-xs">
                      {{ bus.trips_count }} lượt
                    </span>
                  </td>
                  <td class="px-5 py-3.5 text-center font-bold text-slate-700">
                    {{ bus.total_passengers }} khách
                  </td>
                  <td class="px-5 py-3.5 text-right font-bold text-emerald-700 font-mono">
                    {{ formatCurrency(bus.total_revenue) }}
                  </td>
                  <td class="px-5 py-3.5 text-center">
                    <span
                      class="px-2.5 py-1 rounded-full text-[11px] font-bold"
                      :class="bus.status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                    >
                      {{ bus.status === 'ACTIVE' ? 'Đang hoạt động' : 'Bị hủy / Bảo dưỡng' }}
                    </span>
                  </td>
                  <td class="px-5 py-3.5 text-center">
                    <button
                      @click="currentBusId = bus.id"
                      class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition cursor-pointer flex items-center gap-1 mx-auto"
                    >
                      <span>🔍</span>
                      <span>Xem khách & ghế</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== 2. CHẾ ĐỘ: XEM RIÊNG TỪNG XE (DANH SÁCH KHÁCH & VỊ TRÍ GHẾ NGỒI) ==================== -->
      <div v-else class="mt-6 space-y-6">
        <!-- Chi tiết xe đang chọn -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-black">
                🚌
              </div>
              <div>
                <div class="flex items-center gap-2">
                  <h2 class="text-xl font-black text-slate-900 font-mono">{{ selectedBusData?.license_plate }}</h2>
                  <span
                    class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                    :class="selectedBusData?.status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                  >
                    {{ selectedBusData?.status === 'ACTIVE' ? 'Đang hoạt động' : 'Bảo dưỡng định kỳ' }}
                  </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                  {{ selectedBusData?.bus_type }} • Sức chứa: <b>{{ selectedBusData?.total_seats }} chỗ</b> • Tài xế: <b>{{ selectedBusData?.driver_name }}</b>
                </p>
              </div>
            </div>

            <!-- Thống kê của xe trong ngày -->
            <div class="flex items-center gap-4 text-xs">
              <div class="text-center px-3 py-2 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-slate-400 font-semibold block">Lượt chạy</span>
                <span class="text-base font-black text-indigo-600">{{ selectedBusData?.trips_count }} lượt</span>
              </div>
              <div class="text-center px-3 py-2 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-slate-400 font-semibold block">Tổng khách</span>
                <span class="text-base font-black text-purple-600">{{ selectedBusData?.total_passengers }} khách</span>
              </div>
              <div class="text-center px-3 py-2 bg-emerald-50 rounded-xl border border-emerald-200">
                <span class="text-emerald-700 font-semibold block">Doanh thu xe</span>
                <span class="text-base font-black text-emerald-700 font-mono">{{ formatCurrency(selectedBusData?.total_revenue) }}</span>
              </div>
            </div>
          </div>

          <!-- Danh sách từng lượt chạy của xe đó -->
          <div class="mt-6 space-y-6">
            <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
              <span>📋</span>
              <span>Các Chuyến Chạy Trong Ngày ({{ currentDate }}) & Danh Sách Ghế Ngồi Hành Khách</span>
            </h3>

            <div v-if="selectedBusData?.trips.length === 0" class="text-center py-10 bg-slate-50 rounded-xl border border-slate-200">
              <p class="text-slate-500 text-xs">Phương tiện không có lịch chạy trong ngày {{ currentDate }}.</p>
            </div>

            <!-- Từng Chuyến Của Xe -->
            <div
              v-for="trip in selectedBusData?.trips"
              :key="trip.id"
              class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50 space-y-4"
            >
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
                <div>
                  <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">{{ trip.trip_code }}</span>
                    <h4 class="font-black text-slate-900 text-sm sm:text-base">{{ trip.route_name }}</h4>
                    <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded">
                      {{ trip.status }}
                    </span>
                  </div>
                  <p class="text-xs text-slate-500 mt-1">
                    Khởi hành: <b>{{ trip.departure_time }}</b> → Đến: <b>{{ trip.arrival_time }}</b> | Tài xế: <b>{{ trip.driver_name }} ({{ trip.driver_phone }})</b>
                  </p>
                </div>

                <div class="text-right">
                  <span class="text-xs font-bold text-slate-700 block">Đón: <b>{{ trip.passengers_count }} khách</b></span>
                  <span class="text-sm font-black text-emerald-700 font-mono">{{ formatCurrency(trip.revenue) }}</span>
                </div>
              </div>

              <!-- Bảng Hành Khách & Vị Trí Ghế Ngồi -->
              <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-2xs">
                <table class="w-full text-left text-xs">
                  <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200">
                    <tr>
                      <th class="px-4 py-2.5">Mã Vé</th>
                      <th class="px-4 py-2.5">Tên Hành Khách</th>
                      <th class="px-4 py-2.5">Số Điện Thoại</th>
                      <th class="px-4 py-2.5 text-center bg-indigo-50/80 text-indigo-900 font-black">VỊ TRÍ GHẾ NGỒI</th>
                      <th class="px-4 py-2.5">Điểm Đón → Trả</th>
                      <th class="px-4 py-2.5 text-right">Tiền Vé / Cọc</th>
                      <th class="px-4 py-2.5 text-center">Thanh Toán</th>
                      <th class="px-4 py-2.5 text-center">Trạng Thái</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="p in trip.passengers" :key="p.id" class="hover:bg-slate-50/80">
                      <td class="px-4 py-2.5 font-mono font-bold text-indigo-600">
                        {{ p.booking_code }}
                      </td>
                      <td class="px-4 py-2.5 font-bold text-slate-900">
                        {{ p.name }}
                      </td>
                      <td class="px-4 py-2.5 font-mono text-slate-600">
                        {{ p.phone }}
                      </td>
                      <td class="px-4 py-2.5 text-center bg-indigo-50/40">
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-black text-xs shadow-2xs">
                          💺 {{ p.seats }}
                        </span>
                      </td>
                      <td class="px-4 py-2.5 text-slate-600">
                        <span>{{ p.pickup_stop }}</span>
                        <span class="text-slate-400 mx-1">→</span>
                        <span>{{ p.dropoff_stop }}</span>
                      </td>
                      <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900">
                        <div>{{ formatCurrency(p.amount) }}</div>
                        <div v-if="p.payment_status === 'PARTIALLY_PAID'" class="text-[10px] text-amber-600">
                          (Đã cọc 30%: {{ formatCurrency(p.paid_amount) }})
                        </div>
                      </td>
                      <td class="px-4 py-2.5 text-center">
                        <span
                          class="px-2 py-0.5 rounded text-[10px] font-bold"
                          :class="p.payment_status === 'PAID' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                        >
                          {{ p.payment_status === 'PAID' ? 'Đã thanh toán 100%' : 'Cọc 30% (Chờ thu 70%)' }}
                        </span>
                      </td>
                      <td class="px-4 py-2.5 text-center">
                        <span
                          class="px-2 py-0.5 rounded text-[10px] font-bold"
                          :class="p.checkin_status === 'CHECKED_IN' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600'"
                        >
                          {{ p.checkin_status === 'CHECKED_IN' ? '✓ Đã lên xe' : 'Chờ đón' }}
                        </span>
                      </td>
                    </tr>
                    <tr v-if="trip.passengers.length === 0">
                      <td colspan="8" class="text-center py-4 text-slate-400">
                        Chưa có hành khách đặt vé cho chuyến này
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <AiChatModal />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminHeader from '@/Components/AdminHeader.vue';
import AiChatModal from '@/Components/AiChatModal.vue';

const props = defineProps({
  buses_list: Array,
  selected_date: String,
  selected_bus_id: [String, Number],
  all_buses: Array,
});

const currentDate = ref(props.selected_date || new Date().toISOString().split('T')[0]);
const currentBusId = ref(props.selected_bus_id || 'ALL');

const selectedBusData = computed(() => {
  if (currentBusId.value === 'ALL') return null;
  return props.buses_list.find(b => b.id == currentBusId.value) || props.buses_list[0];
});

const activeBusesCount = computed(() => {
  return props.buses_list.filter(b => b.status === 'ACTIVE').length;
});

const totalTripsCount = computed(() => {
  return props.buses_list.reduce((sum, b) => sum + (b.trips_count || 0), 0);
});

const totalPassengersCount = computed(() => {
  return props.buses_list.reduce((sum, b) => sum + (b.total_passengers || 0), 0);
});

const totalRevenueSum = computed(() => {
  return props.buses_list.reduce((sum, b) => sum + (b.total_revenue || 0), 0);
});

const applyFilters = () => {
  router.get('/admin/buses', {
    date: currentDate.value,
    bus_id: currentBusId.value,
  }, { preserveState: true });
};

const handleBusSelect = () => {
  applyFilters();
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
};
</script>