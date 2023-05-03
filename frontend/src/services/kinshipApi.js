import axios from 'axios'; 

const kinshipApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/kinship',
}); 

export default kinshipApi;