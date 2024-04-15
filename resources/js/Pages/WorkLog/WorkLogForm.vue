<script setup>
import InputLabel from "@/Components/InputLabel.vue"
import TextInput from "@/Components/TextInput.vue"
import InputError from "@/Components/InputError.vue"
import {router, useForm} from "@inertiajs/vue3"
import Toggle from "@/Components/Toggle.vue"
import {onMounted} from "vue"
import * as yup from "yup"
import {maxDecimalPlaces} from "@/validation.js"
import {createToast, showToast} from "@/useToast.js"
import DatePicker from "@/Components/DatePicker.vue"
import StyledButton from "@/Components/StyledButton.vue"

const props = defineProps({
  submitRoute: {
    type: String,
    required: true
  },
  worklog: {
    type: Object,
    required: false,
    default: {
      developer_id: null,
      project_id: null,
      rate: 0,
      hrs: 0,
      total: 0,
      status: false,
      date: new Date().toISOString().substring(0, 10)
    }
  },
  rate: {
    type: Number,
    required: false
  },
  developer: {
    type: Number,
    required: false
  },
  project: {
    type: Number,
    required: false
  },
})
const schema = yup.object({
  developer_id: yup.number().required(),
  project_id: yup.number().required(),
  rate: maxDecimalPlaces(2).required().min(0).max(999.99).typeError('rate is required'),
  hrs: maxDecimalPlaces(2).required().min(0.1).max(999.99).typeError('hours are required'),
  total: maxDecimalPlaces(2).required().min(0).max(99999999.99).typeError('total is required'),
  status: yup.string().required().oneOf(["true", "false"]),
  date: yup.date().required()
})
const form = useForm(props.worklog)
onMounted(() => {
  form.defaults()
  update()
  if (props.worklog.total)
    form.total = props.worklog.total
  if (props.worklog.rate)
    form.rate = props.worklog.rate
})
const submit = async () => {
  try {
    form.clearErrors()
    await schema.validate(form, {abortEarly: false})
    form.status = form.status === true
    if (route().current('worklogs.edit')) {
      form.put(props.submitRoute)
      createToast('Work Log was successfully updated')
    } else {
      form.post(props.submitRoute)
      createToast('Work Log was successfully created')
    }
  } catch (err) {
    showToast('Fill the form correctly', 'error')
    err.inner.forEach((element) => {
      form.setError(element.path, element.message)
    })
  }
}
const updateTotal = () => {
  console.log(form.rate);
  console.log(form.hrs);
  form.total = (form.rate * form.hrs)
}
const update = () => {
  if (props.developer) {
    form.developer_id = props.developer
  }
  if (props.project) {
    form.project_id = props.project
  }
  form.rate = props.rate
  updateTotal()
}
const changeUrl = (e) => {
  router.visit(
    `?developer=${form.developer_id}&project=${form.project_id}`, {
      only: ['developer', 'project', 'rate'],
      preserveState: true,
      onSuccess: update
    })
}
</script>

<template>
  <form class="mt-6 space-y-6" novalidate @submit.prevent="submit" dusk="form">

    <div>
      <InputLabel for="date" value="Date"/>
      <DatePicker
        class="w-full"
        v-model="form.date"
        dusk="date"
      />
      <InputError :message="form.errors.date" class="mt-2"/>
    </div>

    <div v-if="$page.props.auth.user.roles.some(role => role.name !== 'developer')">
      <InputLabel for="developer" value="Developer"/>
      <select id="developer"
              v-model="form.developer_id"
              class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
              name="developer"
              @change="changeUrl"
              dusk="developer_id"
      >
        <option v-for="developer in $page.props.developers" :value="developer.id">{{ developer.name }}</option>
      </select>
      <InputError :message="form.errors.developer_id" class="mt-2"/>
    </div>

    <div>
      <InputLabel for="project" value="Project"/>
      <select id="project"
              v-model="form.project_id"
              class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
              name="project"
              @change="changeUrl"
              dusk="project_id"
      >
        <option v-for="project in $page.props.projects" :value="project.id">{{ project.name }}</option>
      </select>
      <InputError :message="form.errors.project_id" class="mt-2"/>
    </div>


    <div>
      <InputLabel for="hrs" value="Hours"/>
      <TextInput
        id="hrs"
        v-model="form.hrs"
        autocomplete="hrs"
        class="mt-1 block w-full"
        step=".01"
        type="number"
        @change="updateTotal"
        dusk="hrs"
      />
      <InputError :message="form.errors.hrs" class="mt-2"/>
    </div>
<div v-if="$page.props.auth.user.roles.some(role => role.name === 'admin')">
  <div>
    <InputLabel for="rate" value="Rate"/>
    <TextInput
      id="rate"
      v-model="form.rate"
      autocomplete="rate"
      class="mt-1 block w-full"
      step=".01"
      type="number"
      @change="updateTotal"
      dusk="rate"
    />
    <InputError :message="form.errors.rate" class="mt-2"/>
  </div>

  <div class="mt-6">
    <InputLabel for="total" value="Total"/>
    <TextInput
      id="total"
      v-model="form.total"
      autocomplete="total"
      class="mt-1 block w-full"
      step=".01"
      type="number"
      dusk="total"
    />
    <InputError :message="form.errors.total" class="mt-2"/>
  </div>

  <div v-show="route().current('worklogs.edit')" class="flex justify-between mt-6">
    <Toggle
      v-model="form.status"
      active-text="Paid"
      not-active-text="Unpaid"
      dusk="status"
    />
  </div>
</div>


    <div class="flex items-center gap-4 justify-between">
      <StyledButton
        :disabled="form.processing"
        variant="primary"
        dusk="submit"
      >
        Save
      </StyledButton>
      <StyledButton
        :disabled="form.processing"
        variant="secondary"
        @click.prevent="form.reset()"
        dusk="reset"
      >
        Reset
      </StyledButton>
      <Transition class="transition ease-in-out" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
      </Transition>
    </div>
  </form>
</template>
