<script setup>
import TextInput from "@/Components/TextInput.vue"
import InputLabel from "@/Components/InputLabel.vue"
import InputError from "@/Components/InputError.vue"
import {router, useForm} from "@inertiajs/vue3"
import {showToast} from "@/useToast.js"
import DatePicker from "@/Components/DatePicker.vue"
import {computed} from "vue"
import StyledButton from "@/Components/StyledButton.vue"

const props = defineProps({
  filterParams: {
    type: Object
  },
  statusText: {
    type: Array,
    default: ['Active', 'Inactive']
  },
  filterFormat: {
    type: Array,
    required: true
  },
})
const defaultParams = {}

props.filterFormat.forEach((column) => {
  defaultParams[column.real] = null
})
const form = useForm(Object.keys(props.filterParams).length ? props.filterParams : defaultParams)
form.defaults(defaultParams)


const submit = async () => {
  try {
    form.clearErrors()
    router.get('', {
      filter: form.data(),
      page:null
    })
  } catch (err) {
    showToast('Fill the form correctly', 'error')
    err.inner.forEach((element) => {
      form.setError(element.path, element.message)
    })
  }

}

const reset = () => {
  form.reset()
  console.log(form.data())
  router.get('', {
    filter: form.data(),
    column: null,
    ascending: null,
    page:null
  })
}
const computedStyles = computed(()=>{
  const gridColumnCount = props.filterFormat.length + 1;
  return {
    'grid-template-columns': `repeat(${gridColumnCount}, minmax(0, 1fr))`
  };
})
</script>

<template>
  <form
    dusk="filter"
    novalidate
    class="flex mb-6 items-end grid"
    :style="computedStyles"
    @submit.prevent="submit">
    <div v-for="column in filterFormat" class="col-span-1 pr-3 font-medium text-black">
      <InputLabel :for="column.real" :value="column.name"/>
      <TextInput
        v-if="column.type === 'rate' || column.type === 'hrs' || column.type === 'total'"
        :id="column.real"
        v-model="form[column.real]"
        :autocomplete="column.real"
        class="mt-1 block w-full"
        step=".01"
        type="number"
        :dusk="column.real"
      />

      <select
        v-else-if="column.type === 'select_status'"
        :id="column.real"
        v-model="form[column.real]"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
        :dusk="column.real"
      >
        <option :value=null selected></option>
        <option v-for="item in column.data" :value="item">{{ item ? statusText[0]: statusText[1]}}</option>
      </select>

      <select
        v-else-if="column.type === 'status'"
        :id="column.real"
        v-model="form[column.real]"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
        :dusk="column.real"
      >
        <option :value=null selected></option>
        <option :value="true">{{ statusText[0] }}</option>
        <option :value="false">{{ statusText[1] }}</option>
      </select>

      <select
        :id="column.real"
        v-else-if="column.type === 'select_model'"
        v-model="form[column.real]"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
        :dusk="column.real"
      >
        <option :value=null selected></option>
        <option v-for="item in column.data" :value="item.id">{{ item.name }}</option>
      </select>


      <select
        :id="column.real"
        v-else-if="column.type === 'select'"
        v-model="form[column.real]"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
        :dusk="column.real"
      >
        <option :value=null selected></option>
        <option v-for="item in column.data" :value="item">{{ item }}</option>
      </select>

      <DatePicker
        :id="column.real"
        v-else-if="column.type === 'date'"
        v-model="form[column.real]"
        class="mt-1 block w-full"
        :dusk="column.real"
      />

      <TextInput
        v-else
        :id="column.real"
        v-model="form[column.real]"
        :autocomplete="column.real"
        class="mt-1 block w-full"
        type="text"
        :dusk="column.real"
      />
    </div>
    <div class="flex justify-between">
      <StyledButton
        :disabled="form.processing"
        class=" h-10 w-2/4 mr-1"
        type="submit"
        variant="primary"
      >
        Filter
      </StyledButton>
      <StyledButton
        :disabled="form.processing"
        class="h-10 w-2/4"
        @click="reset"
        variant="secondary"
        dusk="reset"
      >
        Reset
      </StyledButton>
    </div>
  </form>
  <InputError class="mb-2" v-for="error in form.errors" :message="error"/>
</template>