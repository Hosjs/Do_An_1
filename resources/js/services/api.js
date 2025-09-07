import axios from 'axios'

axios.defaults.baseURL = 'http://127.0.0.1:8000'
//axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

export default axios
