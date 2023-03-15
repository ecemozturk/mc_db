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

const extractEventDataFromEventApi = eventApi => {
  const { id, title, start, end, url, extendedProps: { description } } = eventApi

  return {
    id,
    title,
    start,
    end,
    url,
    extendedProps: {
      description,
    },
  }
}


const { isLeftSidebarOpen } = useResponsiveLeftSidebar()
const { events, fetchEvents, updateEvent, destroyCalendar } = useCalendar();
const { refCalendar,  addEvent, jumpToDate} = useCalendarOld(event, isEventHandlerSidebarActive, isLeftSidebarOpen)

const { removeEvent } = useCalendarOld(event, isEventHandlerSidebarActive, isLeftSidebarOpen)

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
  initialView: "dayGridMonth",
  selectable: true,
  headerToolbar: {
    start: 'drawerToggler,prev,next title',
    end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
  },



  eventClick({ event: clickedEvent }) {
    const extractEventDataFromEventApi = (event) => {
      return {
        id: event.id,
        title: event.title,
        description: event.extendedProps.description,
        start: event.start,
        end: event.end,
      };
    };

    // * Only grab required fields otherwise it goes in an infinite loop
    // ! Always grab all fields rendered by the form (even if they are `undefined`) otherwise due to Vue3/Composition API you might get: "object is not extensible"
    const eventData = extractEventDataFromEventApi(clickedEvent);
    event.value = eventData;
    isEventHandlerSidebarActive.value = true;
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

  async updateEvent(eventId, eventData) {
    const formatDate = (date) => {
      const year = date.getFullYear();
      const month = `0${date.getMonth() + 1}`.slice(-2);
      const day = `0${date.getDate()}`.slice(-2);
      const hours = `0${date.getHours()}`.slice(-2);
      const minutes = `0${date.getMinutes()}`.slice(-2);
      const seconds = `0${date.getSeconds()}`.slice(-2);
      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    };

    try {
      const updatedEventData = {
        title: eventData.title,
        description: eventData.description,
        start: formatDate(new Date(eventData.start)),
        end: eventData.end ? formatDate(new Date(eventData.end)) : null,
      };
      await updateEvent(eventId, updatedEventData);
      isEventHandlerSidebarActive.value = true;
    } catch (error) {
      console.error(error);
    }
  },

  eventDrop: async ({ event }) => {
    try {
      await destroyCalendar(event.id);
      events.value = events.value.filter((e) => e.id !== event.id);
    } catch (error) {
      console.error(error);
    }
  },

  eventResize: async ({ event }) => {
    try {
      await updateEvent(event.id, {
        start: event.start.toISOString(),
        end: event.end?.toISOString(),
      });
    } catch (error) {
      console.error(error);
    }
  },
  events: async (info) => {
    try {
      await fetchEvents();
      return events.value;
    } catch (error) {
      console.error('Error occurred while fetching calendar events', error);
      return [];
    }
  },
});

// 👉 Event Handler

// 👉 Calendar component
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
