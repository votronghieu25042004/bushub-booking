<template>
  <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-slate-800 font-sans">
    <Navbar />

    <!-- TOP SEARCH BAR: NẰM Ở TRANG TÌM CHUYẾN XE (ĐỒNG BỘ 100% VỚI TRANG CHỦ) -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 text-white py-6 shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
          <h1 class="text-xl sm:text-2xl font-black flex items-center gap-2">
            <span>🔍</span>
            <span>Tìm Chuyến Xe: {{ originInput }} ⇄ {{ destinationInput || 'Tất cả điểm đến' }}</span>
          </h1>
          <span class="text-xs bg-white/20 px-3 py-1 rounded-full font-semibold">
            Cập nhật hàng ngày • 10 Xe sẵn sàng
          </span>
        </div>

        <div class="bg-white rounded-2xl p-4 text-slate-800 shadow-xl">
          <form @submit.prevent="executeSearch" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- 1. Điểm Đi (Col 3.5) -->
            <div class="md:col-span-3">
              <label class="block text-[11px] font-bold text-slate-500 uppercase">📍 Điểm Đón Khách</label>
              <input
                type="text"
                v-model="originInput"
                list="trip-origins"
                placeholder="Nhập điểm đi..."
                class="w-full mt-1 px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none"
              />
              <datalist id="trip-origins">
                <option value="Đà Nẵng">Bến xe TT Đà Nẵng</option>
                <option value="Thừa Thiên - Huế">Huế (BX Phía Nam)</option>
                <option value="Quảng Trị">Quảng Trị (BX Đông Hà)</option>
                <option value="Quảng Bình">Quảng Bình (BX Đồng Hới)</option>
                <option value="Quy Nhơn">Bình Định (BX Quy Nhơn)</option>
                <option value="Buôn Ma Thuột">Đắk Lắk (BX Buôn Ma Thuột)</option>
              </datalist>
            </div>

            <!-- Nút Đổi Chiều (Col 1) -->
            <div class="md:col-span-1 flex items-center justify-center pt-3 sm:pt-4">
              <button
                type="button"
                @click="swapLocations"
                title="Đổi chiều đón - trả khách"
                class="w-9 h-9 rounded-full border border-slate-300 bg-white hover:bg-indigo-50 hover:border-indigo-400 text-indigo-600 flex items-center justify-center shadow-xs active:scale-90 transition cursor-pointer font-bold text-base"
              >
                ⇄
              </button>
            </div>

            <!-- 2. Điểm Đến (Col 3.5) -->
            <div class="md:col-span-3">
              <label class="block text-[11px] font-bold text-slate-500 uppercase">🏁 Điểm Trả Khách</label>
              <input
                type="text"
                v-model="destinationInput"
                list="trip-destinations"
                placeholder="Nhập điểm đến..."
                class="w-full mt-1 px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none"
              />
              <datalist id="trip-destinations">
                <option value="Thừa Thiên - Huế">Huế (BX Phía Nam)</option>
                <option value="Quảng Trị">Quảng Trị (BX Đông Hà)</option>
                <option value="Quảng Bình">Quảng Bình (BX Đồng Hới)</option>
                <option value="Quy Nhơn">Bình Định (BX Quy Nhơn)</option>
                <option value="Buôn Ma Thuột">Đắk Lắk (BX Buôn Ma Thuột)</option>
                <option value="Đà Nẵng">Bến xe TT Đà Nẵng</option>
              </datalist>
            </div>

            <!-- 3. Ngày Khởi Hành (Col 2.5) -->
            <div class="md:col-span-2.5">
              <label class="block text-[11px] font-bold text-slate-500 uppercase">📅 Ngày Khởi Hành</label>
              <input
                type="date"
                v-model="dateInput"
                class="w-full mt-1 px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none"
              />
            </div>

            <!-- 4. Nút Tìm Kiếm (Col 2) -->
            <div class="md:col-span-2 flex items-end pt-3 sm:pt-4">
              <button
                type="submit"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-black text-xs sm:text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer"
              >
                <span>🔍 Tìm Kiếm</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
      <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6 flex-wrap gap-3">
        <div>
          <h2 class="text-lg sm:text-xl font-black text-slate-900">
            Danh Sách Chuyến Xe Khởi Hành
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Tìm thấy <span class="font-bold text-indigo-600">{{ tripList.length }}</span> chuyến xe phù hợp cho tuyến <b>{{ originInput }} → {{ destinationInput || 'Các tỉnh' }}</b>
          </p>
        </div>

        <!-- Bộ Lọc Sắp Xếp Nhanh -->
        <div class="flex items-center gap-2 text-xs">
          <span class="text-slate-500 hidden sm:inline">Sắp xếp theo:</span>
          <select
            v-model="sortBy"
            class="bg-white border border-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-xl outline-none cursor-pointer"
          >
            <option value="time_asc">Giờ khởi hành sớm nhất</option>
            <option value="price_asc">Giá vé thấp nhất</option>
            <option value="price_desc">Giá vé cao nhất</option>
          </select>
        </div>
      </div>

      <!-- Khi Không Có Chuyến -->
      <div v-if="sortedTrips.length === 0" class="text-center py-16 bg-white rounded-3xl border border-slate-200 shadow-xs">
        <div class="text-4xl mb-2">🚌</div>
        <h3 class="font-bold text-slate-800 text-base">Không tìm thấy chuyến xe nào theo điều kiện tìm kiếm</h3>
        <p class="text-xs text-slate-500 mt-1">Vui lòng chọn ngày khác hoặc đổi tuyến đường.</p>
        <button
          @click="resetFilters"
          class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition cursor-pointer"
        >
          Xem tất cả chuyến xe hôm nay
        </button>
      </div>

      <!-- Danh Sách Thẻ Chuyến Xe Với Báo Hiệu Đang Chạy Nổi Bật -->
      <div v-else class="space-y-4">
        <div
          v-for="trip in sortedTrips"
          :key="trip.id"
          class="bg-white rounded-2xl border transition-all p-5 sm:p-6 relative overflow-hidden"
          :class="[
            trip.status === 'RUNNING' ? 'border-2 border-amber-400 bg-amber-50/15 shadow-md shadow-amber-500/10' : 'border-slate-200/90 shadow-xs hover:shadow-md',
            trip.status === 'CANCELLED' ? 'opacity-65 bg-slate-50 border-red-200' : ''
          ]"
        >
          <!-- Dải nhãn góc báo hiệu nếu đang chạy -->
          <div v-if="trip.status === 'RUNNING'" class="absolute top-0 right-0 bg-gradient-to-l from-amber-500 to-orange-500 text-white text-[10px] font-black uppercase tracking-wider px-4 py-1 rounded-bl-xl shadow-xs flex items-center gap-1.5 animate-pulse">
            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
            <span>⚡ XE ĐANG CHẠY TRÊN ĐƯỜNG</span>
          </div>

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
              <div
                class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-xl shrink-0"
                :class="trip.status === 'RUNNING' ? 'bg-amber-100 text-amber-700 border border-amber-300' : 'bg-indigo-50 text-indigo-600 border border-indigo-200'"
              >
                🚌
              </div>
              <div>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="font-black text-slate-900 text-base sm:text-lg">
                    {{ trip.route?.name || 'Tuyến xe liên tỉnh' }}
                  </span>

                  <!-- BADGE TRẠNG THÁI NỔI BẬT: ĐANG CHẠY / SẮP CHẠY / HOÀN THÀNH / ĐÃ HỦY -->
                  <span
                    v-if="trip.status === 'RUNNING'"
                    class="px-2.5 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-xs flex items-center gap-1.5 animate-pulse"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    <span>ĐANG CHẠY</span>
                  </span>
                  <span
                    v-else-if="trip.status === 'SCHEDULED'"
                    class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200"
                  >
                    ⏳ Sắp khởi hành
                  </span>
                  <span
                    v-else-if="trip.status === 'COMPLETED'"
                    class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200"
                  >
                    ✓ Đã hoàn thành
                  </span>
                  <span
                    v-else-if="trip.status === 'CANCELLED'"
                    class="px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-700 border border-red-200"
                  >
                    ⚠️ ĐÃ HỦY (Bảo trì phanh)
                  </span>

                  <!-- Loại Xe -->
                  <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ trip.bus?.bus_type || trip.bus?.model || 'VIP Giường Nằm' }}
                  </span>
                </div>

                <div class="text-xs text-slate-500 mt-1">
                  Biển số xe: <span class="font-mono font-bold text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ trip.bus?.license_plate || trip.bus?.plate_number || '43B-012.34' }}</span>
                  <span class="mx-1.5">•</span>
                  Bác tài: <span class="font-semibold text-slate-800">{{ trip.driver?.name || 'Bác tài BusHub' }}</span> ({{ trip.driver?.phone || '0905.123.456' }})
                </div>
              </div>
            </div>

            <!-- Giá vé & Chỗ trống -->
            <div class="text-left sm:text-right">
              <div class="text-xl font-black" :class="trip.status === 'RUNNING' ? 'text-orange-600' : 'text-indigo-600'">
                {{ formatPrice(trip.price || trip.base_price || 140000) }}
              </div>
              <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">
                Cọc linh hoạt 30% chỉ {{ formatPrice((trip.price || trip.base_price || 140000) * 0.3) }}
              </div>
            </div>
          </div>

          <!-- Chi tiết giờ chạy & Nút Đặt Vé -->
          <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1">
              <div>
                <div class="text-lg font-black text-slate-900">
                  {{ formatTime(trip.departure_time) }}
                </div>
                <div class="text-xs text-slate-500 font-semibold">{{ trip.route?.origin || 'Điểm đón' }}</div>
              </div>

              <div class="flex-1 max-w-xs flex flex-col items-center">
                <span
                  v-if="trip.status === 'RUNNING'"
                  class="text-[11px] font-bold text-orange-700 bg-orange-100 px-2.5 py-0.5 rounded-full flex items-center gap-1 animate-pulse"
                >
                  ⚡ Đang di chuyển trên tuyến
                </span>
                <span v-else-if="trip.status === 'COMPLETED'" class="text-[10px] text-emerald-600 font-semibold">
                  ✓ Đã về bến an toàn
                </span>
                <span v-else class="text-[10px] text-slate-400 font-medium">
                  Chạy trực tiếp ({{ trip.route?.estimated_hours || '3.0' }}h)
                </span>

                <div class="w-full flex items-center mt-1">
                  <div class="w-2.5 h-2.5 rounded-full" :class="trip.status === 'RUNNING' ? 'bg-orange-500' : 'bg-indigo-600'"></div>
                  <div
                    class="flex-1 h-0.5 border-t border-dashed"
                    :class="trip.status === 'RUNNING' ? 'border-orange-400 bg-orange-200' : 'border-indigo-400 bg-indigo-200'"
                  ></div>
                  <div class="w-2.5 h-2.5 rounded-full bg-emerald-600"></div>
                </div>
              </div>

              <div>
                <div class="text-lg font-black text-slate-900">
                  {{ formatTime(trip.arrival_time || trip.departure_time) }}
                </div>
                <div class="text-xs text-slate-500 font-semibold">{{ trip.route?.destination || 'Điểm đến' }}</div>
              </div>
            </div>

            <div>
              <!-- Xe đang chạy: Nút màu cam rực rỡ nổi bật -->
              <Link
                v-if="trip.status === 'RUNNING'"
                :href="`/trips/${trip.id}`"
                class="inline-block w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-amber-500 via-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md shadow-orange-500/20 text-center transition-all cursor-pointer animate-pulse"
              >
                ⚡ Xe Đang Chạy • Chọn Ghế
              </Link>

              <!-- Xe sắp chạy: Nút xanh tiêu chuẩn -->
              <Link
                v-else-if="trip.status === 'SCHEDULED'"
                :href="`/trips/${trip.id}`"
                class="inline-block w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-indigo-500/20 text-center transition-all cursor-pointer"
              >
                Chọn Ghế & Đặt Vé
              </Link>

              <!-- Xe đã hoàn thành -->
              <span
                v-else-if="trip.status === 'COMPLETED'"
                class="inline-block px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl border border-slate-200"
              >
                ✓ Đã hoàn tất chuyến
              </span>

              <!-- Xe bị hủy -->
              <span
                v-else
                class="inline-block px-4 py-2 bg-red-100 text-red-700 text-xs font-bold rounded-xl border border-red-200"
              >
                Đã xếp xe dự phòng
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
    <AiChatModal />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import AiChatModal from '@/Components/AiChatModal.vue';

const props = defineProps({
  trips: [Array, Object],
  routes: Array,
  filters: Object,
});

const todayStr = new Date().toISOString().split('T')[0];
const originInput = ref(props.filters?.origin || 'Đà Nẵng');
const destinationInput = ref(props.filters?.destination || 'Quảng Trị');
const dateInput = ref(props.filters?.date || todayStr);
const sortBy = ref('time_asc');

const swapLocations = () => {
  const temp = originInput.value;
  originInput.value = destinationInput.value;
  destinationInput.value = temp;
  executeSearch();
};

const tripList = computed(() => {
  if (Array.isArray(props.trips)) return props.trips;
  if (props.trips && Array.isArray(props.trips.data)) return props.trips.data;
  return [];
});

const sortedTrips = computed(() => {
  let list = [...tripList.value];
  if (sortBy.value === 'price_asc') {
    list.sort((a, b) => (a.price || a.base_price || 0) - (b.price || b.base_price || 0));
  } else if (sortBy.value === 'price_desc') {
    list.sort((a, b) => (b.price || b.base_price || 0) - (a.price || a.base_price || 0));
  } else {
    list.sort((a, b) => new Date(a.departure_time) - new Date(b.departure_time));
  }
  return list;
});

const executeSearch = () => {
  router.get('/trips', {
    origin: originInput.value,
    destination: destinationInput.value,
    date: dateInput.value,
  }, { preserveState: true });
};

const resetFilters = () => {
  originInput.value = 'Đà Nẵng';
  destinationInput.value = '';
  dateInput.value = todayStr;
  router.get('/trips');
};

const formatPrice = (val) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
};

const formatTime = (timeStr) => {
  if (!timeStr) return '08:00';
  if (timeStr.includes('T') || timeStr.includes(' ')) {
    const d = new Date(timeStr);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
  return timeStr.substring(0, 5);
};
</script>