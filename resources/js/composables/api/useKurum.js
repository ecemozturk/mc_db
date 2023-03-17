import { ref } from 'vue';
import axios from 'axios';

export function useKurum() {
    const kurumlar = ref([]);

    async function fetchKurumlar() {
        try {
            const response = await axios.get('/api/kurumlar');
            kurumlar.value = response.data;
        } catch (error) {
            console.error('Error fetching kurumlar:', error);
        }
    }

    return {
        kurumlar,
        fetchKurumlar,
    };
}
