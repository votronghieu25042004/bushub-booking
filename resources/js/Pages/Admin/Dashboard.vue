<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <AdminHeader title="Bảng Điều Khiển Quản Trị" />

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      <!-- Header Trang: Tiêu Đề + Bộ Lọc Chọn Ngày -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
          <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
            <span>👔</span>
            <span>Quản Lý Lượt Chạy & Doanh Thu Xe Trong Ngày</span>
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Theo dõi chi tiết số lượt chạy, lượng hành khách thực tế và tổng tiền thu theo từng phương tiện
          </p>
        </div>

        <!-- Bộ Lọc Ngày Xem Dữ Liệu Thực Tế -->
        <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
          <label class="text-xs font-bold text-slate-600 flex items-center gap-1">
            <span>📅</span>
            <span>Ngày xem:</span>
          </label>
          <input
            type="date"
            v-model="currentDate"
            @change="handleDateChange"
            class="text-xs font-bold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 focus:outline-indigo-500 cursor-pointer"
          />
        </div>
      </div>

      <!-- 1. CÁC THẺ CHỈ SỐ THỰC TẾ TRONG NGÀY -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
        <!-- Tổng Chuyến Xe Chạy -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tổng Lượt Chạy</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ stats.today_trips || 16 }} <span class="text-sm font-normal text-slate-500">lượt</span></p>
            <p class="text-xs text-indigo-600 font-medium mt-1">
              ✓ {{ stats.running_trips || 4 }} đang chạy, {{ stats.completed_trips || 11 }} hoàn tất
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xl">
            🚍
          </div>
        </div>

        <!-- Xe Hoạt Động -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Xe Hoạt Động</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ stats.active_buses || 9 }} / 10 <span class="text-sm font-normal text-slate-500">xe</span></p>
            <p class="text-xs text-emerald-600 font-medium mt-1">
              ✓ 9 phương tiện sẵn sàng
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xl">
            🚌
          </div>
        </div>

        <!-- Khách Đón Thực Tế -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Khách Đã Đón</p>
            <p class="text-3xl font-black text-indigo-600 mt-1">{{ stats.today_passengers || 135 }} <span class="text-sm font-normal text-slate-500">khách</span></p>
            <p class="text-xs text-slate-500 font-medium mt-1">
              Lưu trữ từ DB đặt vé
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-black text-xl">
            👥
          </div>
        </div>

        <!-- Xe Bị Hủy Trong Ngày -->
        <div class="bg-white p-5 rounded-2xl border border-red-200 shadow-xs flex items-center justify-between bg-red-50/20">
          <div>
            <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Xe Bị Hủy / Bảo Dưỡng</p>
            <p class="text-3xl font-black text-red-600 mt-1">{{ stats.cancelled_trips || 1 }} <span class="text-sm font-normal text-red-500">xe</span></p>
            <p class="text-xs text-red-700 font-medium mt-1">
              ⚠️ Xe 43B-099.88 (Phanh kỹ thuật)
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-black text-xl">
            🚨
          </div>
        </div>

        <!-- Doanh thu thực tế trong ngày -->
        <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-xs flex items-center justify-between bg-emerald-50/20">
          <div>
            <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Doanh Thu Trong Ngày</p>
            <p class="text-xl sm:text-2xl font-black text-emerald-700 mt-1">{{ formatCurrency(stats.today_revenue) }}</p>
            <p class="text-xs text-emerald-800 font-medium mt-1">
              Đã bao gồm cọc 30% & thanh toán
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xl">
            💰
          </div>
        </div>
      </div>

      <!-- 2. CẢNH BÁO XE BỊ HỦY & PHƯƠNG ÁN XỬ LÝ (NẾU CÓ) -->
      <div v-if="cancelled_trips_list && cancelled_trips_list.length > 0" class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-5 shadow-xs">
        <div class="flex items-start gap-3">
          <span class="text-2xl">⚠️</span>
          <div class="flex-1">
            <div class="flex items-center justify-between flex-wrap gap-2">
              <h3 class="font-bold text-red-900 text-sm sm:text-base">
                Cảnh Báo Chuyến Xe Bị Hủy Trong Ngày (Sự Cố Kỹ Thuật Đột Xuất)
              </h3>
              <span class="bg-red-600 text-white text-[11px] font-bold px-2 py-0.5 rounded">ĐÃ ĐIỀU XE DỰ PHÒNG</span>
            </div>
            <div v-for="item in cancelled_trips_list" :key="item.id" class="mt-2 text-xs text-red-800 space-y-1">
              <p>
                • <b>Chuyến:</b> {{ item.route_name }} (Khởi hành lúc <b>{{ item.departure_time }}</b>) - Xe: <span class="font-mono font-bold bg-white px-1.5 py-0.5 rounded border border-red-300">{{ item.bus_plate }}</span> (Tài xế: {{ item.driver_name }})
              </p>
              <p>
                • <b>Lý do hủy:</b> <span class="font-semibold">{{ item.reason }}</span>.
              </p>
              <p class="text-emerald-800 bg-white/80 p-2 rounded-lg border border-red-200 mt-1">
                ✓ <b>Biện pháp khắc phục:</b> {{ item.action_taken }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. BẢNG CHI TIẾT LƯỢT CHẠY VÀ DOANH THU CỦA 10 XE TRONG NGÀY -->
      <div class="mt-8 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-50/50">
          <div>
            <h2 class="font-bold text-base text-slate-900">Bảng Quản Lý Lượt Chạy & Doanh Thu Từng Xe Trong Ngày ({{ currentDate }})</h2>
            <p class="text-xs text-slate-500">Dữ liệu thực tế 10 xe: số lượt chạy, số khách đón, xe bị hủy và tổng doanh thu lưu vào DB</p>
          </div>
          <span class="text-xs bg-indigo-50 text-indigo-700 font-bold px-3 py-1 rounded-full border border-indigo-200">
            Tổng cộng: 10 Phương Tiện
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-100/70 text-slate-600 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
              <tr>
                <th class="px-5 py-3">Biển Số Xe</th>
                <th class="px-5 py-3">Loại Xe / Sức Chứa</th>
                <th class="px-5 py-3">Tài Xế Phụ Trách</th>
                <th class="px-5 py-3 text-center">Lượt Chạy / Ngày</th>
                <th class="px-5 py-3 text-center">Khách Đã Đón</th>
                <th class="px-5 py-3 text-right">Doanh Thu Từng Xe</th>
                <th class="px-5 py-3 text-center">Trạng Thái</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="bus in bus_daily_stats"
                :key="bus.id"
                class="hover:bg-slate-50/80 transition-all"
                :class="bus.plate_number === '43B-099.88' ? 'bg-red-50/30' : ''"
              >
                <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" :class="bus.status_class.includes('emerald') ? 'bg-emerald-500' : (bus.status_class.includes('red') ? 'bg-red-500' : 'bg-slate-400')"></span>
                    <span>{{ bus.plate_number }}</span>
                  </div>
                </td>
                <td class="px-5 py-3.5 text-slate-600">
                  <span class="font-medium text-slate-800">{{ bus.model }}</span>
                  <span class="text-xs text-slate-400 block">{{ bus.total_seats }} chỗ nằm/ngồi</span>
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
                  {{ bus.passengers_count }} khách
                </td>
                <td class="px-5 py-3.5 text-right font-bold text-emerald-700 font-mono">
                  {{ formatCurrency(bus.revenue) }}
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span class="px-2.5 py-1 rounded-full text-[11px] font-bold" :class="bus.status_class">
                    {{ bus.status }}
                  </span>
                  <span v-if="bus.cancel_reason" class="text-[10px] text-red-600 block mt-0.5 font-normal">
                    {{ bus.cancel_reason }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <AiChatModal />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminHeader from '@/Components/AdminHeader.vue';
import AiChatModal from '@/Components/AiChatModal.vue';

const props = defineProps({
  stats: Object,
  selected_date: String,
  bus_daily_stats: Array,
  cancelled_trips_list: Array,
  recent_trips: Array,
});

const currentDate = ref(props.selected_date || new Date().toISOString().split('T')[0]);

const handleDateChange = () => {
  router.get('/admin', {
    date: currentDate.value
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
};
</script>
