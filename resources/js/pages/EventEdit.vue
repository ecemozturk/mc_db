<template>
  <div>
    <h1>Edit Event</h1>
    <form @submit.prevent="submitForm">
      <div>
        <label for="title">Title:</label>
        <input type="text" id="title" v-model="event.title">
      </div>
      <div>
        <label for="description">Description:</label>
        <input type="text" id="description" v-model="event.description">
      </div>
      <div>
        <label for="start">Start Date:</label>
        <AppDateTimePicker
          :key="JSON.stringify(startDateTimePickerConfig)"
          v-model="event.start"
          :rules="[requiredValidator]"
          label="Start date"
          :config="startDateTimePickerConfig"
        />
      </div>

      <div>
        <label for="end">End Date:</label>
        <AppDateTimePicker
          :key="JSON.stringify(endDateTimePickerConfig)"
          v-model="event.end"
          :rules="[requiredValidator]"
          label="End date"
          :config="endDateTimePickerConfig"
        />
      </div>

      <button>Update Event</button>
    </form>
  </div>
</template>u

<script>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useCalendar from '@/composables/useCalendar'

export default {
  name: 'EventEdit',
  setup() {
    const router = useRouter()

    const requiredValidator = (value) => !!value || 'Required.'
    const startDateTimePickerConfig = computed(() => {
      const config = {
        enableTime: true,
        dateFormat: 'Y-m-d H:i:00',
      }

      if (event.value.end)
        config.maxDate = event.value.end

      return config
    })

    const endDateTimePickerConfig = computed(() => {
      const config = {
        enableTime: true,
        dateFormat: 'Y-m-d H:i:00',
      }

      if (event.value.start)
        config.minDate = event.value.start

      return config
    })

    const { updateEvent, fetchEvents } = useCalendar()
    const route = useRoute()
    const eventId = route.params.id
    const event = ref({ title: '', description: '', start: '', end: '' })

    const fetchEvent = async () => {
      const events = await fetchEvents()
      if (events && events.length > 0) {
        const eventToUpdate = events.find((e) => e.id === eventId)
        if (eventToUpdate) {
          event.value = eventToUpdate
        }
      }
    }


    const submitForm = async () => {
      await updateEvent(eventId, event.value)
      router.push({ name: 'event-edit' })
    }

    fetchEvent()

    return {
      event,
      endDateTimePickerConfig,
      startDateTimePickerConfig,
      requiredValidator,
      submitForm
    }
  }
}
</script>
