import { ref } from 'vue';
import axios from 'axios';

export function useCountry() {
    const countries = ref([]);

    async function fetchCountries() {
        try {
            const response = await axios.get('/api/countries');
            countries.value = response.data;
        } catch (error) {
            console.error("Error fetching countries:", error);
        }
    }

    return { countries, fetchCountries };
}
