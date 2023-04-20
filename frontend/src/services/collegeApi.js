import axios from 'axios'; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const collegeApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/college',
}); 

// collegeApi.interceptors.request.use(interceptorRequest);
// collegeApi.interceptors.response.reject(interceptorReponse);

export default collegeApi;