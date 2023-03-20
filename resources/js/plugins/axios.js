// axiosConfig.js
import axios from 'axios';

const instance = axios.create({
    baseURL: 'http://localhost', // Update this with your own base URL
});

instance.interceptors.request.use(
    (config) => {
        const accessToken = localStorage.getItem('accessToken');
        const tokenType = localStorage.getItem('tokenType');

        if (accessToken && tokenType) {
            config.headers.Authorization = `${tokenType} ${accessToken}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default instance;
