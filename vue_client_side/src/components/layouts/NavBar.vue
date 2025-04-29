<template>
  <nav class="navbar">
    <div class="logo">BTECH-TODOLIST</div>
    <div class="user-info">
      <i class="fas fa-user"></i>
      <span>{{ userData?.username }}</span>
      <i
        class="fas fa-pen text-danger"
        @click="openEditPasswordModal()"
        title="Modifier Mot de passe"
      ></i>
    </div>
    <button class="logout-btn" @click="logout()">Logout</button>
  </nav>
  <div v-if="showEditPasswordModal" class="modal-backdrop">
    <div class="modal-content">
      <h2>Modifier Mot de passe</h2>
      <input
        v-model="editPassword.currentPassword"
        type="password"
        placeholder="Mot de passe actuel"
      />
      <input
        v-model="editPassword.newPassword"
        type="password"
        placeholder="Nouveau Mot de passe"
      />

      <div class="modal-buttons">
        <button class="btn btn-success" @click="saveNewPassword">Modifier</button>
        <button class="btn btn-secondary" @click="closeEditPasswordModal">Annuler</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { userStore } from '@/stores/me'
import axios from 'axios'
import Swal from 'sweetalert2'
import { API_URL } from '@/services/apis'

const userData = JSON.parse(localStorage.getItem('user'))
const router = useRouter()
const showEditPasswordModal = ref(false)
const editPassword = ref({
  currentPassword: '',
  newPassword: '',
})

function openEditPasswordModal() {
  showEditPasswordModal.value = true
}
function closeEditPasswordModal() {
  showEditPasswordModal.value = false
}
async function saveNewPassword() {
  const result = await Swal.fire({
    title: 'Es-tu sûr ?',
    text: 'Cette action est irréversible !',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Oui !',
    cancelButtonText: 'Annuler',
  })

  if (result.isConfirmed) {
    try {
      const token = localStorage.getItem('token')
      await axios.patch(
        `${API_URL}user/update-password`,
        {
          currentPassword: editPassword.value.currentPassword,
          newPassword: editPassword.value.newPassword,
        },
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      )
      Swal.fire('Modifier!', 'Mot de passe modifier', 'success')
    } catch (error) {
      console.error('Erreur', error)
      Swal.fire('Erreur', 'Impossible de modifier mot de passe.', 'error')
    }
  }
}
function logout() {
  localStorage.removeItem('isConnected')
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  userStore.clearUser()
  router.push('/')
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background-color: #000;
  color: #ffd700;
  display: flex;
  flex-direction: column;
}

.logout-btn:hover {
  background-color: #ffd700;
  color: #000;
  cursor: pointer;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #111;
  padding: 1rem 2rem;
  box-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
}

.logo {
  font-size: 1.8rem;
  font-weight: bold;
  color: #ffd700;
}

.logout-btn {
  background-color: transparent;
  border: 2px solid #ffd700;
  color: #ffd700;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: bold;
  transition: all 0.3s ease;
}
.user-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #ffd700;
  font-weight: bold;
  font-size: 1.1rem;
}
.user-info i {
  font-size: 1.2rem;
}
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(2px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-content {
  background: #111;
  border: 2px solid #ffd700;
  padding: 2rem;
  border-radius: 10px;
  color: #ffd700;
  min-width: 300px;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.modal-content h2 {
  margin-top: 0;
}

.modal-content input,
.modal-content textarea {
  width: 100%;
  padding: 0.5rem;
  background: #222;
  border: 1px solid #ffd70050;
  color: #ffd700;
  border-radius: 5px;
}

.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>
