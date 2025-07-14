<template>
  <div class="hello-world">
    <h1>{{ message }}</h1>
    <button @click="fetchFromAPI">Get Message from Laravel API</button>
    <p v-if="apiMessage">{{ apiMessage }}</p>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'HelloWorld',
  data() {
    return {
      message: 'Hello World from Vue!',
      apiMessage: ''
    }
  },
  methods: {
    async fetchFromAPI() {
      try {
        const response = await axios.get('http://localhost:8000/api/hello')
        this.apiMessage = response.data.message
      } catch (error) {
        console.error('Error fetching from API:', error)
        this.apiMessage = 'Error connecting to API'
      }
    }
  }
}
</script>

<style scoped>
.hello-world {
  text-align: center;
  margin-top: 60px;
}

h1 {
  color: #2c3e50;
}

button {
  background-color: #42b983;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin: 20px 0;
}

button:hover {
  background-color: #369870;
}
</style>
