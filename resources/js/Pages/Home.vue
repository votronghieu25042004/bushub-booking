<template>
  <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-slate-800 font-sans">
    <Navbar />

    <!-- ==================== HERO SECTION & SEARCH ==================== -->
    <section class="relative bg-gradient-to-b from-blue-700 via-blue-600 to-[#2563EB] text-white pt-10 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
      <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>

      <div class="max-w-5xl mx-auto relative z-10 text-center space-y-4 mb-8">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold uppercase tracking-wider text-white border border-white/20">
          <span>🚍</span> MẠNG LƯỚI XE KHÁCH CÔNG NGHỆ • ĐẶT VÉ NHANH CHÓNG
        </div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
          BUSHUB - CỔNG THÔNG TIN & QUẢN LÝ BẾN XE LIÊN TỈNH
        </h1>
        <p class="text-white/90 text-sm sm:text-base max-w-2xl mx-auto font-medium">
          Hệ thống đặt vé xe trực tuyến thông minh. Chọn chỗ ngồi 2 tầng, chọn trạm đón tận nơi, cọc linh hoạt 30% và nhận mã QR soát vé camera tức thì.
        </p>
      </div>

      <!-- Search Widget Embedded (Linh hoạt điểm đón/trả, đổi chiều, tìm kiếm gần đây) -->
      <div class="relative z-20 -mb-16">
        <SearchWidget :destinations="destinations_today" />
      </div>
    </section>

    <!-- Spacer for Search Widget overlap -->
    <div class="h-20 sm:h-24"></div>

    <!-- ==================== TUYẾN ĐƯỜNG PHỔ BIẾN (SLIDER PHONG CẢNH) ==================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between gap-4 mb-5">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
              Tuyến Đường Phổ Biến Xuất Bến Từ Đà Nẵng
            </h2>
          </div>
          <p class="text-xs text-slate-500 font-medium mt-0.5">
            Được khách hàng tin chọn nhiều nhất với giá vé ưu đãi và dàn xe VIP hiện đại
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="scrollRoutes('left')"
            class="w-9 h-9 rounded-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 flex items-center justify-center shadow-xs transition-all active:scale-95 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button
            @click="scrollRoutes('right')"
            class="w-9 h-9 rounded-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 flex items-center justify-center shadow-xs transition-all active:scale-95 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <!-- CAROUSEL THẺ PHONG CẢNH -->
      <div
        ref="routesContainer"
        class="flex gap-5 overflow-x-auto no-scrollbar scroll-smooth pb-3"
      >
        <div
          v-for="(item, idx) in popularRoutesList"
          :key="idx"
          @click="selectRoute(item)"
          class="flex-shrink-0 w-72 sm:w-80 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group flex flex-col justify-between"
        >
          <!-- Nửa Trên: Hình ảnh -->
          <div class="h-44 relative overflow-hidden bg-slate-200">
            <img
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
            
            <div class="absolute top-3 left-3 bg-black/40 backdrop-blur-md text-white text-[11px] font-bold px-2.5 py-1 rounded-full border border-white/20">
              {{ item.tripsCount }} chuyến/ngày
            </div>
            <div class="absolute top-3 right-3 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">
              {{ item.distance }}
            </div>

            <div class="absolute bottom-3 left-4 right-4 text-white">
              <p class="text-xs font-medium text-slate-200">{{ item.duration }} di chuyển</p>
              <h3 class="text-base font-bold truncate">{{ item.title }}</h3>
            </div>
          </div>

          <!-- Nửa Dưới: Thông tin giá & nút Đặt -->
          <div
            class="p-4 text-white flex items-center justify-between"
            :style="{ backgroundColor: item.bgColor }"
          >
            <div>
              <p class="text-[11px] opacity-80 uppercase tracking-wider font-semibold">Giá vé từ</p>
              <div class="flex items-baseline gap-1.5">
                <span class="text-lg font-black">{{ item.price }}</span>
                <span class="text-xs opacity-60 line-through">{{ item.originalPrice }}</span>
              </div>
            </div>
            <span class="bg-white/20 group-hover:bg-white text-white group-hover:text-slate-900 text-xs font-bold px-3 py-1.5 rounded-xl transition-all">
              Đặt vé →
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== ƯU ĐÃI KHÁCH HÀNG (4 VOUCHER PASTEL) ==================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
          Ưu Đãi
        </h2>
        <Link href="/trips" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
          Xem tất cả →
        </Link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Voucher 1: Vàng pastel -->
        <div class="bg-[#FFF8E7] rounded-2xl p-4 border border-amber-200/70 shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
          <div>
            <span class="text-[10px] font-black text-amber-800 bg-white px-2 py-0.5 rounded shadow-2xs">BUS</span>
            <h3 class="font-bold text-slate-900 text-sm mt-2 leading-snug">
              Tiết kiệm tới 30% khi đặt vé xe lần đầu
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Valid Till 31 Th3</p>
          </div>
          <div class="mt-4 flex items-center justify-between">
            <button class="bg-white border border-dashed border-amber-300 text-slate-800 text-xs font-bold px-3 py-1 rounded-lg flex items-center gap-1.5 shadow-2xs hover:bg-amber-50 transition-colors cursor-pointer">
              <span>🏷️</span> DAUTIEN
            </button>
            <span class="text-2xl">💰</span>
          </div>
        </div>

        <!-- Voucher 2: Tím nhạt pastel -->
        <div class="bg-[#F6F0FF] rounded-2xl p-4 border border-purple-200/70 shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
          <div>
            <span class="text-[10px] font-black text-purple-800 bg-white px-2 py-0.5 rounded shadow-2xs">BUS</span>
            <h3 class="font-bold text-slate-900 text-sm mt-2 leading-snug">
              Tiết kiệm 20% cho lần đặt vé tiếp theo
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Valid Till 31 Th3</p>
          </div>
          <div class="mt-4 flex items-center justify-between">
            <button class="bg-white border border-dashed border-purple-300 text-slate-800 text-xs font-bold px-3 py-1 rounded-lg flex items-center gap-1.5 shadow-2xs hover:bg-purple-50 transition-colors cursor-pointer">
              <span>🏷️</span> GIAMGIA
            </button>
            <span class="text-2xl">🎟️</span>
          </div>
        </div>

        <!-- Voucher 3: Xanh ngọc Grab -->
        <div class="bg-[#EBF7FF] rounded-2xl p-4 border border-blue-200/70 shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
          <div>
            <span class="text-[10px] font-black text-blue-800 bg-white px-2 py-0.5 rounded shadow-2xs">BUS</span>
            <h3 class="font-bold text-slate-900 text-sm mt-2 leading-snug">
              Giảm giá lên đến 50,000 VNĐ khi đặt xe tại Grab
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Valid Till 31 Th12</p>
          </div>
          <div class="mt-4 flex items-center justify-between">
            <button class="bg-white border border-dashed border-blue-300 text-slate-800 text-xs font-bold px-3 py-1 rounded-lg flex items-center gap-1.5 shadow-2xs hover:bg-blue-50 transition-colors cursor-pointer">
              <span>🏷️</span> REDBUS1
            </button>
            <span class="text-xs font-bold text-emerald-600 bg-white px-2 py-1 rounded">Grab</span>
          </div>
        </div>

        <!-- Voucher 4: Xanh mint GreenSM -->
        <div class="bg-[#F0FAF7] rounded-2xl p-4 border border-emerald-200/70 shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
          <div>
            <span class="text-[10px] font-black text-emerald-800 bg-white px-2 py-0.5 rounded shadow-2xs">BUS</span>
            <h3 class="font-bold text-slate-900 text-sm mt-2 leading-snug">
              Giảm giá lên đến 50,000 VNĐ khi đặt xe tại GreenSM
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">Valid Till 31 Th12</p>
          </div>
          <div class="mt-4 flex items-center justify-between">
            <button class="bg-white border border-dashed border-emerald-300 text-slate-800 text-xs font-bold px-3 py-1 rounded-lg flex items-center gap-1.5 shadow-2xs hover:bg-emerald-50 transition-colors cursor-pointer">
              <span>🏷️</span> REDBUS
            </button>
            <span class="text-xs font-bold text-emerald-700 bg-white px-2 py-1 rounded">GreenSM</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== CHÍNH SÁCH HỦY VÉ & ĐẢM BẢO QUYỀN LỢI (THAY THẾ CÓ GÌ MỚI) ==================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-8">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
            <span>🛡️</span>
            <span>Chính Sách Hủy Vé & Đảm Bảo Quyền Lợi Khách Hàng</span>
          </h2>
          <p class="text-xs text-slate-500 font-medium mt-0.5">
            Quy định minh bạch, cam kết đền bù và hỗ trợ đổi chuyến 24/7 trên toàn hệ thống BusHub
          </p>
        </div>
      </div>

      <!-- Lưới 4 Khung Thẻ Chính Sách Chuẩn Đẹp -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Khung 1: Đảm bảo vé & bồi thường 150% -->
        <div class="bg-gradient-to-br from-amber-50/80 to-amber-100/40 border border-amber-200 rounded-2xl p-5 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all">
          <div>
            <div class="w-10 h-10 rounded-xl bg-amber-200/80 text-amber-800 flex items-center justify-center text-xl mb-3">
              🛡️
            </div>
            <h4 class="font-black text-slate-900 text-sm">Chính Sách Đảm Bảo Vé 150%</h4>
            <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
              Hoàn tiền lên đến <b>150% giá vé</b> nếu xe bị hủy đột xuất do sự cố kỹ thuật hoặc trễ chuyến quá 30 phút mà không báo trước.
            </p>
          </div>
          <div class="mt-4 pt-3 border-t border-amber-200/60 flex items-center justify-between text-xs">
            <span class="text-amber-800 font-bold">Xếp xe dự phòng</span>
            <span class="text-amber-600 font-medium">Tự động 100%</span>
          </div>
        </div>

        <!-- Khung 2: Hủy vé & Đổi chuyến linh hoạt -->
        <div class="bg-gradient-to-br from-blue-50/80 to-blue-100/40 border border-blue-200 rounded-2xl p-5 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all">
          <div>
            <div class="w-10 h-10 rounded-xl bg-blue-200/80 text-blue-800 flex items-center justify-center text-xl mb-3">
              🔄
            </div>
            <h4 class="font-black text-slate-900 text-sm">Hủy & Đổi Vé Linh Hoạt</h4>
            <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
              Hủy trước <b>2 tiếng</b>: Hoàn 100% tiền vé.<br/>
              Hủy trước <b>1 tiếng</b>: Hoàn 80% tiền vé.<br/>
              Hỗ trợ đổi giờ chạy trực tuyến hoàn toàn miễn phí 01 lần.
            </p>
          </div>
          <div class="mt-4 pt-3 border-t border-blue-200/60 flex items-center justify-between text-xs">
            <span class="text-blue-800 font-bold">Hoàn tiền tự động</span>
            <span class="text-blue-600 font-medium">Trong 5 phút</span>
          </div>
        </div>

        <!-- Khung 3: Cọc 30% giữ chỗ -->
        <div class="bg-gradient-to-br from-emerald-50/80 to-emerald-100/40 border border-emerald-200 rounded-2xl p-5 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all">
          <div>
            <div class="w-10 h-10 rounded-xl bg-emerald-200/80 text-emerald-800 flex items-center justify-center text-xl mb-3">
              💳
            </div>
            <h4 class="font-black text-slate-900 text-sm">Đặt Cọc 30% Giữ Đúng Vị Trí Ghế</h4>
            <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
              Khách hàng chỉ cần thanh toán <b>30% tiền cọc</b> để khóa vị trí giường/ghế tầng 1 hoặc tầng 2. Phần 70% còn lại thanh toán khi lên xe qua mã QR.
            </p>
          </div>
          <div class="mt-4 pt-3 border-t border-emerald-200/60 flex items-center justify-between text-xs">
            <span class="text-emerald-800 font-bold">Giữ chỗ 100%</span>
            <span class="text-emerald-600 font-medium">Quét QR lên xe</span>
          </div>
        </div>

        <!-- Khung 4: Trợ lý AI & Gmail tự động -->
        <div class="bg-gradient-to-br from-purple-50/80 to-purple-100/40 border border-purple-200 rounded-2xl p-5 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all">
          <div>
            <div class="w-10 h-10 rounded-xl bg-purple-200/80 text-purple-800 flex items-center justify-center text-xl mb-3">
              🤖
            </div>
            <h4 class="font-black text-slate-900 text-sm">Trợ Lý AI & Thông Báo Gmail</h4>
            <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
              Hệ thống tự động quét và gửi <b>thông báo qua Gmail trước 45 phút</b> khi xe chuẩn bị xuất bến hoặc sắp đến trạm đón khách, đồng thời giải đáp thắc mắc 24/7.
            </p>
          </div>
          <div class="mt-4 pt-3 border-t border-purple-200/60 flex items-center justify-between text-xs">
            <span class="text-purple-800 font-bold">Gửi Gmail tự động</span>
            <span class="text-purple-600 font-medium">Trước 45 phút</span>
          </div>
        </div>
      </div>
    </section>

    <Footer />
    <AiChatModal />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import SearchWidget from '@/Components/SearchWidget.vue';
import AiChatModal from '@/Components/AiChatModal.vue';

const props = defineProps({
  routes: Array,
  destinations_today: Array,
  featured_trips: Array,
});

const routesContainer = ref(null);

const popularRoutesList = ref([
  {
    title: 'Đà Nẵng - Thừa Thiên Huế',
    dest: 'Huế',
    image: 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&w=600&q=80',
    bgColor: '#475569',
    price: '120.000đ',
    originalPrice: '160.000đ',
    tripsCount: 4,
    distance: '100 km',
    duration: '2.5 giờ'
  },
  {
    title: 'Đà Nẵng - Quảng Trị (Đông Hà)',
    dest: 'Quảng Trị',
    image: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80',
    bgColor: '#4C4368',
    price: '140.000đ',
    originalPrice: '190.000đ',
    tripsCount: 3,
    distance: '165 km',
    duration: '3.5 giờ'
  },
  {
    title: 'Đà Nẵng - Quy Nhơn (Bình Định)',
    dest: 'Quy Nhơn',
    image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
    bgColor: '#B6794D',
    price: '220.000đ',
    originalPrice: '280.000đ',
    tripsCount: 3,
    distance: '315 km',
    duration: '6.0 giờ'
  },
  {
    title: 'Đà Nẵng - Quảng Bình (Đồng Hới)',
    dest: 'Quảng Bình',
    image: 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=600&q=80',
    bgColor: '#8C7F67',
    price: '190.000đ',
    originalPrice: '250.000đ',
    tripsCount: 3,
    distance: '270 km',
    duration: '5.5 giờ'
  },
  {
    title: 'Đà Nẵng - Buôn Ma Thuột (Đắk Lắk)',
    dest: 'Buôn Ma Thuột',
    image: 'https://images.unsplash.com/photo-1511497584788-87676104235f?auto=format&fit=crop&w=600&q=80',
    bgColor: '#2E5A44',
    price: '290.000đ',
    originalPrice: '380.000đ',
    tripsCount: 2,
    distance: '480 km',
    duration: '9.5 giờ'
  }
]);

const scrollRoutes = (direction) => {
  if (routesContainer.value) {
    const scrollAmount = 320;
    routesContainer.value.scrollBy({
      left: direction === 'left' ? -scrollAmount : scrollAmount,
      behavior: 'smooth'
    });
  }
};

const selectRoute = (item) => {
  router.get('/trips', {
    destination: item.dest
  });
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
