import axios from 'axios';

const pensumSubjectDetailApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/pensumSubjectDetail',
}); 

export default pensumSubjectDetailApi;