import axios from "axios"; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const directionApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + "/api/direction",
}); 

// directionApi.interceptors.request.use(interceptorRequest);
// directionApi.interceptors.response.reject(interceptorReponse);

export default directionApi;