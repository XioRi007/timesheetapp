<script setup>
import InputLabel from "@/Components/InputLabel.vue"
import TextInput from "@/Components/TextInput.vue"
import InputError from "@/Components/InputError.vue"
import {useForm} from "@inertiajs/vue3"
import Toggle from "@/Components/Toggle.vue"
import * as yup from "yup"
import {maxDecimalPlaces} from "@/validation.js"
import {createToast, showToast} from "@/useToast.js"
import StyledButton from "@/Components/StyledButton.vue"

const props = defineProps({
  submitRoute: {
    type: String,
    required: true
  },
  developer: {
    type: Object,
    required: false,
    default: {
      first_name: '',
      last_name: '',
      email: '',
      password: '',
      rate: 1.00,
      rate_percent: 1.00,
      status: true,
    }
  },
})

const createSchema = yup.object({
  first_name: yup.string().required(),
  last_name: yup.string().required(),
  email: yup.string().required().email(),
  password: yup.string().required(),
  rate: maxDecimalPlaces(2).required().min(0).max(999.99).typeError('rate is required'),
  status: yup.string().required().oneOf(['true', 'false']),
})
const updateSchema = yup.object({
  first_name: yup.string().required(),
  last_name: yup.string().required(),
  rate: maxDecimalPlaces(2).required().min(0).max(999.99).typeError('rate is required'),
  status: yup.string().required().oneOf(['true', 'false']),
})
const form = useForm(props.developer)
form.defaults()
const submit = async () => {
  try {
    form.clearErrors()
    if (route().current('developers.edit')) {
      await updateSchema.validate(form, {abortEarly: false})
      form.status = form.status === true
      form
        .transform((data)=>{
          delete data.password
          delete data.email
          return data
        })
        .put(props.submitRoute)
      createToast('Developer was successfully updated')
    } else {
      await createSchema.validate(form, {abortEarly: false})
      form.status = form.status === true
      form.post(props.submitRoute)
      createToast('Developer was successfully created')
    }
  } catch (err) {
    showToast('Fill the form correctly', 'error')
    err.inner.forEach((element) => {
      form.setError(element.path, element.message)
    })
  }
}
</script>

<template>
  <form class="mt-6 space-y-6" novalidate @submit.prevent="submit" dusk="form">

    <div>
      <InputLabel for="name" value="First Name"/>
      <TextInput
        id="first_name"
        v-model="form.first_name"
        autocomplete="first_name"
        autofocus
        class="mt-1 block w-full"
        type="text"
        dusk="first_name"
      />
      <InputError :message="form.errors.first_name" class="mt-2"/>
    </div>

    <div>
      <InputLabel for="name" value="Last Name"/>
      <TextInput
        id="last_name"
        v-model="form.last_name"
        autocomplete="last_name"
        class="mt-1 block w-full"
        type="text"
        dusk="last_name"
      />
      <InputError :message="form.errors.last_name" class="mt-2"/>
    </div>

    <div v-if="!route().current('developers.edit')">
      <div>
        <InputLabel for="email" value="Email"/>
        <TextInput
          id="email"
          v-model="form.email"
          autocomplete="email"
          class="mt-1 block w-full"
          type="text"
          dusk="email"
        />
        <InputError :message="form.errors.email" class="mt-2"/>
      </div>

      <div class="mt-6">
        <InputLabel for="password" value="Password"/>
        <TextInput
          id="password"
          v-model="form.password"
          autocomplete="password"
          class="mt-1 block w-full"
          type="password"
          dusk="password"
        />
        <InputError :message="form.errors.password" class="mt-2"/>
      </div>
    </div>

    <div>
      <InputLabel for="rate" value="Rate"/>
      <TextInput
        id="rate"
        v-model="form.rate"
        autocomplete="rate"
        class="mt-1 block w-full"
        step="0.01"
        type="number"
        dusk="rate"
      />
      <InputError :message="form.errors.rate" class="mt-2"/>
    </div>

    <div v-show="route().current('developers.edit')">
      <Toggle
        v-model="form.status"
        active-text="Active"
        not-active-text="Inactive"
        dusk="status"
      />
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
