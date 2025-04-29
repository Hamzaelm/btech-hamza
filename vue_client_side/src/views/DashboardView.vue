<template>
  <div class="dashboard-container">
    <main class="main-content">
      <h1>Bienvenue sur votre tableau de bord 🎯</h1>
      <button class="btn btn-sm btn-success me-2" @click="openAddModal()">Ajouter une tâche</button>
      <BasicTable :data="data" :columns="columns">
        <template #actions="{ row }">
          <button
            @click="isCompleted(row.id)"
            class="btn btn-sm btn-light me-2"
            title="Terminer la tâche"
          >
            ✅
          </button>
          <button
            @click="openEditModal(row)"
            class="btn btn-sm btn-warning me-2"
            title="Modifier la tâche"
          >
            ✏️
          </button>
          <button
            @click="confirmDelete(row.id)"
            class="btn btn-sm btn-danger me-2"
            title="Supprimer la tâche"
          >
            🗑️
          </button>
        </template>
      </BasicTable>
      <div v-if="showAddModal" class="modal-backdrop">
        <div class="modal-content">
          <h2>Ajouter une Tâche</h2>
          <input v-model="task.title" type="text" placeholder="Titre" />
          <textarea v-model="task.description" placeholder="Description"></textarea>

          <div class="modal-buttons">
            <button class="btn btn-success" @click="saveNewTask">Enregistrer</button>
            <button class="btn btn-secondary" @click="closeEditModal">Annuler</button>
          </div>
        </div>
      </div>
      <div v-if="showEditModal" class="modal-backdrop">
        <div class="modal-content">
          <h2>Modifier la Tâche</h2>
          <input v-model="editingTask.title" type="text" placeholder="Titre" />
          <textarea v-model="editingTask.description" placeholder="Description"></textarea>

          <div class="modal-buttons">
            <button class="btn btn-success" @click="saveTask">Enregistrer</button>
            <button class="btn btn-secondary" @click="closeEditModal">Annuler</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import BasicTable from '@/components/utils/BasicTable.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { API_URL } from '@/services/apis'

const columns = [
  { key: 'id', label: '#' },
  { key: 'title', label: 'Tâche' },
  { key: 'description', label: 'Déscription' },
  { key: 'completed', label: 'Etat' },
  { key: 'actions', label: 'Actions' },
]
const data = ref([])

const task = ref({
  title: '',
  description: '',
})
const showAddModal = ref(false)
const editingTask = ref(null)
const showEditModal = ref(false)

async function fetchTasks() {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(API_URL + 'todos', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    data.value = response.data
    console.log(response.data)
  } catch (error) {
    console.error('Erreur lors de la récupération des tâches', error)
  }
}
onMounted(() => {
  fetchTasks()
})

//modals
function openAddModal() {
  showAddModal.value = true
}
function openEditModal(task) {
  editingTask.value = { ...task } // clone to edit
  showEditModal.value = true
}
function closeEditModal() {
  showEditModal.value = false
  showAddModal.value = false
}
async function isCompleted(taskId) {
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
      // Correct placement of headers as the second argument to axios.patch
      await axios.patch(
        `${API_URL}todos/${taskId}`,
        {},
        {
          headers: { Authorization: `Bearer ${token}` },
        },
      )
      Swal.fire('Términer!', 'La tâche est terminée', 'success')
      fetchTasks()
    } catch (error) {
      console.error('Erreur', error)
      Swal.fire('Erreur', 'Impossible de compléter.', 'error')
    }
  }
}
async function confirmDelete(taskId) {
  const result = await Swal.fire({
    title: 'Es-tu sûr ?',
    text: 'Cette action est irréversible !',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Oui, supprimer !',
    cancelButtonText: 'Annuler',
  })

  if (result.isConfirmed) {
    try {
      const token = localStorage.getItem('token')
      await axios.delete(`${API_URL}todos/${taskId}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      Swal.fire('Supprimé!', 'La tâche a été supprimée.', 'success')
      fetchTasks()
    } catch (error) {
      console.error('Erreur lors de la suppression', error)
      Swal.fire('Erreur', 'Impossible de supprimer.', 'error')
    }
  }
}
async function saveTask() {
  try {
    const token = localStorage.getItem('token')
    await axios.put(
      `${API_URL}todos/${editingTask.value.id}`,
      {
        title: editingTask.value.title,
        description: editingTask.value.description,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    )
    Swal.fire('Modifié!', 'La tâche a été mise à jour.', 'success')
    showEditModal.value = false
    fetchTasks()
  } catch (error) {
    console.error('Erreur lors de la modification', error)
    Swal.fire('Erreur', 'Impossible de modifier.', 'error')
  }
}
async function saveNewTask() {
  try {
    const token = localStorage.getItem('token')
    await axios.post(
      `${API_URL}todos`,
      {
        title: task.value.title,
        description: task.value.description,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    )
    Swal.fire('Ajouter!', 'La tâche a été ajouter.', 'success')
    showAddModal.value = false
    clearInputs()
    fetchTasks()
  } catch (error) {
    console.error('Erreur lors de la modification', error)
    Swal.fire('Erreur', 'Impossible de modifier.', 'error')
  }
}
function clearInputs() {
  task.value.title = ''
  task.value.description = ''
}
</script>

<style scoped>
@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }

  .main-content h1 {
    font-size: 2rem;
  }

  .modal-content {
    width: 90%;
    max-width: 350px;
    padding: 1rem;
  }

  .modal-buttons {
    flex-direction: column;
    gap: 0.5rem;
  }
}

@media (max-width: 480px) {
  .main-content h1 {
    font-size: 1.5rem;
  }

  .modal-content {
    width: 95%;
  }

  .modal-content input,
  .modal-content textarea {
    font-size: 0.9rem;
  }
}
.dashboard-container {
  min-height: 100vh;
  background-color: #000;
  /* Black */
  color: #ffd700;
  /* Gold */
  display: flex;
  flex-direction: column;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #111;
  padding: 1rem 2rem;
  box-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
  /* gold glow */
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 2rem;
}

.main-content h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.main-content p {
  font-size: 1.2rem;
  opacity: 0.8;
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
