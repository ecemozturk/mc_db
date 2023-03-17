import { ref } from 'vue';
import axios from 'axios';

export function useCities() {
    const cities = ref([]);

    async function fetchCities() {
        try {
            const response = await axios.get('/api/cities');
            cities.value = response.data;
        } catch (error) {
            console.error('Error fetching cities:', error);
        }
    }

    return {
        cities,
        fetchCities,
    };
}
