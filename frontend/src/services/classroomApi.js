import axios from 'axios';

const classroomApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL +'/api/classroom',
}); 

export default classroomApi;