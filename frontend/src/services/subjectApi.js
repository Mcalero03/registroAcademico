import axios from 'axios';
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const subjectApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/subject',
}) 

// subjectApi.interceptors.request.use(interceptorRequest);
// subjectApi.interceptors.response.reject(interceptorReponse);

export default subjectApi;