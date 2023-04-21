import axios from 'axios'; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const cycleApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/cycle',
}); 

// cycleApi.interceptors.request.use(interceptorRequest);
// cycleApi.interceptors.response.reject(interceptorReponse);

export default cycleApi;