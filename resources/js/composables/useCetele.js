import { ref } from 'vue';
import axios from 'axios';

const ceteleList = ref([]);

const getCeteleList = async () => {
    try {
        const response = await axios.get('/api/ceteles');
        ceteleList.value = response.data.data;
    } catch (error) {
        console.log(error.response.data);
    }
};



const getCeteleById = async (id) => {
    try {
        const response = await axios.get(`/api/ceteles/${id}`);
        return response.data.data;
    } catch (error) {
        console.log(error.response.data);
    }
};

const createCetele = async (data) => {
    try {
        await axios.post('/api/ceteles', data);
    } catch (error) {
        console.log('Error:', error.response.data);
        return error.response.data;
    }
};

const updateCetele = async (id, data) => {
    try {
        await axios.put(`/api/ceteles/${id}`, data);
    } catch (error) {
        console.log(error.response.data);
    }
};

const deleteCetele = async (id) => {
    try {
        await axios.delete(`/api/ceteles/${id}`);
        ceteleList.value = ceteleList.value.filter((cetele) => cetele.id !== id);
    } catch (error) {
        console.log(error.response.data);
    }
};

export default {
    ceteleList,
    getCeteleList,
    getCeteleById,
    createCetele,
    updateCetele,
    deleteCetele,
};
