<template>
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 gap-4">
        <!-- Logo Brand BusHub -->
        <Link href="/" class="flex items-center gap-2.5 group flex-shrink-0">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 flex items-center justify-center text-white font-black text-xl shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-all">
            🚌
          </div>
          <div>
            <span class="text-xl font-black tracking-tight text-slate-900 flex items-center gap-1">
              Bus<span class="text-indigo-600">Hub</span>
            </span>
            <span class="text-[10px] text-slate-400 font-medium block -mt-1 tracking-wider uppercase">Bến Xe Trung Tâm Đà Nẵng</span>
          </div>
        </Link>

        <!-- Thanh Tìm Kiếm Nhanh Trên Navbar -->
        <div class="hidden lg:flex items-center flex-1 max-w-xs mx-4">
          <div class="relative w-full">
            <input
              type="text"
              v-model="quickSearchQuery"
              @keydown.enter="handleQuickSearch"
              placeholder="Tìm tuyến, điểm đến (Huế, Quảng Trị...)"
              class="w-full bg-slate-100/80 hover:bg-slate-100 focus:bg-white text-xs text-slate-800 placeholder-slate-400 rounded-full pl-9 pr-8 py-2 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button
              v-if="quickSearchQuery"
              @click="quickSearchQuery = ''"
              class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Menu Điều Hướng Chuẩn Gọn Gàng -->
        <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
          <Link
            href="/"
            class="hover:text-indigo-600 transition-colors py-1"
            :class="$page.url === '/' ? 'text-indigo-600 font-bold border-b-2 border-indigo-600' : ''"
          >
            Trang Chủ
          </Link>
          <Link
            href="/trips"
            class="hover:text-indigo-600 transition-colors py-1 flex items-center gap-1.5"
            :class="$page.url.startsWith('/trips') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600' : ''"
          >
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Tìm Chuyến Xe</span>
          </Link>
          <Link
            href="/schedules"
            class="hover:text-indigo-600 transition-colors py-1"
            :class="$page.url.startsWith('/schedules') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600' : ''"
          >
            Lịch Trình Hàng Ngày
          </Link>
        </nav>

        <!-- Cổng Đăng Nhập & Nút Đặt Vé -->
        <div class="flex items-center gap-3">
          <!-- Dropdown Phân Quyền Nhanh Gọn Gàng -->
          <div class="relative">
            <button
              @click="isRoleOpen = !isRoleOpen"
              class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200/80 text-slate-800 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 border border-slate-200 cursor-pointer"
            >
              <span class="text-amber-500">⚡</span>
              <span>Đăng nhập</span>
              <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="isRoleOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Menu thả xuống -->
            <div
              v-if="isRoleOpen"
              class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-200/80 py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
            >
              <div class="px-4 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                Chuyển Nhanh Quyền
              </div>
              <a
                href="/auth/fast-login/admin"
                class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors"
              >
                <span>👔</span>
                <div>
                  <p class="leading-none">Quản Trị Viên (Admin)</p>
                  <p class="text-[10px] text-slate-400 mt-0.5">Quản lý lượt chạy & doanh thu</p>
                </div>
              </a>
              <a
                href="/auth/fast-login/driver"
                class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors"
              >
                <span>👨‍✈️</span>
                <div>
                  <p class="leading-none">Bác Tài (Driver)</p>
                  <p class="text-[10px] text-slate-400 mt-0.5">Nhận chuyến & kiểm tra xe</p>
                </div>
              </a>
              <a
                href="/auth/fast-login/conductor"
                class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors"
              >
                <span>🎫</span>
                <div>
                  <p class="leading-none">Lơ Xe / Tiếp Viên</p>
                  <p class="text-[10px] text-slate-400 mt-0.5">Sơ đồ ghế & soát vé</p>
                </div>
              </a>
              <div class="border-t border-slate-100 mt-1 pt-1">
                <Link
                  href="/scanner"
                  class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors"
                >
                  <span>📷</span>
                  <span>Quét Mã QR Vé</span>
                </Link>
              </div>
            </div>
          </div>

          <Link
            href="/trips"
            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-500/20 transition-all cursor-pointer flex items-center gap-1.5"
          >
            <span>Đặt Vé Ngay</span>
          </Link>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const isRoleOpen = ref(false);
const quickSearchQuery = ref('');

const handleQuickSearch = () => {
  if (quickSearchQuery.value.trim()) {
    router.get('/trips', {
      destination: quickSearchQuery.value.trim()
    });
  } else {
    router.get('/trips');
  }
};
</script>
