<template>
  <div>
    <!-- Nút Nổi Mở Trợ Lý AI (Góc Dưới Phải) -->
    <div
      v-if="!isOpen"
      class="fixed bottom-6 right-6 z-50 flex items-center gap-3 cursor-pointer group"
      @click="openModal"
    >
      <div class="hidden sm:flex flex-col items-end bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-indigo-100 text-xs font-semibold text-indigo-900 group-hover:scale-105 transition-all">
        <span>Trợ Lý AI BusHub</span>
        <span class="text-[10px] text-emerald-600 font-normal flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
          Trực tuyến 24/7
        </span>
      </div>
      <button
        class="w-14 h-14 rounded-full bg-gradient-to-tr from-indigo-600 via-blue-600 to-indigo-700 text-white shadow-xl shadow-indigo-500/30 flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-200 border-2 border-white relative"
        title="Trợ Lý AI BusHub"
      >
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
        <svg class="w-7 h-7 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
      </button>
    </div>

    <!-- Khung Chat AI Cao Cấp (Có Thể Kéo Thả Tự Do) -->
    <div
      v-if="isOpen"
      ref="chatWindowRef"
      class="fixed z-50 select-none shadow-2xl rounded-2xl overflow-hidden bg-white border border-slate-200/90 flex flex-col transition-shadow duration-200"
      :style="modalStyle"
    >
      <!-- HEADER: Khu Vực Kéo Thả (Drag Handle) -->
      <div
        class="bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 px-4 py-3 text-white flex items-center justify-between cursor-move shadow-sm select-none"
        @mousedown="startDrag"
        @touchstart.passive="startDragTouch"
      >
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 text-base">
            🤖
          </div>
          <div>
            <div class="flex items-center gap-1.5">
              <h3 class="font-bold text-sm leading-none">Trợ Lý AI BusHub</h3>
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
            <p class="text-[11px] text-blue-100/90 mt-0.5">Bến xe Trung tâm Đà Nẵng</p>
          </div>
        </div>

        <!-- Các Nút Thao Tác Header Tinh Gọn -->
        <div class="flex items-center gap-1 text-white/90">
          <button
            @click="resetPosition"
            class="p-1.5 hover:bg-white/20 rounded-lg transition-all"
            title="Căn chỉnh lại vị trí mặc định"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
          </button>
          <button
            @click="closeModal"
            class="p-1.5 hover:bg-red-500/80 rounded-lg transition-all text-white"
            title="Đóng khung chat"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- THANH CHUYỂN TAB SANG TRỌNG -->
      <div class="flex border-b border-slate-200 bg-slate-100/80 p-1 gap-1">
        <button
          @click="activeTab = 'chat'"
          class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5"
          :class="activeTab === 'chat' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
          <span>💬</span>
          <span>Trò Chuyện AI</span>
        </button>
        <button
          @click="activeTab = 'autoNotify'"
          class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5"
          :class="activeTab === 'autoNotify' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
          <span>📧</span>
          <span>Gửi Gmail Tự Động</span>
        </button>
      </div>

      <!-- TAB 1: TRÒ CHUYỆN AI -->
      <div v-if="activeTab === 'chat'" class="flex flex-col flex-1 h-[420px] bg-slate-50">
        <!-- Danh Sách Tin Nhắn -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-3.5 space-y-3">
          <div
            v-for="(msg, index) in messages"
            :key="index"
            class="flex flex-col"
            :class="msg.sender === 'user' ? 'items-end' : 'items-start'"
          >
            <div
              class="max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm leading-relaxed shadow-2xs"
              :class="msg.sender === 'user'
                ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-br-none'
                : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-none'"
            >
              <div class="whitespace-pre-line" v-html="formatMessage(msg.text)"></div>

              <!-- Danh sách chuyến xe đính kèm -->
              <div v-if="msg.trips && msg.trips.length > 0" class="mt-2.5 space-y-2">
                <div
                  v-for="trip in msg.trips"
                  :key="trip.id"
                  @click="goToTrip(trip.id)"
                  class="bg-indigo-50/90 hover:bg-indigo-100 border border-indigo-200/80 rounded-xl p-2.5 cursor-pointer transition-all hover:shadow-xs"
                >
                  <div class="flex items-center justify-between font-bold text-indigo-950 text-xs">
                    <span>{{ trip.route_name }}</span>
                    <span class="text-indigo-600">{{ trip.price }}</span>
                  </div>
                  <div class="text-[11px] text-slate-600 mt-1 flex items-center justify-between">
                    <span>⏰ Xuất bến: <b>{{ trip.departure_time }}</b></span>
                    <span class="font-mono bg-white px-1.5 py-0.5 rounded border text-[10px] font-bold text-slate-800">{{ trip.bus_plate }}</span>
                  </div>
                  <div class="text-[10px] text-slate-500 mt-0.5">
                    👨‍✈️ Bác tài: {{ trip.driver }}
                  </div>
                </div>
              </div>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 px-1">{{ msg.time }}</span>
          </div>

          <div v-if="loading" class="flex items-center gap-2 text-xs text-slate-500 bg-white p-2.5 rounded-xl border border-slate-200 w-fit shadow-2xs">
            <div class="w-3.5 h-3.5 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            <span>AI đang tìm dữ liệu chuyến xe...</span>
          </div>
        </div>

        <!-- Gợi ý câu hỏi nhanh -->
        <div class="px-3 py-1.5 bg-slate-100/90 border-t border-slate-200 flex gap-1.5 overflow-x-auto no-scrollbar">
          <button
            v-for="(sug, idx) in suggestions"
            :key="idx"
            @click="sendSuggestion(sug)"
            class="whitespace-nowrap text-[11px] bg-white border border-slate-200 text-indigo-700 hover:bg-indigo-50 px-2.5 py-1 rounded-full font-medium transition-all shadow-2xs"
          >
            {{ sug }}
          </button>
        </div>

        <!-- Ô Nhập Tin Nhắn -->
        <div class="p-2.5 bg-white border-t border-slate-200 flex items-center gap-2">
          <input
            v-model="inputQuery"
            @keyup.enter="sendMessage"
            type="text"
            placeholder="Hỏi về vé xe, lịch trình, chính sách cọc..."
            class="flex-1 text-xs sm:text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-xl px-3.5 py-2 outline-none"
          />
          <button
            @click="sendMessage"
            :disabled="!inputQuery.trim() || loading"
            class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white p-2.5 rounded-xl transition-all shadow-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </div>

      <!-- TAB 2: AI TỰ ĐỘNG GỬI GMAIL TRƯỚC 45P & SOẠN THÔNG BÁO TÙY CHỈNH -->
      <div v-else class="flex flex-col flex-1 h-[420px] bg-slate-50 p-3 overflow-y-auto space-y-3">
        <!-- Banner Tự Động Quét Gmail 45 Phút -->
        <div class="bg-gradient-to-r from-indigo-900 to-blue-900 text-white p-3 rounded-2xl shadow-xs">
          <div class="flex items-center justify-between">
            <h4 class="font-bold text-xs sm:text-sm flex items-center gap-1.5">
              <span>⏰</span> Tự Động Nhận Diện & Gửi Gmail (Trước 45p)
            </h4>
            <span class="text-[10px] bg-emerald-500 text-white px-2 py-0.5 rounded-full font-bold">TỰ ĐỘNG BẬT</span>
          </div>
          <p class="text-[11px] text-indigo-200 mt-1 leading-relaxed">
            Hệ thống AI tự động phát hiện chuyến xe nào sắp đến giờ chạy trước 45 phút để gửi email nhắc cổng đón, biển số xe và bác tài về Gmail của khách.
          </p>
          <div class="mt-2 flex items-center gap-2">
            <button
              @click="triggerAutoNotify"
              :disabled="autoNotifyLoading"
              class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-1.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm"
            >
              <span v-if="autoNotifyLoading" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span>⚡ Quét Tự Động 45p Ngay</span>
            </button>
            <button
              @click="showCustomForm = !showCustomForm"
              class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-xl text-xs font-bold transition-all"
            >
              {{ showCustomForm ? 'Đóng Soạn Tin' : '✍️ Soạn Tin Tùy Chỉnh' }}
            </button>
          </div>
        </div>

        <!-- FORM SOẠN THÔNG BÁO TÙY CHỈNH CHO PHÉP TỰ GỬI NỘI DUNG BẤT KỲ -->
        <div v-if="showCustomForm" class="bg-white p-3 rounded-2xl border border-indigo-200 shadow-sm space-y-2">
          <div class="flex items-center justify-between">
            <h5 class="font-bold text-xs text-indigo-950 flex items-center gap-1">
              <span>✍️</span> Soạn Thông Báo Gửi Về Gmail Khách Hàng
            </h5>
            <span class="text-[10px] text-slate-400">Gửi tức thì</span>
          </div>

          <!-- Chọn chuyến xe -->
          <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase">Chuyến xe áp dụng:</label>
            <select
              v-model="customTripId"
              class="w-full mt-0.5 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium focus:bg-white outline-none"
            >
              <option :value="1">Tất cả chuyến xe sắp xuất bến hôm nay</option>
              <option v-for="t in availableTrips" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>

          <!-- Tiêu đề email -->
          <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase">Tiêu đề thông báo:</label>
            <input
              v-model="customSubject"
              type="text"
              placeholder="VD: Thông báo đổi cổng đón số 2 / Lưu ý thời tiết..."
              class="w-full mt-0.5 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium focus:bg-white outline-none"
            />
          </div>

          <!-- Nội dung -->
          <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase">Nội dung chi tiết:</label>
            <textarea
              v-model="customContent"
              rows="2"
              placeholder="Nhập nội dung bạn muốn gửi tới khách hàng..."
              class="w-full mt-0.5 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium focus:bg-white outline-none"
            ></textarea>
          </div>

          <button
            @click="sendCustomNotification"
            :disabled="customSending || !customContent.trim()"
            class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs py-2 rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5"
          >
            <span v-if="customSending" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>📧 Gửi Gmail Cho Khách Ngay</span>
          </button>
        </div>

        <!-- Danh sách email đã gửi tự động -->
        <div v-if="autoNotifications.length > 0" class="space-y-2">
          <div
            v-for="item in autoNotifications"
            :key="item.id"
            class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs text-xs space-y-1.5"
          >
            <div class="flex items-center justify-between">
              <span
                class="px-2 py-0.5 rounded text-[10px] font-bold"
                :class="item.type.includes('45M') ? 'bg-amber-100 text-amber-900' : (item.type.includes('CUSTOM') ? 'bg-emerald-100 text-emerald-900' : 'bg-blue-100 text-blue-900')"
              >
                {{ item.badge }}
              </span>
              <span class="text-[10px] text-slate-400 font-mono">{{ item.time_sent }}</span>
            </div>

            <!-- Tuyến đường & Biển số xe -->
            <div class="flex items-center justify-between font-bold text-slate-900 pt-0.5">
              <span>{{ item.route_name }}</span>
              <span class="font-mono bg-slate-100 px-2 py-0.5 rounded border border-slate-200 text-indigo-700 text-[11px]">
                {{ item.bus_plate }}
              </span>
            </div>

            <!-- Email người nhận -->
            <div class="text-[11px] text-slate-500 flex items-center gap-1">
              <span>Gửi đến:</span>
              <span class="font-medium text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ item.recipient_email || 'khachhang@gmail.com' }}</span>
              <span>({{ item.recipient_count }} khách)</span>
            </div>

            <!-- Nội dung email -->
            <p class="text-[11px] text-slate-600 bg-slate-50/80 p-2.5 rounded-lg border border-slate-100 leading-relaxed">
              {{ item.message }}
            </p>

            <!-- Trạng thái gửi -->
            <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-100">
              <span class="text-indigo-600 font-medium">Hệ thống Gmail SMTP</span>
              <span class="text-emerald-600 font-bold flex items-center gap-1">
                ✓ Đã chuyển phát vào hộp thư đến
              </span>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8 text-xs text-slate-400">
          <p>Đang tải dữ liệu thông báo gửi Gmail...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';

const isOpen = ref(false);
const activeTab = ref('chat');
const loading = ref(false);
const autoNotifyLoading = ref(false);
const inputQuery = ref('');
const messagesContainer = ref(null);
const autoNotifications = ref([]);
const availableTrips = ref([]);

// Custom announcement
const showCustomForm = ref(false);
const customTripId = ref(1);
const customSubject = ref('Thông báo cập nhật từ Bến xe Đà Nẵng');
const customContent = ref('');
const customSending = ref(false);

// Tọa độ draggable
const posX = ref(null);
const posY = ref(null);
const isDragging = ref(false);
const dragStartX = ref(0);
const dragStartY = ref(0);

const suggestions = ref([
  'Đà Nẵng đi Huế',
  'Đà Nẵng đi Quy Nhơn',
  'Chính sách cọc 30%',
  'Điểm đón & Điểm trả'
]);

const messages = ref([
  {
    sender: 'ai',
    text: 'Xin chào! Tôi là Trợ Lý AI của BusHub Bến xe Trung tâm Đà Nẵng.\nBạn có thể hỏi về lịch trình các tuyến xe, giá vé, chính sách cọc 30%, hoặc bật gửi Gmail thông báo đón/trả tự động trước 45 phút!',
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    trips: []
  }
]);

const modalStyle = computed(() => {
  if (posX.value === null || posY.value === null) {
    return {
      bottom: '24px',
      right: '24px',
      width: '390px',
      maxWidth: 'calc(100vw - 32px)',
    };
  }
  return {
    left: `${posX.value}px`,
    top: `${posY.value}px`,
    width: '390px',
    maxWidth: 'calc(100vw - 32px)',
  };
});

const openModal = () => {
  isOpen.value = true;
  nextTick(() => scrollToBottom());
};

const closeModal = () => {
  isOpen.value = false;
};

const resetPosition = () => {
  posX.value = null;
  posY.value = null;
};

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

const formatMessage = (text) => {
  if (!text) return '';
  return text
    .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
    .replace(/\*(.*?)\*/g, '<i>$1</i>')
    .replace(/`(.*?)`/g, '<code class="bg-slate-100 text-indigo-700 px-1 rounded font-mono text-[11px] font-bold">$1</code>');
};

const sendMessage = async () => {
  const q = inputQuery.value.trim();
  if (!q || loading.value) return;

  const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  messages.value.push({
    sender: 'user',
    text: q,
    time: now,
    trips: []
  });
  inputQuery.value = '';
  loading.value = true;
  nextTick(() => scrollToBottom());

  try {
    const res = await axios.post('/ai/chat', { message: q });
    const replyText = res.data.reply || 'Cảm ơn bạn đã hỏi. Tôi có thể hỗ trợ gì thêm không?';
    const trips = res.data.data || [];

    messages.value.push({
      sender: 'ai',
      text: replyText,
      time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      trips: trips
    });

    if (res.data.suggestions && res.data.suggestions.length > 0) {
      suggestions.value = res.data.suggestions;
    }
  } catch (error) {
    console.error('AI Chat Error:', error);
    messages.value.push({
      sender: 'ai',
      text: '🚌 **BusHub Bến xe Đà Nẵng** phục vụ liên tục các tuyến: Huế, Quảng Trị, Quảng Bình, Quy Nhơn, Buôn Ma Thuột. Tất cả chuyến xe đều xuất bến đúng giờ tại Bến xe Trung tâm Đà Nẵng. Quý khách có thể xem trực tiếp lịch trình trên mục "Tìm Chuyến Xe" hoặc "Lịch Trình"!',
      time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      trips: []
    });
  } finally {
    loading.value = false;
    nextTick(() => scrollToBottom());
  }
};

const sendSuggestion = (sug) => {
  inputQuery.value = sug;
  sendMessage();
};

const goToTrip = (tripId) => {
  window.location.href = `/trips/${tripId}`;
};

const triggerAutoNotify = async () => {
  autoNotifyLoading.value = true;
  try {
    const res = await axios.post('/ai/auto-notify');
    if (res.data.notifications) {
      autoNotifications.value = res.data.notifications;
    }
    if (res.data.trips_available) {
      availableTrips.value = res.data.trips_available;
    }
  } catch (err) {
    console.error('Auto notify error:', err);
  } finally {
    autoNotifyLoading.value = false;
  }
};

const sendCustomNotification = async () => {
  if (!customContent.value.trim()) return;
  customSending.value = true;
  try {
    const res = await axios.post('/ai/custom-notify', {
      trip_id: customTripId.value,
      subject: customSubject.value,
      content: customContent.value
    });
    if (res.data.notification) {
      autoNotifications.value.unshift(res.data.notification);
      customContent.value = '';
      showCustomForm.value = false;
      alert('Đã gửi thông báo về Gmail cho khách hàng thành công!');
    }
  } catch (err) {
    console.error('Custom notify error:', err);
    alert('Không thể gửi thông báo. Vui lòng thử lại.');
  } finally {
    customSending.value = false;
  }
};

// Draggable Logic
const startDrag = (e) => {
  if (e.target.closest('button') || e.target.closest('input') || e.target.closest('textarea') || e.target.closest('select')) return;
  isDragging.value = true;
  const rect = e.currentTarget.closest('.fixed').getBoundingClientRect();
  dragStartX.value = e.clientX - rect.left;
  dragStartY.value = e.clientY - rect.top;

  window.addEventListener('mousemove', onDrag);
  window.addEventListener('mouseup', stopDrag);
};

const onDrag = (e) => {
  if (!isDragging.value) return;
  const newX = e.clientX - dragStartX.value;
  const newY = e.clientY - dragStartY.value;

  const maxX = window.innerWidth - 400;
  const maxY = window.innerHeight - 470;

  posX.value = Math.max(10, Math.min(maxX, newX));
  posY.value = Math.max(10, Math.min(maxY, newY));
};

const stopDrag = () => {
  isDragging.value = false;
  window.removeEventListener('mousemove', onDrag);
  window.removeEventListener('mouseup', stopDrag);
};

// Touch drag support
const startDragTouch = (e) => {
  if (e.target.closest('button') || !e.touches[0]) return;
  isDragging.value = true;
  const rect = e.currentTarget.closest('.fixed').getBoundingClientRect();
  dragStartX.value = e.touches[0].clientX - rect.left;
  dragStartY.value = e.touches[0].clientY - rect.top;

  window.addEventListener('touchmove', onDragTouch, { passive: false });
  window.addEventListener('touchend', stopDragTouch);
};

const onDragTouch = (e) => {
  if (!isDragging.value || !e.touches[0]) return;
  e.preventDefault();
  const newX = e.touches[0].clientX - dragStartX.value;
  const newY = e.touches[0].clientY - dragStartY.value;

  const maxX = window.innerWidth - 350;
  const maxY = window.innerHeight - 470;

  posX.value = Math.max(10, Math.min(maxX, newX));
  posY.value = Math.max(10, Math.min(maxY, newY));
};

const stopDragTouch = () => {
  isDragging.value = false;
  window.removeEventListener('touchmove', onDragTouch);
  window.removeEventListener('touchend', stopDragTouch);
};

onMounted(() => {
  triggerAutoNotify();
});
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