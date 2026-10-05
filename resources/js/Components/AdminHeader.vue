<template>
  <header class="bg-white border-b border-slate-200 sticky top-0 z-50 px-4 sm:px-6 py-3 shadow-xs">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row lg:items-center justify-between gap-3">
      <!-- Brand & Info -->
      <div class="flex items-center space-x-3">
        <Link href="/admin" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 flex items-center justify-center font-black text-white text-base shadow-sm shadow-indigo-500/20 shrink-0 hover:opacity-95 transition">
          🚌
        </Link>
        <div>
          <div class="flex items-center space-x-2">
            <h1 class="text-base font-black text-slate-900 leading-tight tracking-tight">{{ title }}</h1>
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">
              ADMIN PORTAL
            </span>
          </div>
          <p class="text-[11px] text-slate-500 hidden sm:block">{{ subtitle }}</p>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <nav class="flex items-center flex-wrap gap-1.5 overflow-x-auto pb-1 lg:pb-0">
        <Link 
          v-for="item in navItems" 
          :key="item.path"
          :href="item.path"
          :class="[
            'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center space-x-1.5 whitespace-nowrap cursor-pointer',
            $page.url.startsWith(item.path) && (item.path !== '/admin' || $page.url === '/admin')
              ? 'bg-indigo-600 text-white shadow-xs'
              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 bg-slate-50/80 border border-slate-200/80'
          ]"
        >
          <span>{{ item.icon }}</span>
          <span>{{ item.label }}</span>
        </Link>

        <div class="h-4 w-px bg-slate-200 mx-1 hidden lg:block"></div>

        <Link 
          href="/" 
          class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition flex items-center space-x-1"
        >
          <span>🏠</span>
          <span>Trang Khách</span>
        </Link>

        <Link 
          href="/driver" 
          class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition flex items-center space-x-1"
        >
          <span>👨‍✈️</span>
          <span>Bác Tài</span>
        </Link>

        <Link 
          href="/conductor" 
          class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition flex items-center space-x-1"
        >
          <span>🎫</span>
          <span>Lơ Xe</span>
        </Link>
      </nav>
    </div>

    <!-- Floating Persistent AI Assistant -->
    <AiChatModal />
  </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AiChatModal from './AiChatModal.vue';

defineProps({
  title: {
    type: String,
    default: 'BusHub Terminal Management'
  },
  subtitle: {
    type: String,
    default: 'Quản trị điều hành, lịch trình, đội xe và doanh thu'
  }
});

const navItems = [
  { label: 'Tổng quan', path: '/admin', icon: '📊' },
  { label: 'Quản lý Đội xe', path: '/admin/buses', icon: '🚌' },
  { label: 'Hành khách & CRM', path: '/admin/customers', icon: '👥' },
  { label: 'Lịch trình chuyến', path: '/admin/trips', icon: '🚍' },
  { label: 'Tuyến đường', path: '/admin/routes', icon: '🗺️' },
  { label: 'Tài xế', path: '/admin/drivers', icon: '👨‍✈️' },
  { label: 'Tài chính', path: '/admin/finance', icon: '💰' },
];
</script>