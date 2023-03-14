<script setup>
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import {
  blankEvent,
  useCalendarOld
} from '@/views/apps/calendar/useCalendar'
import { useResponsiveLeftSidebar } from '@core/composable/useResponsiveSidebar'
import useCalendar from "@/composables/useCalendar";
import CalendarEventHandler from '@/views/apps/calendar/CalendarEventHandler.vue'
import { watch, watchEffect, ref } from "vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import listPlugin from "@fullcalendar/list";

// 👉 Event
const event = ref(structuredClone(blankEvent))
const isEventHandlerSidebarActive = ref(false)

watch(isEventHandlerSidebarActive, val => {
  if (!val)
    event.value = structuredClone(blankEvent)
})


const { isLeftSidebarOpen } = useResponsiveLeftSidebar()
const { events, fetchEvents, storeCalendar, updateCalendar, destroyCalendar } = useCalendar();
const { removeEvent } = useCalendarOld(event, isEventHandlerSidebarActive, isLeftSidebarOpen)
const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
  initialView: "dayGridMonth",
  selectable: true,
  headerToolbar: {
    start: 'drawerToggler,prev,next title',
    end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
  },

  dateClick(info) {
    event.value = { ...event.value, start: String(new Date(info.date)) }
    isEventHandlerSidebarActive.value = true
  },
  customButtons: {
    drawerToggler: {
      text: 'calendarDrawerToggler',
      click() {
        isLeftSidebarOpen.value = true
      },
    },
  },

  eventDrop: async ({ event }) => {
    await destroyCalendar(event.id);
    events.value = events.value.filter((e) => e.id !== event.id);
  },
  eventClick: async ({ event }) => {
    const formatDate = (date) => {
      const year = date.getFullYear();
      const month = `0${date.getMonth() + 1}`.slice(-2);
      const day = `0${date.getDate()}`.slice(-2);
      const hours = `0${date.getHours()}`.slice(-2);
      const minutes = `0${date.getMinutes()}`.slice(-2);
      const seconds = `0${date.getSeconds()}`.slice(-2);
      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    };

    await updateCalendar(event.id, {
      title: event.title,
      description: event.extendedProps.description,
      start: formatDate(new Date(event.start)),
      end: event.end ? formatDate(new Date(event.end)) : null,
    });
  },
  eventResize: async ({ event }) => {
    await updateCalendar(event.id, {
      start: event.start.toISOString(),
      end: event.end?.toISOString(),
    });
  },
  events: async (info, successCallback) => {
    try {
      await fetchEvents();
      successCallback(events.value);
    } catch (error) {
      console.error('Error occurred while fetching calendar events', error);
    }
  },
})

</script>


        <template>
  <div>

    <VCard>
      <!-- `z-index: 0` Allows overlapping vertical nav on calendar -->
      <VLayout style="z-index: 0;">
        <!-- 👉 Navigation drawer -->
        <VNavigationDrawer
          v-model="isLeftSidebarOpen"
          width="292"
          absolute
          touchless
          location="start"
          class="calendar-add-event-drawer"
          :temporary="$vuetify.display.mdAndDown"
        >
          <div style="margin: 1.4rem;">
            <VBtn
              block
              prepend-icon="tabler-plus"
              @click="isEventHandlerSidebarActive = true"
            >
              Yeni Ekle
            </VBtn>
          </div>

          <VDivider />


          <div class="d-flex align-center justify-center pa-2 mb-3">
            <AppDateTimePicker
              :model-value="new Date().toJSON().slice(0, 10)"
              label="Inline"
              :config="{ inline: true }"
              class="calendar-date-picker"
              @input="jumpToDate($event.target.value)"
            />
          </div>

          <VDivider />
          <div class="pa-7">
            <p class="text-sm text-uppercase text-disabled mb-3">
              FILTER
            </p>

            <div class="d-flex flex-column calendars-checkbox">
              <VCheckbox
                v-model="checkAll"
                label="View all"
              />
            </div>
          </div>
        </VNavigationDrawer>

        <VMain>
          <VCard flat>
            <FullCalendar
              :events="events"
              :options="calendarOptions"
            />
          </VCard>
        </VMain>
      </VLayout>
    </VCard>
    <CalendarEventHandler
      v-model:isDrawerOpen="isEventHandlerSidebarActive"
      :event="event"
      @add-event="addEvent"
      @update-event="updateEvent"
      @remove-event="removeEvent"
    />
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/libs/full-calendar";

.calendars-checkbox {
  .v-label {
    color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
    opacity: var(--v-high-emphasis-opacity);
  }
}

.calendar-add-event-drawer {
  &.v-navigation-drawer {
    border-end-start-radius: 0.375rem;
    border-start-start-radius: 0.375rem;
  }
}

.calendar-date-picker {
  display: none;

  +.flatpickr-input {
    +.flatpickr-calendar.inline {
      border: none;
      box-shadow: none;

      .flatpickr-months {
        border-block-end: none;
      }
    }
  }
}
</style>

<style lang="scss" scoped>
.v-layout {
  overflow: visible !important;

  .v-card {
    overflow: visible;
  }
}
</style>
