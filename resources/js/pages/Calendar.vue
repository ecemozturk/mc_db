<template>
  <div>
    <FullCalendar :options="calendarOptions" :events="events" />
  </div>
</template>

<script>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timeGridPlugin from "@fullcalendar/timegrid";
import useCalendar from "@/composables/useCalendar";
import { ref, watchEffect } from "vue";

export default {
  components: {
    FullCalendar,
  },

  setup() {
    const { events, fetchEvents, storeCalendar, updateCalendar, destroyCalendar } =
      useCalendar();

    const calendarOptions = {
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      initialView: "dayGridMonth",
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
      },
      selectable: true,
      select: async ({ start, end }) => {
        const newEvent = await storeCalendar({
          title: "",
          description: "",
          start: start.toISOString(),
          end: end.toISOString(),
        });
        events.value.push(newEvent);
      },
      eventDrop: async ({ event }) => {
        await destroyCalendar(event.id);
        events.value = events.value.filter((e) => e.id !== event.id);
      },
      eventClick: async ({ event }) => {
        console.log('event clicked')
        const formatDate = (date) => {
          const year = date.getFullYear();
          const month = `0${date.getMonth() + 1}`.slice(-2);
          const day = `0${date.getDate()}`.slice(-2);
          const hours = `0${date.getHours()}`.slice(-2);
          const minutes = `0${date.getMinutes()}`.slice(-2);
          const seconds = `0${date.getSeconds()}`.slice(-2);
          return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        };

        await updateEvent(event.id, {
          title: event.title,
          description: event.extendedProps.description,
          start: formatDate(new Date(event.start)),
          end: event.end ? formatDate(new Date(event.end)) : null,
        });
      },
      eventResize: async ({ event }) => {
        await updateEvent(event.id, {
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
    };

    watchEffect(() => {
      console.log("events", events.value);
    });

  },
};
</script>
