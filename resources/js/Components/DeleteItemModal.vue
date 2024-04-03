<script setup>
import Modal from "@/Components/Modal.vue"
import StyledButton from "@/Components/StyledButton.vue"

defineProps({
  entityName: {
    type: String,
    required: true
  },
  close: {
    type: Function,
    required: true
  },
  item: {
    type: Number,
    required: false
  },
  deleteItem: {
    type: Function,
    required: true
  },
  error: {
    type: String,
    required: false
  }
})
</script>

<template>
  <Modal>
    <div class="p-6" dusk="delete_modal">
      <h2 class="text-lg font-medium text-gray-900 text-center">
        Are you sure you want to delete this {{ entityName }}?
      </h2>
      <p class="text-red-600 text-center py-2">{{ error }}</p>
      <div class="mt-6 flex justify-center">
        <StyledButton
          variant="secondary"
          @click="close"
          dusk="close_modal"
        >
          Cancel
        </StyledButton>
        <StyledButton
          variant="danger"
          class="ml-3"
          @click="deleteItem(item)"
          dusk="delete_item"
          >
          Delete
        </StyledButton>
      </div>
    </div>
  </Modal>
</template>
