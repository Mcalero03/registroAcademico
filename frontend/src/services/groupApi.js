import axios from 'axios';
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const groupApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/group',
}); 

// directionApi.interceptors.request.use(interceptorRequest);
// directionApi.interceptors.response.reject(interceptorReponse);

export default groupApi;