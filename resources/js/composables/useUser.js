import { ref } from 'vue';
import axios from 'axios';

const userList = ref([]);

const getUserList = async () => {
    try {
        const response = await axios.get('/api/users'); // Update the API endpoint as required
        console.log('API Response:', response.data);
        userList.value = response.data.data; // Access the 'data' property from the response
    } catch (error) {
        console.error('Error fetching user data:', error);
    }
};

export default {
    userList,
    getUserList,
};
