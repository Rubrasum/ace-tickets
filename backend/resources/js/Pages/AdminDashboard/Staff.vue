<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin Dashboard - Staff
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <div class="relative flex focus-within:z-10">
                <div class="flex-1 flex items-center justify-end mr-2">
                    <h5 :class="{'text-accent' : search || filters }" >Role filter: </h5>
                </div>
                <div class="relative mr-2 flex-1" :class="{ hidden : !users }">
                    <!-- Categories dropdown component -->
                    <Dropdown
                        :title="'Roles'"
                        :options="[
                            { value: 'ticket scanner', label: 'Ticket Scanner' },
                            { value: 'ticket counter', label: 'Ticket Counter' },
                        ]"
                        v-model="filters.role"
                        @change="applyFilters"
                    />
                </div>
                <div class="flex items-center justify-end mr-2">
                    <h5 :class="{'text-accent' : search || filters }" >Device filter: </h5>
                </div>
                <div class="relative mr-4 flex-1" :class="{ hidden : !users }">
                    <Dropdown
                        :title="'Devices'"
                        :options="[
                            { value: 'tablet', label: 'Tablet' },
                            { value: 'desktop', label: 'Desktop' },
                            { value: 'mobile', label: 'Mobile' },
                        ]"
                        v-model="filters.device"
                        @change="applyFilters"
                    />
                </div>
                <div class="relative mr-4 w-full flex-1" :class="{ hidden : !users }">
                    <form @submit.prevent="debouncedSearch">
                        <div class="pointer-events-none absolute top-2.5 left-0 flex items-center pl-3 ">
                            <MagnifyingGlassIcon class="h-5 w-5" aria-hidden="true" />
                        </div>
                        <input
                            type="search"
                            name="search"
                            placeholder="Search"
                            class="block w-full rounded-none rounded-l-md border-0 py-1.5 pl-10 ring-1
                                        ring-inset ring-primary-gray placeholder:text-secondary-text focus:ring-2 focus:ring-inset
                                        focus:ring-accent border border-accent rounded-lg px-4 py-2
                                       bg-primary-bg text-primary-text"
                            v-model="search"
                            @input="debouncedSearch"
                        />
                    </form>
                </div>
            </div>
            <h2>All Staff</h2>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle">
                        <!-- Scrollable container for sticky headers -->
                        <div class="overflow-y-auto">
                            <table class="min-w-full border-separate border-spacing-0">
                                <thead>
                                <tr>
                                    <th
                                        @click="updateSort('name')"
                                        class="cursor-pointer top-0 z-10 border-b border-gray-300 bg-white/75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8"
                                    >
                                        Name
                                        <span v-if="sort === 'name'">
                                            {{ direction ? '⬆️' : '⬇️' }}
                                        </span>
                                    </th>
                                    <th
                                        @click="updateSort('email')"
                                        class="cursor-pointer top-0 z-10 hidden border-b border-gray-300 bg-white/75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter lg:table-cell"
                                    >
                                        Email
                                        <span v-if="sort === 'email'">
                                            {{ direction ? '⬆️' : '⬇️' }}
                                        </span>
                                    </th>
                                    <th
                                        @click="updateSort('role')"
                                        class="cursor-pointer top-0 z-10 border-b border-gray-300 bg-white/75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                    >
                                        Role
                                        <span v-if="sort === 'role'">
                                            {{ direction ? '⬆️' : '⬇️' }}
                                        </span>
                                    </th>
                                    <th
                                        @click="updateSort('device')"
                                        class="cursor-pointer top-0 z-10 border-b border-gray-300 bg-white/75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                    >
                                        Device
                                        <span v-if="sort === 'device'">
                                            {{ direction ? '⬆️' : '⬇️' }}
                                        </span>
                                    </th>
                                    <th
                                        @click="updateSort('last_login_at')"
                                        class="cursor-pointer top-0 z-10 border-b border-gray-300 bg-white/75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                    >
                                        Last Login At
                                        <span v-if="sort === 'last_login_at'">
                                            {{ direction ? '⬆️' : '⬇️' }}
                                        </span>
                                    </th>
                                    <th
                                        @click="updateSort('is_active')"
                                        class="cursor-pointer top-0 z-10 border-b border-gray-300 bg-white/75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                    >
                                        Is Active
                                        <span v-if="sort === 'is_active'">
                                            {{ direction ? '⬆️' : '⬇️' }}
                                        </span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user, userIdx) in users" :key="user.email">
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '', 'whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8']">{{ user.name }}</td>
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '', 'hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell']">{{ user.email }}</td>
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '', 'whitespace-nowrap px-3 py-4 text-sm text-gray-500']">{{ user.role }}</td>
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '',
                                    'whitespace-nowrap px-3 py-4 text-sm text-gray-500']">{{ user.device_type }}</td>
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '',
                                        'whitespace-nowrap px-3 py-4 text-sm text-gray-500']">
                                        {{ user.last_login_at ? new Date(user.last_login_at).toLocaleString(undefined, {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) : 'Never' }}
                                    </td>
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '',
                                        'whitespace-nowrap px-3 py-4 text-sm text-gray-500']">
                                        <span :class="[
                                            'inline-block w-2 h-2 rounded-full',
                                            user.is_active ? 'bg-green-500' : 'bg-red-500'
                                        ]"></span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {router, usePage} from "@inertiajs/vue3";
import Dropdown from "@/Components/Default/FilterDropdown.vue";
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { ref } from 'vue'


const page = usePage();
const props = defineProps({
    users: {
        type: Object,
        required: false,
    },
    filters: {
        type: Object,
        default: () => ({ role: '', device: '' }),
    },
    sort: {
        type: String,
        default: ''
    },
    direction: Boolean,
    search: {
        type: String,
        default: ''
    },
});
const search = ref(props.search) // This isn't as clean because its not a custom component. Can't directly v-model.

// Normally you might see a watcher here but that seems unnecessary to me when it can be manually setup. Maybe I am
// just duplicating that same functionality.
// search debounce
let searchTimer = null
function debouncedSearch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => {
        applyFilters();
    }, 200)
}


// Apply filters and refresh data.
const applyFilters = () => {
    const sortField = props.sort;
    const direction = props.direction;
    const role = props.filters.role;
    const device = props.filters.device;

    router.get(route('admin.dashboard.staff'), {
        sort: sortField,
        direction: direction,
        role: role,
        device: device,
        search: search.value,
    }, {
        only: ['users', 'sort', 'direction', 'filters', 'search'],
        preserveState: true,
        preserveScroll: true,
    })
};

function updateSort(field) {
    const role = props.filters.role;
    const device = props.filters.device;
    const isSameField = props.sort === field // check if clicking on a column for 2nd+ time
    const sortField = isSameField && !props.direction ? 'name' : field; // Resets to name on 3rd click
    const newDirection = isSameField ? !props.direction : true; // switch to desc (false) on same field.

    router.get(route('admin.dashboard.staff'), {
        sort: sortField,
        direction: newDirection,
        role: role,
        device: device,
        search: search.value,
    }, {
        only: ['users', 'sort', 'direction', 'filters', 'search'],
        preserveState: true,
        preserveScroll: true,
    })
}

</script>
