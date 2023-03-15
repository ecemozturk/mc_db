import { ref } from 'vue'
import axios from 'axios'


const events = ref([])

const fetchEvents = async () => {
    const response = await axios.get('/api/events')
    events.value = response.data.data
}

const storeCalendar = async (event) => {
    const response = await axios.post('/api/events', event)
    const newEvent = response.data.data
    events.value.push(newEvent)
    return newEvent
}

const updateEvent = async (id, event) => {
    const response = await axios.put(`/api/events/${id}`, event)
    const updatedEvent = response.data.data
    events.value = events.value.map((e) => (e.id === id ? updatedEvent : e))
    return updatedEvent
}

const destroyCalendar = async (id) => {
    await axios.delete(`/api/events/${id}`)
    events.value = events.value.filter((e) => e.id !== id)
}

export default () => ({
    events,
    fetchEvents,
    storeCalendar,
    updateEvent,
    destroyCalendar
})
