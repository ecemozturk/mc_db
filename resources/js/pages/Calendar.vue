<template>
  　<div>
  <div v-for="event in events">
    {{ event.title }}
  </div>

  <div>
    <FullCalendar :options="calendarOptions" :events="events" />
  </div>
</div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import useCalendar from '@/composables/useCalendar'
import { ref, watchEffect } from 'vue'


export default {
  components: {
    FullCalendar
  },

  setup() {
    const { events, fetchEvents, storeCalendar, updateCalendar, destroyCalendar } = useCalendar()

    const calendarOptions = {
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      selectable: true,
      select: ({ start, end }) => {
        storeCalendar({
          title: '',
          description: '',
          start: start.toISOString(),
          end: end.toISOString()
        })
      },
      eventClick: ({ event }) => {
        destroyCalendar(event.id)
      },
      eventDrop: ({ event }) => {
        updateCalendar(event.id, {
          start: event.start.toISOString(),
          end: event.end?.toISOString()
        })
      },
      eventResize: ({ event }) => {
        updateCalendar(event.id, {
          start: event.start.toISOString(),
          end: event.end?.toISOString()
        })
      }
    }

    fetchEvents()

    console.log('events', events.value)

    watchEffect(() => {
      console.log('evensts', events.value)
    })
    return {
      calendarOptions,
      events
    }
  }
}
</script>
