<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import {Head, useForm} from '@inertiajs/vue3'
import * as yup from "yup"
import StyledButton from "@/Components/StyledButton.vue"

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
})

const schema = yup.object({
  email: yup.string().required().email(),
  password: yup.string().required()
})
const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = async () => {
  try {
    form.clearErrors()
    await schema.validate(form, {abortEarly: false})
    form.post(route('login'), {
      onFinish: () => form.reset('password'),
    })
  } catch (err) {
    err.inner.forEach((element) => {
      form.setError(element.path, element.message)
    })
  }
}
</script>

<template>
  <GuestLayout>
    <Head title="Log in"/>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" value="Email"/>

        <TextInput
          dusk="email"
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          autofocus
          autocomplete="email"
        />

        <InputError class="mt-2" :message="form.errors.email"/>
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Password"/>

        <TextInput
          dusk="password"
          id="password"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password"
          autocomplete="current-password"
        />

        <InputError class="mt-2" :message="form.errors.password"/>
      </div>

      <div class="block mt-4">
        <label class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember" dusk="remember"/>
          <span class="ml-2 text-sm text-gray-600">Remember me</span>
        </label>
      </div>

      <div class="flex items-center justify-end mt-4">
        <StyledButton
          variant="primary"
          class="ml-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          dusk="submit"
        >
          Log in
        </StyledButton>
      </div>
    </form>
  </GuestLayout>
</template>
