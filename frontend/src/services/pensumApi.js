import axios from "axios"; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const pensumApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/pensum',
}) 

// pensumApi.interceptors.request.use(interceptorRequest);
// pensumApi.interceptors.response.reject(interceptorReponse);

export default pensumApi;