import axios from 'axios'; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const scheduleApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/schedule',
}) 
// scheduleApi.interceptors.request.use(interceptorRequest);
// scheduleApi.interceptors.response.reject(interceptorReponse);

export default scheduleApi;