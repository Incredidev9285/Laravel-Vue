import axios from 'axios'

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    withCredentials: true
})

// Request interceptor
api.interceptors.request.use(
    config => {
        console.log('Making request to:', config.url)
        return config
    },
    error => {
        console.error('Request error:', error)
        return Promise.reject(error)
    }
)

// Response interceptor
api.interceptors.response.use(
    response => response,
    error => {
        console.error('Response error:', error)
        if (error.code === 'ERR_NETWORK') {
            console.error('Network error - check if Laravel server is running')
        }
        return Promise.reject(error)
    }
)

export default api
