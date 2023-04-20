import axios from "axios"; 
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const relativeApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + "/api/relative",
});

// relativeApi.interceptors.request.use(interceptorRequest);
// relativeApi.interceptors.response.reject(interceptorReponse);

export default relativeApi;