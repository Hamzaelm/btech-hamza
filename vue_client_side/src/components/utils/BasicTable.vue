<template>
  <div class="table-wrapper">
    <input v-model="searchQuery" type="text" placeholder="Recherche..." class="form-control mb-3" />

    <div class="row mb-3">
      <div class="col-md-6 col-sm-12">
        <label for="filterCompleted" class="form-label">Filtrer par état de la tâche</label>
        <select v-model="completedFilter" class="form-control" id="filterCompleted">
          <option value="">Tous</option>
          <option value="1">Tâches terminées</option>
          <option value="0">Tâches non terminées</option>
        </select>
      </div>

      <div class="col-md-6 col-sm-12">
        <label for="rowsPerPage" class="form-label">Afficher par page</label>
        <select v-model="rowsPerPage" class="form-control" id="rowsPerPage">
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="150">150</option>
          <option value="200">200</option>
        </select>
      </div>
    </div>

    <table class="custom-table">
      <thead>
        <tr>
          <th v-for="col in columns" :key="col.key">
            {{ col.label }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, index) in paginatedData" :key="index">
          <td v-for="col in columns" :key="col.key">
            <slot v-if="col.key === 'actions'" name="actions" :row="row" />
            <template v-if="col.key === 'completed'">
              {{ row[col.key] ? 'Tâche terminée' : 'Tâche non complétée' }}
            </template>

            <template v-else>
              {{ row[col.key] }}
            </template>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
      <button
        class="btn btn-outline-warning mx-1"
        :disabled="currentPage === 1"
        @click="currentPage--"
      >
        Previous
      </button>
      <span class="mx-2" style="line-height: 2.4">
        Page {{ currentPage }} of {{ totalPages }}
      </span>
      <button
        class="btn btn-outline-warning mx-1"
        :disabled="currentPage === totalPages"
        @click="currentPage++"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  data: {
    type: Array,
    required: true,
  },
  columns: {
    type: Array,
    required: true,
  },
})

const searchQuery = ref('')
const currentPage = ref(1)
const rowsPerPage = ref(10)
const completedFilter = ref('')

const filteredData = computed(() => {
  let filtered = props.data

  if (searchQuery.value) {
    filtered = filtered.filter((row) =>
      Object.values(row).some((val) =>
        String(val).toLowerCase().includes(searchQuery.value.toLowerCase()),
      ),
    )
  }

  if (completedFilter.value !== '') {
    filtered = filtered.filter((row) => row.completed == completedFilter.value)
  }

  return filtered
})

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage.value
  const end = start + rowsPerPage.value
  return filteredData.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / rowsPerPage.value))
</script>

<style scoped>
.table-wrapper {
  width: 100%;
  overflow-x: auto;
  margin-top: 2rem;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #111;
  color: #ffd700;
}

.custom-table th,
.custom-table td {
  padding: 0.75rem 1rem;
  border: 1px solid #ffd70050;
  text-align: left;
}

.custom-table th {
  background-color: #222;
}

.custom-table tbody tr:hover {
  background-color: #333;
}
</style>
