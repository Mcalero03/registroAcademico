import axios from 'axios'; 

const teacherSubjectDetailApi = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL + '/api/teacherSubjectDetail',
}); 

export default teacherSubjectDetailApi;