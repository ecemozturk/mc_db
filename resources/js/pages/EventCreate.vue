<template>
  <div>
    <h1>Create New Event</h1>
    <form @submit.prevent="createEvent">
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
          label="End date"
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
      <button>Create Event</button>
    </form>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import useCalendar from '@/composables/useCalendar'

export default {

  setup() {
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

    const { storeCalendar } = useCalendar()
    const event = ref({ title: '', description: '', start: '', end: '' })

    const createEvent = async () => {
      await storeCalendar(event.value)
      event.value = { title: '', description:'', start: '', end: '' }
    }

    return {
      event,
      createEvent,
      endDateTimePickerConfig,
      startDateTimePickerConfig,
      requiredValidator
    }
  }
}
</script>
