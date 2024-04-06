<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Head} from '@inertiajs/vue3'
import Title from "@/Components/Title.vue"
import ClientForm from "@/Pages/Client/ClientForm.vue"
import StyledLink from "@/Components/StyledLink.vue"


const props = defineProps({
  client: {
    type: Object,
  },
})
</script>

<template>
  <Head title="Edit Client"/>
  <AuthenticatedLayout>
    <template #header>
      <Title text="Edit Client"/>
    </template>

    <div class="py-12 w-2/4 m-auto">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="bg-white overflow-hidden mb-6 flex justify-end">
            <StyledLink
              :href="$page.props.backLink"
              icon="fa-solid fa-xmark"
              variant="close"
              dusk="close"
            />
          </div>
          <ClientForm
            :client="client"
            :submitRoute="route('clients.update', props.client.id)"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
