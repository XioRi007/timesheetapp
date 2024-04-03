<script setup>
import {computed, ref} from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import {Link, usePage} from '@inertiajs/vue3'
import StyledLink from "@/Components/StyledLink.vue"

const page = usePage()
const showingNavigationDropdown = ref(false)
const userName = computed(() => {
  if (page.props.auth.user.roles.some(role => role.name === 'developer')) {
    return page.props.auth.user.developer.first_name + " " + page.props.auth.user.developer.last_name
  }
  return 'admin'
})
</script>

<template>
  <div>
    <div class="min-h-screen bg-gray-100">
      <nav class="bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="shrink-0 flex items-center">
                <Link :href="route('dashboard')">
                  <ApplicationLogo
                    class="block h-9 w-auto fill-current text-gray-800"
                  />
                </Link>
              </div>
              <!-- Navigation Links -->
              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                   v-if="$page.props.auth.user.roles.some(role => role.name === 'admin')">
                <StyledLink :href="route('dashboard')" :active="route().current('dashboard')" variant="nav">
                  Dashboard
                </StyledLink>
                <StyledLink :href="route('clients.index')" :active="route().current('clients.index')" variant="nav">
                  Clients
                </StyledLink>
                <StyledLink :href="route('projects.index')" :active="route().current('projects.index')" variant="nav">
                  Projects
                </StyledLink>
                <StyledLink :href="route('developers.index')" :active="route().current('developers.index')"
                            variant="nav">
                  Developers
                </StyledLink>
                <StyledLink :href="route('worklogs.index')" :active="route().current('worklogs.index')" variant="nav">
                  Work Logs
                </StyledLink>
              </div>

              <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                   v-if="$page.props.auth.user.roles.some(role => role.name === 'developer')">
                <StyledLink
                  :href="route('developers.worklogs', $page.props.auth.user.developer.id)"
                  :active="route().current('developers.worklogs', $page.props.auth.user.developer.id)"
                  variant="nav"
                >
                  Work Logs
                </StyledLink>
              </div>

            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
              <!-- Settings Dropdown -->
              <div class="ml-3 relative">
                <Dropdown align="right" width="48">
                  <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                              dusk="profile_btn"
                                              type="button"
                                              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ userName }}

                                                <svg
                                                  class="ml-2 -mr-0.5 h-4 w-4"
                                                  xmlns="http://www.w3.org/2000/svg"
                                                  viewBox="0 0 20 20"
                                                  fill="currentColor"
                                                >
                                                    <path
                                                      fill-rule="evenodd"
                                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                      clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('profile.edit')"> Profile</DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button" dusk="logout">
                      Log Out
                    </DropdownLink>
                  </template>
                </Dropdown>
              </div>
            </div>

          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header class="bg-white shadow" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header"/>
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot/>
      </main>
    </div>
  </div>
</template>
