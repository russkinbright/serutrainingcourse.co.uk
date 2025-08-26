<div class="container-fluid mx-5 p-6 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl shadow-xl border border-purple-300 backdrop-blur-sm"
     x-data="activitiesPanel()" x-init="init()" x-cloak>

  <!-- Header and Month/Day Filter -->
  <div class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <h3 class="text-2xl font-extrabold text-purple-900 tracking-tight drop-shadow-sm">
      Recent Activity
    </h3>

    <div class="flex items-center gap-3">
      <!-- Month select -->
      <div class="relative">
        <select x-model="selectedMonth" @change="updateDaysForMonth()"
                class="appearance-none pl-3 pr-10 py-2 border border-purple-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-400 focus:border-purple-400 text-gray-700">
          <option value="">All Months</option>
          <template x-for="month in months" :key="month">
            <option :value="month" x-text="month"></option>
          </template>
        </select>
      </div>

      <!-- Day select (enabled only when a month is chosen) -->
      <div class="relative">
        <select x-model="selectedDay" :disabled="!selectedMonth"
                class="appearance-none pl-3 pr-10 py-2 border border-purple-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-400 focus:border-purple-400 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
          <option value="">All Days</option>
          <template x-for="d in daysInSelectedMonth" :key="d">
            <option :value="d" x-text="d"></option>
          </template>
        </select>
      </div>
    </div>
  </div>

  <!-- Error Message -->
  <div x-show="error" class="p-4 mb-5 bg-red-100 text-red-800 rounded-xl shadow-sm border border-red-200">
    <p x-text="error"></p>
  </div>

  <!-- Activities List -->
  <div class="bg-white/90 rounded-xl shadow-inner p-6 backdrop-blur-md relative"
       x-show="!isLoading && !error"
       x-ref="activitiesList"
       @scroll="onScroll"
       style="height:80vh; overflow-y:auto;">

    <!-- Jump to latest -->
    <button x-show="showJumpToLatest"
            @click="jumpToLatest()"
            class="fixed right-6 bottom-6 z-10 px-3 py-2 rounded-full bg-purple-600 text-white text-sm shadow-lg hover:bg-purple-700">
      Jump to latest
    </button>

    <ul class="space-y-4">
      <template x-for="(activity, index) in filtered" :key="activity.id">
        <div>
          <!-- Date separator -->
          <div x-show="index === 0 || getDateOnly(activity.created_at) !== getDateOnly(filtered[index - 1]?.created_at)"
               class="my-4 relative">
            <hr class="border-purple-300">
            <span
              class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-purple-50 px-3 py-1 text-sm font-medium text-purple-700 rounded-full shadow-md"
              x-text="formatDateOnly(activity.created_at)"></span>
          </div>

          <li class="p-4 mt-8 bg-gradient-to-r from-purple-200 to-purple-300 rounded-xl flex items-start border border-purple-300 hover:shadow-lg transition-all duration-300 min-h-[70px]">
            <span class="w-10 h-10 flex items-center justify-center rounded-full bg-purple-600 text-white text-lg mr-4 mt-1">📢</span>
            <div class="flex-1">
              <p class="text-purple-900 font-semibold" x-html="activity.message"></p>
              <p class="text-xs text-purple-600 mt-2 font-medium" x-text="formatDate(activity.created_at)"></p>
            </div>
          </li>
        </div>
      </template>

      <li x-show="filtered.length === 0" class="text-center text-purple-600 font-medium py-6">
        <template x-if="selectedMonth && selectedDay">
          <p x-text="'No activities found for ' + selectedMonth + ' ' + selectedDay"></p>
        </template>
        <template x-if="selectedMonth && !selectedDay">
          <p x-text="'No activities found for ' + selectedMonth"></p>
        </template>
        <template x-if="!selectedMonth">
          <p>No activities found</p>
        </template>
      </li>
    </ul>

  </div>
</div>

<style>
  @keyframes bounce-in {
    0% { opacity: 0; transform: scale(0.95); }
    50% { opacity: 0.5; transform: scale(1.05); }
    100% { opacity: 1; transform: scale(1); }
  }
  @media (prefers-reduced-motion: reduce) {
    .animate-bounce-in { animation: none !important; }
  }
  .animate-bounce-in { animation: bounce-in 0.5s ease-out; }

  [x-ref="activitiesList"]::-webkit-scrollbar { width: 8px; }
  [x-ref="activitiesList"]::-webkit-scrollbar-track { background: #f3e8ff; border-radius: 8px; }
  [x-ref="activitiesList"]::-webkit-scrollbar-thumb { background-color: #a78bfa; border-radius: 8px; }
</style>

<script>
function activitiesPanel() {
  return {
    // --- state ---
    activities: [],
    activitiesById: new Set(),
    filtered: [],
    isLoading: false,
    error: null,
    lastActivityId: null,

    // auto-scroll state
    shouldAutoScroll: false,       // follow only when user is at bottom
    showJumpToLatest: false,       // show button if updates arrive while user is scrolled up
    nearBottomThreshold: 120,

    // filters
    months: ['January','February','March','April','May','June','July','August','September','October','November','December'],
    selectedMonth: null,
    selectedDay: null,
    daysInSelectedMonth: [],
    currentYear: (new Date()).getFullYear(),

    // --- helpers ---
    formatDate(d) {
      return new Date(d).toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'2-digit', minute:'2-digit' });
    },
    formatDateOnly(d) {
      return new Date(d).toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric' });
    },
    getDateOnly(d) { return new Date(d).toDateString(); },

    monthNameToNumber(name) { return this.months.indexOf(name); },
    getDaysInMonth(monthName, year) {
      const m = this.monthNameToNumber(monthName);
      if (m < 0) return 31;
      return new Date(year, m + 1, 0).getDate();
    },
    updateDaysForMonth() {
      if (!this.selectedMonth) {
        this.daysInSelectedMonth = [];
        this.selectedDay = null;
      } else {
        const count = this.getDaysInMonth(this.selectedMonth, this.currentYear);
        this.daysInSelectedMonth = Array.from({ length: count }, (_, i) => i + 1);
        this.selectedDay = null;
      }
      this.recomputeFiltered();
    },

    // recompute filtered list once per change
    recomputeFiltered() {
      const month = this.selectedMonth;
      const day = Number(this.selectedDay);
      this.filtered = this.activities.filter(a => {
        const d = a.__d || (a.__d = new Date(a.created_at));
        const monthName = a.__monthName || (a.__monthName = d.toLocaleString('en-US', { month: 'long' }));
        const dateNum   = a.__dateNum   || (a.__dateNum   = d.getDate());
        const monthMatch = !month || monthName === month;
        const dayMatch   = !day || dateNum === day;
        return monthMatch && dayMatch;
      });
    },

    // --- scrolling logic ---
    isNearBottom() {
      const c = this.$refs.activitiesList;
      if (!c) return false;
      return (c.scrollHeight - c.scrollTop - c.clientHeight) < this.nearBottomThreshold;
    },
    onScroll() {
      this.shouldAutoScroll = this.isNearBottom();
      if (this.shouldAutoScroll) this.showJumpToLatest = false;
    },
    jumpToLatest() {
      const c = this.$refs.activitiesList;
      if (!c) return;
      c.scrollTo({ top: c.scrollHeight, behavior: 'auto' });
      this.shouldAutoScroll = true;
      this.showJumpToLatest = false;
    },
    scrollToBottomIfAllowed() {
      if (!this.shouldAutoScroll) return;
      this.$nextTick(() => {
        const c = this.$refs.activitiesList;
        if (!c) return;
        c.scrollTo({ top: c.scrollHeight, behavior: 'auto' });
      });
    },

    // --- fetching ---
    async fetchLatestActivity() {
      if (this.isLoading) return;
      this.isLoading = true; this.error = null;
      try {
        const resp = await fetch('{{ route('activities.get') }}?last_id=' + (this.lastActivityId || 0), {
          headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
        });
        if (!resp.ok) throw new Error('Network response was not ok');
        const data = await resp.json();

        if (Array.isArray(data) && data.length) {
          let maxId = this.lastActivityId || 0;
          const newOnes = [];
          for (const a of data) {
            if (!this.activitiesById.has(a.id)) {
              this.activitiesById.add(a.id);
              newOnes.push(a);
              if (a.id > maxId) maxId = a.id;
            }
          }
          if (newOnes.length) {
            this.activities.push(...newOnes);
            this.lastActivityId = maxId;
            this.recomputeFiltered();

            if (!this.shouldAutoScroll) {
              this.showJumpToLatest = true;
            } else {
              this.scrollToBottomIfAllowed();
            }
          }
        }
      } catch (e) {
        console.error(e);
        this.error = 'Failed to load activities. Please try again.';
      } finally {
        this.isLoading = false;
      }
    },

    // polling with backoff + pause when hidden
    pollTimer: null,
    pollDelay: 2000,
    maxPollDelay: 15000,

    async pollOnce() {
      if (document.hidden) return;
      try {
        const resp = await fetch('{{ route('activities.poll') }}?last_id=' + (this.lastActivityId || 0), {
          headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
        });
        if (!resp.ok) throw new Error('Network response was not ok');
        const data = await resp.json();

        let gotNew = false;
        if (Array.isArray(data) && data.length) {
          let maxId = this.lastActivityId || 0;
          const newOnes = [];
          for (const a of data) {
            if (!this.activitiesById.has(a.id)) {
              this.activitiesById.add(a.id);
              newOnes.push(a);
              if (a.id > maxId) maxId = a.id;
            }
          }
          if (newOnes.length) {
            this.activities.push(...newOnes);
            this.lastActivityId = maxId;
            this.recomputeFiltered();
            gotNew = true;

            if (!this.shouldAutoScroll) {
              this.showJumpToLatest = true;
            } else {
              this.scrollToBottomIfAllowed();
            }
          }
        }

        // backoff management
        this.pollDelay = gotNew ? 2000 : Math.min(this.pollDelay + 1000, this.maxPollDelay);
      } catch (e) {
        console.error(e);
        this.error = 'Failed to load activities. Retrying...';
        this.pollDelay = Math.min(this.pollDelay * 2, this.maxPollDelay);
      }
    },

    startPolling() {
      const tick = async () => {
        await this.pollOnce();
        this.pollTimer = setTimeout(tick, this.pollDelay);
      };
      tick();

      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          if (this.pollTimer) { clearTimeout(this.pollTimer); this.pollTimer = null; }
        } else if (!this.pollTimer) {
          this.pollDelay = 2000;
          this.startPolling();
        }
      });
    },

    // --- init ---
    init() {
      // set initial auto-scroll preference based on current scroll position
      this.$nextTick(() => { this.shouldAutoScroll = this.isNearBottom(); });

      this.fetchLatestActivity();
      this.startPolling();

      this.selectedMonth = this.months[new Date().getMonth()];
      this.updateDaysForMonth();

      this.$watch('selectedDay', () => this.recomputeFiltered());
      this.$watch('selectedMonth', () => this.recomputeFiltered());
      this.$watch('activities.length', () => this.recomputeFiltered());
    }
  };
}
</script>


<style>
    @keyframes bounce-in {
        0% {
            opacity: 0;
            transform: scale(0.95);
        }

        50% {
            opacity: 0.5;
            transform: scale(1.05);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .animate-bounce-in {
            animation: none !important;
        }
    }


    .animate-bounce-in {
        animation: bounce-in 0.5s ease-out;
    }

    [x-ref="activitiesList"]::-webkit-scrollbar {
        width: 8px;
    }

    [x-ref="activitiesList"]::-webkit-scrollbar-track {
        background: #f3e8ff;
        /* light purple track */
        border-radius: 8px;
    }

    [x-ref="activitiesList"]::-webkit-scrollbar-thumb {
        background-color: #a78bfa;
        /* purple thumb */
        border-radius: 8px;
    }
</style>

