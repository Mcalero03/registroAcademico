import axios from 'axios';
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const pensumTypeApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + "/api/pensumType",
}); 

// directionApi.interceptors.request.use(interceptorRequest);
// directionApi.interceptors.response.reject(interceptorReponse);

export default pensumTypeApi;