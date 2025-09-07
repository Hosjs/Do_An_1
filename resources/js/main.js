import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import '@fortawesome/fontawesome-free/css/all.min.css'

import MathJax, { initMathJax } from 'mathjax-vue3'

initMathJax({
  tex: {
    inlineMath: [['$', '$'], ['\\(', '\\)']],
    displayMath: [['$$', '$$'], ['\\[', '\\]']]
  },
  svg: {
    fontCache: 'global'
  }
})

const app = createApp(App)
app.use(router)
app.use(store)
app.use(MathJax)
app.mount('#app')
