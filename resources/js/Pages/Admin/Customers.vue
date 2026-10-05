<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <AdminHeader title="Lưu Trữ & Quản Lý Khách Hàng Thân Thiết (CRM)" subtitle="Theo dõi tần suất đặt vé, số lần đi, tổng tiền chi tiêu và vị trí ghế ngồi của từng khách hàng" />

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      <!-- Header Trang -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
          <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
            <span>👥</span>
            <span>Lưu Trữ Khách Hàng Thân Thiết & Tần Suất Đi Xe</span>
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Lưu vết toàn bộ hành khách: đã đi mấy lần, tổng chi tiêu bao nhiêu tiền, từng đi những xe nào và ngồi ghế số mấy
          </p>
        </div>

        <!-- Ô Tìm Kiếm Khách -->
        <div class="relative max-w-xs w-full">
          <input
            type="text"
            v-model="searchQuery"
            @input="handleSearch"
            placeholder="Tìm theo tên, SĐT khách..."
            class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-4 py-2 text-xs font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none shadow-2xs"
          />
          <span class="absolute left-3 top-2.5 text-slate-400">🔍</span>
        </div>
      </div>

      <!-- 1. Thống Kê Nhanh Khách Hàng -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase">Tổng Khách Hàng Lưu Trữ</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ stats.total_customers }} <span class="text-sm font-normal text-slate-500">người</span></p>
            <p class="text-xs text-indigo-600 font-medium mt-0.5">✓ Đã đồng bộ hồ sơ</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-black">
            👥
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-purple-200 shadow-xs flex items-center justify-between bg-purple-50/20">
          <div>
            <p class="text-xs font-bold text-purple-700 uppercase">Khách VIP Thường Xuyên</p>
            <p class="text-3xl font-black text-purple-700 mt-1">{{ stats.total_vip }} <span class="text-sm font-normal text-purple-500">khách VIP</span></p>
            <p class="text-xs text-purple-600 font-medium mt-0.5">Đi từ 3 đến 6 lần trở lên</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-2xl">
            💎
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-xs flex items-center justify-between bg-emerald-50/20">
          <div>
            <p class="text-xs font-bold text-emerald-700 uppercase">Tổng Chi Tiêu Toàn Bến</p>
            <p class="text-2xl font-black text-emerald-700 mt-1 font-mono">{{ formatCurrency(stats.total_spend) }}</p>
            <p class="text-xs text-emerald-800 font-medium mt-0.5">Tích lũy trọn đời</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl">
            💰
          </div>
        </div>
      </div>

      <!-- 2. Bảng Danh Sách Khách Hàng Thân Thiết -->
      <div class="mt-8 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
          <h2 class="font-bold text-base text-slate-900">Danh Sách Khách Hàng (Sắp xếp theo tổng tiền chi tiêu)</h2>
          <span class="text-xs text-slate-500">Bấm "Xem Lịch Sử Đi & Ghế" để kiểm tra các lần đi và số ghế</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-100/80 text-slate-600 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
              <tr>
                <th class="px-5 py-3">Họ Tên Khách Hàng</th>
                <th class="px-5 py-3">Số Điện Thoại</th>
                <th class="px-5 py-3">Email</th>
                <th class="px-5 py-3 text-center">Số Lần Đi Xe</th>
                <th class="px-5 py-3 text-right">Tổng Chi Tiêu</th>
                <th class="px-5 py-3 text-center">Hạng Khách Hàng</th>
                <th class="px-5 py-3 text-center">Lịch Sử & Số Ghế</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="(cust, idx) in customers"
                :key="cust.phone"
                class="hover:bg-slate-50/80 transition"
              >
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                      {{ cust.name.charAt(0) }}
                    </div>
                    <div>
                      <span class="font-bold text-slate-900 block">{{ cust.name }}</span>
                      <span class="text-[10px] text-slate-400">Khách ID #{{ 1000 + idx }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3.5 font-mono font-bold text-slate-700">
                  {{ cust.phone }}
                </td>
                <td class="px-5 py-3.5 text-slate-500 text-xs">
                  {{ cust.email }}
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span class="px-2.5 py-1 rounded-full font-black text-xs bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ cust.total_trips }} lần đi
                  </span>
                </td>
                <td class="px-5 py-3.5 text-right font-black text-emerald-700 font-mono text-sm">
                  {{ formatCurrency(cust.total_spent) }}
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold border" :class="cust.rank_badge">
                    {{ cust.rank }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <button
                    @click="openHistoryModal(cust)"
                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1 mx-auto"
                  >
                    <span>📜</span>
                    <span>Xem các lần đi ({{ cust.total_trips }})</span>
                  </button>
                </td>
              </tr>
              <tr v-if="customers.length === 0">
                <td colspan="7" class="text-center py-8 text-slate-400">
                  Không tìm thấy khách hàng nào phù hợp với từ khóa tìm kiếm
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- MODAL: XEM LỊCH SỬ CÁC LẦN ĐI XE VÀ VỊ TRÍ GHẾ NGỒI CỦA KHÁCH -->
    <div
      v-if="selectedCustomerModal"
      class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden border border-slate-200">
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold">
              👤
            </div>
            <div>
              <h3 class="font-black text-slate-900 text-base">
                Lịch Sử Đi Xe & Vị Trí Ghế: {{ selectedCustomerModal.name }}
              </h3>
              <p class="text-xs text-slate-500">
                SĐT: <b class="font-mono">{{ selectedCustomerModal.phone }}</b> • Tổng chi tiêu: <b class="text-emerald-700 font-mono">{{ formatCurrency(selectedCustomerModal.total_spent) }}</b> ({{ selectedCustomerModal.total_trips }} chuyến)
              </p>
            </div>
          </div>
          <button
            @click="selectedCustomerModal = null"
            class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-700 flex items-center justify-center font-bold cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Body: Bảng Chi Tiết Từng Chuyến -->
        <div class="p-6 overflow-y-auto space-y-4">
          <div
            v-for="(h, idx) in selectedCustomerModal.history"
            :key="idx"
            class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-slate-50 transition space-y-2 text-xs"
          >
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div class="flex items-center gap-2">
                <span class="font-mono font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-[11px]">{{ h.booking_code }}</span>
                <span class="font-black text-slate-900 text-sm">{{ h.route_name }}</span>
              </div>
              <span class="text-emerald-700 font-mono font-black text-sm">{{ formatCurrency(h.amount) }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 border-t border-slate-200 text-slate-600">
              <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Thời Gian Khởi Hành</span>
                <span class="font-bold text-slate-800">{{ h.departure_time }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Phương Tiện & Bác Tài</span>
                <span class="font-bold text-slate-800 font-mono">{{ h.bus_plate }}</span> ({{ h.driver_name }})
              </div>
              <div>
                <span class="text-indigo-600 block text-[10px] uppercase font-bold">VỊ TRÍ GHẾ NGỒI</span>
                <span class="px-2 py-0.5 rounded bg-indigo-600 text-white font-black text-xs inline-block">
                  💺 {{ h.seats }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Modal -->
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50 flex justify-end">
          <button
            @click="selectedCustomerModal = null"
            class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold cursor-pointer transition"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>

    <AiChatModal />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminHeader from '@/Components/AdminHeader.vue';
import AiChatModal from '@/Components/AiChatModal.vue';

const props = defineProps({
  customers: Array,
  stats: Object,
  search_query: String,
});

const searchQuery = ref(props.search_query || '');
const selectedCustomerModal = ref(null);

const handleSearch = () => {
  router.get('/admin/customers', {
    search: searchQuery.value
  }, {
    preserveState: true,
    replace: true,
  });
};

const openHistoryModal = (cust) => {
  selectedCustomerModal.value = cust;
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
};
</script>