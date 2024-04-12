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
  <Head title="Work Logs"/>

  <AuthenticatedLayout>
    <template #header>
      <Title text="Work Logs"/>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
        <div class="bg-white overflow-hidden p-10">
          <div
            class="relative">
            <StyledLink
              :href="route('worklogs.create')"
              icon="fa-solid fa-plus"
              variant="add"
            />
            <DataTable
              :data="$page.props.worklogs.data"
              :has-actions=false
              :status-text="['Paid', 'Unpaid']"
              delete-action-link="worklogs.destroy"
              edit-action-link="worklogs.edit"
              entity-name="work log"
              redirect-link="worklogs.index"
              :column="$page.props.column"
              :ascending="$page.props.ascending"
              :filter-params="$page.props.filterParams"
              :filterFormat="[{
                  name: 'Date',
                  real: 'date',
                  type: 'select',
                  data: $page.props.filterData.date
                },{
                  name: 'Project',
                  real: 'project_id',
                  type: 'select_model',
                  data: $page.props.filterData.projects
                },{
                  name: 'Rate',
                  real: 'rate',
                  type: 'select',
                  data: $page.props.filterData.rate
                },{
                  name: 'Hours',
                  real: 'hrs',
                  type: 'select',
                  data: $page.props.filterData.hrs
                },{
                  name: 'Total',
                  real: 'total',
                  type: 'select',
                  data: $page.props.filterData.total
                },{
                  name: 'Status',
                  real: 'status',
                  type: 'select_status',
                  data: $page.props.filterData.status
                }
               ]"
            />
            <Pagination
              :links="$page.props.worklogs.links"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
