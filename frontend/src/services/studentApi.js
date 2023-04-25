import axios from 'axios'; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const studentApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/student',
}); 

// studentApi.interceptors.request.use(interceptorRequest);
// studentApi.interceptors.response.reject(interceptorReponse);

export default studentApi;