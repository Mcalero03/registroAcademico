import axios from "axios"; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const groupApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + "/api/group",
}); 

// groupApi.interceptors.request.use(interceptorRequest);
// groupApi.interceptors.response.reject(interceptorReponse);

export default groupApi;