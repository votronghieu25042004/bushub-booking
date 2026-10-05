<template>
  <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-5 sm:p-7 text-slate-800 transition-all max-w-5xl mx-auto">
    <!-- Hàng trên: Radio chọn Một chiều / Khứ hồi & Link Hướng dẫn mua vé -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5 pb-3 border-b border-slate-100">
      <div class="flex items-center gap-6">
        <!-- Một chiều -->
        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs sm:text-sm select-none">
          <input
            type="radio"
            name="trip_type"
            value="one_way"
            v-model="tripType"
            class="w-4 h-4 text-orange-600 focus:ring-orange-500 accent-orange-600"
          />
          <span :class="tripType === 'one_way' ? 'text-orange-600 font-black' : 'text-slate-600'">
            Một chiều
          </span>
        </label>

        <!-- Khứ hồi -->
        <label class="flex items-center gap-2 cursor-pointer font-bold text-xs sm:text-sm select-none">
          <input
            type="radio"
            name="trip_type"
            value="round_trip"
            v-model="tripType"
            class="w-4 h-4 text-orange-600 focus:ring-orange-500 accent-orange-600"
          />
          <span :class="tripType === 'round_trip' ? 'text-orange-600 font-black' : 'text-slate-600'">
            Khứ hồi
          </span>
        </label>
      </div>

      <!-- Hướng dẫn mua vé -->
      <button
        type="button"
        @click="showGuide = !showGuide"
        class="text-xs font-semibold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1 cursor-pointer"
      >
        <span>📖</span>
        <span>Hướng dẫn mua vé</span>
      </button>
    </div>

    <!-- Hộp modal/alert nhỏ hướng dẫn mua vé nếu bấm -->
    <div v-if="showGuide" class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-xl text-xs text-orange-900 flex items-start justify-between">
      <div class="space-y-1">
        <p class="font-bold">💡 Hướng dẫn đặt vé nhanh:</p>
        <p>1. Chọn <b>Điểm đi</b> và <b>Điểm đến</b> (bấm nút ⇄ để đổi chiều đón trả khách khi xe chạy từ Huế/Quảng Trị về lại Đà Nẵng).</p>
        <p>2. Chọn ngày khởi hành và số lượng vé mong muốn.</p>
        <p>3. Bấm <b>Tìm chuyến xe</b> để xem sơ đồ chỗ, đặt cọc 30% hoặc thanh toán nhận mã QR lên xe.</p>
      </div>
      <button @click="showGuide = false" class="text-orange-600 hover:text-orange-900 font-bold ml-2">✕</button>
    </div>

    <!-- Khung Input Tìm Kiếm (Điểm đi - ⇄ - Điểm đến - Ngày đi - [Ngày về] - Số vé) -->
    <form @submit.prevent="handleSearch">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
        <!-- 1. ĐIỂM ĐI (Col 3.5) -->
        <div class="md:col-span-3 space-y-1">
          <label class="text-xs font-bold text-slate-700">Điểm đi</label>
          <div class="relative">
            <input
              type="text"
              v-model="origin"
              list="popular-origins"
              placeholder="Nhập điểm đi..."
              class="w-full bg-white border border-slate-300 text-slate-900 font-bold text-sm rounded-xl px-3.5 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all shadow-2xs"
            />
            <datalist id="popular-origins">
              <option value="Đà Nẵng">Bến xe TT Đà Nẵng</option>
              <option value="Thừa Thiên - Huế">Huế (BX Phía Nam)</option>
              <option value="Quảng Trị">Quảng Trị (BX Đông Hà)</option>
              <option value="Quảng Bình">Quảng Bình (BX Đồng Hới)</option>
              <option value="Quy Nhơn">Bình Định (BX Quy Nhơn)</option>
              <option value="Buôn Ma Thuột">Đắk Lắk (BX Buôn Ma Thuột)</option>
            </datalist>
          </div>
        </div>

        <!-- Nút Đổi Chiều (Col 1) -->
        <div class="md:col-span-1 flex items-center justify-center pt-5">
          <button
            type="button"
            @click="swapLocations"
            title="Đổi chiều điểm đón - trả khách"
            class="w-10 h-10 rounded-full border border-slate-300 bg-white hover:bg-orange-50 hover:border-orange-400 text-orange-600 flex items-center justify-center shadow-xs active:scale-90 transition-all cursor-pointer font-bold text-lg"
          >
            ⇄
          </button>
        </div>

        <!-- 2. ĐIỂM ĐẾN (Col 3.5) -->
        <div class="md:col-span-3 space-y-1">
          <label class="text-xs font-bold text-slate-700">Điểm đến</label>
          <div class="relative">
            <input
              type="text"
              v-model="destination"
              list="popular-destinations"
              placeholder="Nhập điểm đến..."
              class="w-full bg-white border border-slate-300 text-slate-900 font-bold text-sm rounded-xl px-3.5 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all shadow-2xs"
            />
            <datalist id="popular-destinations">
              <option value="Thừa Thiên - Huế">Huế (BX Phía Nam, Phú Bài)</option>
              <option value="Quảng Trị">Quảng Trị (Đông Hà, Hải Lăng)</option>
              <option value="Quảng Bình">Quảng Bình (Đồng Hới, Lệ Thủy)</option>
              <option value="Quy Nhơn">Bình Định (Quy Nhơn, Quảng Ngãi)</option>
              <option value="Buôn Ma Thuột">Đắk Lắk (Buôn Ma Thuột, Pleiku)</option>
              <option value="Đà Nẵng">Bến xe TT Đà Nẵng</option>
            </datalist>
          </div>
        </div>

        <!-- 3. NGÀY ĐI (Col 2.5) -->
        <div :class="tripType === 'round_trip' ? 'md:col-span-2' : 'md:col-span-3'" class="space-y-1">
          <label class="text-xs font-bold text-slate-700 flex justify-between">
            <span>Ngày đi</span>
            <span class="text-orange-600 font-semibold">{{ getDayOfWeek(selectedDate) }}</span>
          </label>
          <input
            type="date"
            v-model="selectedDate"
            :min="todayStr"
            class="w-full bg-white border border-slate-300 text-slate-900 font-semibold text-sm rounded-xl px-3 py-2.5 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all shadow-2xs"
          />
        </div>

        <!-- 4. NGÀY VỀ (NẾU KHỨ HỒI) (Col 2) -->
        <div v-if="tripType === 'round_trip'" class="md:col-span-2 space-y-1 animate-in fade-in duration-200">
          <label class="text-xs font-bold text-slate-700 flex justify-between">
            <span>Ngày về</span>
            <span class="text-orange-600 font-semibold">{{ getDayOfWeek(selectedReturnDate) }}</span>
          </label>
          <input
            type="date"
            v-model="selectedReturnDate"
            :min="selectedDate || todayStr"
            class="w-full bg-white border border-slate-300 text-slate-900 font-semibold text-sm rounded-xl px-3 py-2.5 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all shadow-2xs"
          />
        </div>

        <!-- 5. SỐ VÉ (Col 1.5) -->
        <div :class="tripType === 'round_trip' ? 'md:col-span-1' : 'md:col-span-1.5'" class="space-y-1">
          <label class="text-xs font-bold text-slate-700">Số vé</label>
          <select
            v-model="ticketCount"
            class="w-full bg-white border border-slate-300 text-slate-900 font-bold text-sm rounded-xl px-2.5 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all shadow-2xs"
          >
            <option :value="1">1</option>
            <option :value="2">2</option>
            <option :value="3">3</option>
            <option :value="4">4</option>
            <option :value="5">5</option>
          </select>
        </div>
      </div>

      <!-- Khối: TÌM KIẾM GẦN ĐÂY (Ảnh 3 của User) -->
      <div class="mt-5 pt-4 border-t border-slate-100">
        <p class="text-xs font-bold text-slate-700 mb-2.5 flex items-center gap-1.5">
          <span>🕒</span>
          <span>Tìm kiếm gần đây</span>
        </p>

        <div class="flex flex-wrap items-center gap-2.5">
          <button
            v-for="(rec, idx) in recentSearches"
            :key="idx"
            type="button"
            @click="applyRecentSearch(rec)"
            class="group text-left px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-orange-50 border border-slate-200 hover:border-orange-300 transition-all shadow-2xs cursor-pointer"
          >
            <div class="font-bold text-xs text-slate-800 group-hover:text-orange-700 flex items-center gap-1">
              <span>{{ rec.origin }}</span>
              <span class="text-slate-400 group-hover:text-orange-400">→</span>
              <span>{{ rec.destination }}</span>
            </div>
            <div class="text-[10px] text-slate-400 group-hover:text-orange-600 mt-0.5 font-medium">
              {{ rec.formattedDate }}
            </div>
          </button>
        </div>
      </div>

      <!-- Nút To "Tìm Chuyến Xe" Đặt Ở Giữa Bên Dưới (Màu Cam Rực Rỡ FUTA Chuẩn Mẫu) -->
      <div class="mt-6 flex justify-center">
        <button
          type="submit"
          class="w-full sm:w-auto min-w-[280px] bg-gradient-to-r from-[#EF5222] to-[#E03A00] hover:from-[#E03A00] hover:to-[#C83400] text-white font-black text-base py-3.5 px-10 rounded-2xl shadow-lg shadow-orange-500/30 active:scale-98 transition-all flex items-center justify-center gap-2 cursor-pointer"
        >
          <span>Tìm chuyến xe</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  destinations: Array,
});

const today = new Date();
const todayStr = today.toISOString().split('T')[0];

const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
const tomorrowStr = tomorrow.toISOString().split('T')[0];

const tripType = ref('one_way');
const showGuide = ref(false);
const origin = ref('Đà Nẵng');
const destination = ref('Quảng Trị');
const selectedDate = ref(todayStr);
const selectedReturnDate = ref(tomorrowStr);
const ticketCount = ref(1);

// Danh sách tìm kiếm gần đây mẫu và được lưu trữ
const recentSearches = ref([
  { origin: 'Đà Nẵng', destination: 'Thừa Thiên - Huế', date: '2026-09-21', formattedDate: '21/09/2026' },
  { origin: 'Thừa Thiên - Huế', destination: 'Đà Nẵng', date: todayStr, formattedDate: 'Hôm nay' },
  { origin: 'Đà Nẵng', destination: 'Quảng Trị', date: todayStr, formattedDate: '05/10/2026' },
  { origin: 'Đà Nẵng', destination: 'Quy Nhơn', date: tomorrowStr, formattedDate: 'Ngày mai' },
]);

onMounted(() => {
  try {
    const saved = localStorage.getItem('bushub_recent_searches');
    if (saved) {
      const parsed = JSON.parse(saved);
      if (Array.isArray(parsed) && parsed.length > 0) {
        recentSearches.value = parsed;
      }
    }
  } catch (e) {}
});

const swapLocations = () => {
  const temp = origin.value;
  origin.value = destination.value;
  destination.value = temp;
};

const getDayOfWeek = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
  return days[d.getDay()] || '';
};

const applyRecentSearch = (rec) => {
  origin.value = rec.origin;
  destination.value = rec.destination;
  if (rec.date) {
    selectedDate.value = rec.date;
  }
};

const handleSearch = () => {
  // Lưu vào recent searches
  const d = new Date(selectedDate.value);
  const formatted = `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
  
  const newRecent = [
    { origin: origin.value, destination: destination.value, date: selectedDate.value, formattedDate: formatted },
    ...recentSearches.value.filter(item => !(item.origin === origin.value && item.destination === destination.value))
  ].slice(0, 5);
  
  recentSearches.value = newRecent;
  try {
    localStorage.setItem('bushub_recent_searches', JSON.stringify(newRecent));
  } catch (e) {}

  router.get('/trips', {
    trip_type: tripType.value,
    origin: origin.value,
    destination: destination.value,
    date: selectedDate.value,
    return_date: tripType.value === 'round_trip' ? selectedReturnDate.value : undefined,
    seats: ticketCount.value
  });
};
</script>
