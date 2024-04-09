<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Head} from '@inertiajs/vue3'
import DataTable from "@/Components/DataTable.vue"
import Title from "@/Components/Title.vue"
import {useToast} from "@/useToast.js"
import Pagination from "@/Components/Pagination.vue"
import StyledLink from "@/Components/StyledLink.vue"

useToast();
</script>

<template>
  <Head title="Projects"/>

  <AuthenticatedLayout>
    <template #header>
      <Title text="Projects"/>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
        <div class="bg-white overflow-hidden p-10">
          <div
            class="relative">
            <StyledLink
              :href="route('projects.create')"
              icon="fa-solid fa-plus"
              variant="add"
              dusk="create"
            />
            <DataTable
              :data="$page.props.projects.data"
              :has-actions=true
              delete-action-link="projects.destroy"
              edit-action-link="projects.edit"
              entity-name="project"
              redirect-link="projects.index"
              :column="$page.props.column"
              :ascending="$page.props.ascending"
              :filter-params="$page.props.filterParams"
              :filterFormat="[{
                  name: 'Name',
                  real: 'name',
                  type: 'select',
                  data: $page.props.filterData.name
                }, {
                  name: 'Client',
                  real: 'client_id',
                  type: 'select_model',
                  data: $page.props.filterData.clients
                },{
                  name: 'Rate',
                  real: 'rate',
                  type: 'select',
                  data: $page.props.filterData.rate
                },{
                  name: 'Status',
                  real: 'status',
                  type: 'select_status',
                  data: $page.props.filterData.status
                }
               ]"
            />
            <Pagination
              :links="$page.props.projects.links"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
