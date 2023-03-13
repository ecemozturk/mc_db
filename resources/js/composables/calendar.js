import {ref} from 'vue'
import axios from "axios"
import {useRouter} from 'vue-router'

export default function useCalendar() {
    const allEvents = ref([])
    const events = ref([])
    const router = useRouter()
    const errors = ref('')

    const getCalendars = async () => {
        let response = await axios.get('/api/calendars')
        allEvents.value = response.data.data
    }

    const getCalendar = async id => {
        let response = await axios.get('/api/calendars/' + id)
        events.value = response.data.data
    }

    const storeCalendar = async data => {
        errors.value = ''
        try {
            await axios.post('/api/calendars', data)
            await router.push({name: 'events.index'})
        } catch (e) {
            if (e.response.status === 422) {
                errors.value = e.response.data.errors
            }
        }
    }

    const updateCalendar = async id => {
        errors.value = ''
        try {
            await axios.patch(`/api/calendars/${id}`, events.value)
            await router.push({name: 'events.index'})
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
        }
    }

    const destroyCalendar = async id => {
        await axios.delete('/api/calendars/' + id)
    }

    return {
        allEvents,
        events,
        errors,
        getCalendars,
        getCalendar,
        storeCalendar,
        updateCalendar,
        destroyCalendar,
    }
}
