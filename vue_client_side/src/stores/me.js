import { reactive } from 'vue'
import axios from 'axios'
import { API_URL } from '@/services/apis'
export const userStore = reactive({
  user: null,
  async fetchUser() {
    try {
      const token = localStorage.getItem('token')
      if (token) {
        const response = await axios.get(API_URL + 'user', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        this.user = response.data
        localStorage.setItem('user', JSON.stringify(this.user))
        console.log(this.user)
      }
    } catch (error) {
      console.error('Error fetching user:', error)
      this.user = null
    }
  },
  clearUser() {
    this.user = null
  },
})
