<script setup>
import {Link, router} from '@inertiajs/vue3'
import {ref} from "vue"
import DeleteItemModal from "@/Components/DeleteItemModal.vue"
import axios from "axios"
import {showToast} from "@/useToast.js"
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"
import TableFilter from "@/Components/TableFilter.vue"

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  hasActions: {
    type: Boolean,
    default: false
  },
  editActionLink: {
    type: String,
  },
  deleteActionLink: {
    type: String,
  },
  entityName: {
    type: String,
    required: true
  },
  statusText: {
    type: Array,
    default: ['Active', 'Inactive']
  },
  redirectLink: {
    type: String,
    required: true
  },
  column: {
    type: String
  },
  ascending: {
    type: Boolean
  },
  filterFormat: {
    type: Array,
    required: true
  },
  filterParams: {
    type: [Array, Object],
  }
})
const columns = props.data.length ? Object.keys(props.data[0]).filter(key => key !== 'id') : []
const columnNames = props.filterFormat.map((e)=>e.name)

const deleteError = ref(null)
const deletedItem = ref(null)
const deleteItem = async (item) => {
  try {
    await axios.delete(route(props.deleteActionLink, item))
    deletedItem.value = null
    showToast(`${props.entityName.toProperCase()} was successfully deleted`)
    router.reload()
  } catch (err) {
    showToast(err.response.data.message, 'error')
    deleteError.value = err.response.data.message
  }
}

const formatColumnName = (fieldName) => {
  const words = fieldName.split('_')
  const formattedWords = words.map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
  return formattedWords.join(' ')
}
const sortByColumn = (column) => {
  router.get('', {
      column,
      ascending: !props.ascending
  })
}
</script>

<template>
  <TableFilter
    :filter-params="filterParams"
    :status-text="statusText"
    :filter-format="filterFormat"
  />
  <div v-if="data.length !== 0" class="border rounded-lg overflow-hidden dark:border-gray-700">
    <table class="w-full text-sm text-left text-gray-300 rounded-lg table-fixed	">
      <thead class="bg-gray-700 text-gray-300">
      <tr dusk="sorting">
        <th v-for="(column, index) in columns" class="px-6 py-4 cursor-pointer" scope="col" @click="sortByColumn(column)" :dusk="column" >
          {{ formatColumnName(columnNames[index]) }}
          <span class="" v-if="props.column === column">
            <font-awesome-icon v-if="ascending" icon="fa-solid fa-sort-up" class="align-bottom" dusk="up_icon"/>
            <font-awesome-icon v-if="!ascending" icon="fa-solid fa-sort-down" class="align-text-top" dusk="down_icon"/>
          </span>
        </th>
        <th v-if="hasActions" class="px-6 py-4" scope="col">
          Actions
        </th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="item in data" :key="item.id"
          class="border-b bg-gray-800 border-gray-700 hover:bg-gray-600">
        <th v-for="column in columns" class="px-6 py-4 font-medium whitespace-nowrap text-white"
            scope="row">
          {{
            column === 'status' ?
              item[column] ? statusText[0] : statusText[1]
              : item[column]
          }}
        </th>
        <th v-if="hasActions" class="px-0 py-4 font-medium whitespace-nowrap text-white" scope="row">
          <Link :href="route(editActionLink, item.id)" class="mx-6" title="Edit" dusk="edit">
            <font-awesome-icon class="text-blue-400" icon="fa-solid fa-pencil"/>
          </Link>
          <button title="Delete" @click="deletedItem = item.id" dusk="delete">
            <font-awesome-icon class="text-red-600" icon="fa-solid fa-trash-can"/>
          </button>
        </th>
      </tr>
      </tbody>
    </table>

  </div>
  <p v-else class="">No {{ entityName }}s yet.</p>

  <DeleteItemModal
    :close="()=>{deletedItem = null; deleteError=null}"
    :delete-item="deleteItem"
    :entity-name="entityName"
    :error="deleteError"
    :item="deletedItem"
    :show="deletedItem !== null"
  />

</template>
