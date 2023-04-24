import axios from 'axios';
// import { interceptorRequest, interceptorReponse } from "./interceptor";

const evaluationApi = axios.create({ baseURL: import.meta.env.VITE_BACKEND_URL + '/api/evaluation',}); 

// evaluationApi.interceptors.request.use(interceptorRequest);
// evaluationApi.interceptors.response.reject(interceptorReponse);

export default evaluationApi;