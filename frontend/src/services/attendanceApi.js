import axios from 'axios';

const attendanceApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL +'/api/attendance',
}); 

export default attendanceApi;