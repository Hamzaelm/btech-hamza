<template>
  <div class="login-container d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-form bg-white p-5 shadow rounded">
      <h2 class="text-center mb-4">BTECH-TODOLIST</h2>
      <form @submit.prevent="onSubmit">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input
            type="text"
            id="username"
            class="form-control"
            v-model="username"
            :class="{ 'is-invalid': usernameError }"
            placeholder="Username"
          />
          <div v-if="usernameError" class="invalid-feedback">Username est obligatoire.</div>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Mot de passe</label>
          <div class="input-group">
            <input
              :type="passwordVisible ? 'text' : 'password'"
              id="password"
              class="form-control"
              v-model="password"
              :class="{ 'is-invalid': passwordError }"
              placeholder="Mot de passe"
            />
            <button
              type="button"
              class="btn btn-outline-secondary"
              @click="togglePasswordVisibility"
            >
              <i :class="passwordVisible ? 'bi-eye-slash' : 'bi-eye'"></i>
            </button>
          </div>
          <div v-if="passwordError" class="invalid-feedback">Mot de passe est obligatoire.</div>
        </div>
        <div class="d-flex justify-content-center">
          <button
            type="submit"
            class="btn btn-primary w-100"
            :disabled="formIsInvalid || isLoading"
          >
            <span v-if="isLoading" class="spinner-border spinner-border-sm"></span>
            Login
          </button>
        </div>
      </form>
      <p class="text-center mt-3">
        Vous n'avez pas de compte?
        <a href="#" @click.prevent="showRegisterModal = true">Inscrivez-vous</a>
      </p>

      <div v-if="apiError" class="alert alert-danger mt-3">
        {{ apiError }}
      </div>
    </div>

    <!-- Register Modal -->
    <div v-if="showRegisterModal" class="modal-backdrop" @click.self="showRegisterModal = false">
      <div class="modal-content p-4">
        <h3 class="mb-3 text-center">Créer un compte</h3>
        <form @submit.prevent="onRegister">
          <div class="mb-3">
            <label for="register-username" class="form-label">Username</label>
            <input
              type="text"
              id="register-username"
              class="form-control"
              v-model="registerUsername"
              required
            />
          </div>
          <div class="mb-3">
            <label for="register-password" class="form-label">Mot de passe</label>
            <input
              type="password"
              id="register-password"
              class="form-control"
              v-model="registerPassword"
              required
            />
          </div>
          <div class="mb-3">
            <label for="register-confirm-password" class="form-label">Confirm Mot de passe</label>
            <input
              type="password"
              id="register-confirm-password"
              class="form-control"
              v-model="registerConfirmPassword"
              required
            />
            <div v-if="registerPasswordMismatch" class="text-danger small mt-1">
              Les mots de passe ne correspondent pas
            </div>
          </div>
          <button type="submit" class="btn btn-success w-100" :disabled="registerPasswordMismatch">
            Registre
          </button>
          <button
            type="button"
            class="btn btn-secondary w-100 mt-2"
            @click="showRegisterModal = false"
          >
            Annuler
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_URL } from '@/services/apis'
import router from '@/router'
import { userStore } from '@/stores/me'
import Swal from 'sweetalert2'
export default {
  setup() {
    // Login
    const username = ref('')
    const password = ref('')
    const passwordVisible = ref(false)
    const isLoading = ref(false)
    const apiError = ref(null)

    const usernameError = computed(() => !username.value)
    const passwordError = computed(() => !password.value)
    const formIsInvalid = computed(() => usernameError.value || passwordError.value)

    const togglePasswordVisibility = () => {
      passwordVisible.value = !passwordVisible.value
    }

    const onSubmit = async () => {
      if (formIsInvalid.value) return
      isLoading.value = true
      apiError.value = null

      try {
        const response = await axios.post(API_URL + 'login', {
          username: username.value,
          password: password.value,
        })
        if (response.data) {
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('isConnected', 1)
          userStore.fetchUser()
          router.push('dashboard')
        }
      } catch (error) {
        apiError.value = error.response
          ? error.response.data.message
          : 'An error occurred, please try again later'
      } finally {
        isLoading.value = false
      }
    }

    // Register
    const showRegisterModal = ref(false)
    const registerUsername = ref('')
    const registerPassword = ref('')
    const registerConfirmPassword = ref('')

    const registerPasswordMismatch = computed(() => {
      return (
        registerPassword.value &&
        registerConfirmPassword.value &&
        registerPassword.value !== registerConfirmPassword.value
      )
    })

    const onRegister = async () => {
      if (registerPasswordMismatch.value) return
      try {
        const response = await axios.post(API_URL + 'register', {
          username: registerUsername.value,
          password: registerPassword.value,
        })
        if (response.data) {
          Swal.fire('Bienvenue !')
          showRegisterModal.value = false
        }
      } catch (error) {
        alert(
          error.response ? error.response.data.message : 'Registration failed, please try again.',
        )
      }
    }

    return {
      username,
      password,
      passwordVisible,
      togglePasswordVisibility,
      usernameError,
      passwordError,
      formIsInvalid,
      isLoading,
      apiError,
      onSubmit,
      showRegisterModal,
      registerUsername,
      registerPassword,
      registerConfirmPassword,
      registerPasswordMismatch,
      onRegister,
    }
  },
}
</script>

<style scoped>
/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
}
</style>
