import axios from 'axios';
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const teacherApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/teacher',
}); 

// teacherApi.interceptors.request.use(interceptorRequest);
// teacherApi.interceptors.response.reject(interceptorReponse);

export default teacherApi;