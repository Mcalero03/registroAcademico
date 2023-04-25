import axios from 'axios'; 

const inscriptionApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL +'/api/inscription',
});

export default inscriptionApi;