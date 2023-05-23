import axios from 'axios'; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const subSchoolApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/subSchool',
}); 

// subSchoolApi.interceptors.request.use(interceptorRequest);
// subSchoolApi.interceptors.response.reject(interceptorReponse);

export default subSchoolApi;