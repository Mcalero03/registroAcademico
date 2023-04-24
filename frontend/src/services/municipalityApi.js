import axios from 'axios'; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const municipalityApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/municipality',
});
// municipalityApi.interceptors.request.use(interceptorRequest);
// municipalityApi.interceptors.response.reject(interceptorReponse);

export default municipalityApi;