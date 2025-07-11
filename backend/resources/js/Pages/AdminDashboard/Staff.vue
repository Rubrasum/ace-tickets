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
                <div class="relative mr-4 flex-1" :class="{ hidden : !users }">
                    <!-- Categories dropdown component -->
                    <Dropdown
                        title="Roles"
                        filter-key="role"
                        :options="[
                            { value: 'ticket scanner', label: 'Ticket Scanner' },
                            { value: 'ticket counter', label: 'Ticket Counter' },
                        ]"
                        v-model="filters.role"
                        @change="applyFilters"
                    />
                </div>
                <div class="relative mr-4 flex-1" :class="{ hidden : !users }">
                    <Dropdown
                        title="Devices"
                        filter-key="device"
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
                    <form @submit.prevent="handleSearch" ref="form">
                        <!--                        This is hidden. Please remember. You might be looking for dropdown ^^ above (categories)-->
                        <input
                            v-if="filters.role"
                            type="hidden"
                            name="category"
                            :value="filters.role"
                        />

                        <div class="pointer-events-none absolute top-0 left-0 flex items-center pl-3 pt-2">
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
                                            {{ direction ? '⬇️' : '⬆️' }}
                                        </span>
                                    </th>
                                    <th
                                        @click="updateSort('email')"
                                        class="cursor-pointer top-0 z-10 hidden border-b border-gray-300 bg-white/75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter lg:table-cell"
                                    >
                                        Email
                                        <span v-if="sort === 'email'">
                                            {{ direction ? '⬇️' : '⬆️' }}
                                        </span>
                                    </th>
                                    <th
                                        @click="updateSort('role')"
                                        class="cursor-pointer top-0 z-10 border-b border-gray-300 bg-white/75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter"
                                    >
                                        Role
                                        <span v-if="sort === 'role'">
                                            {{ direction ? '⬇️' : '⬆️' }}
                                        </span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user, userIdx) in users" :key="user.email">
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '', 'whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8']">{{ user.name }}</td>
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '', 'hidden whitespace-nowrap px-3 py-4 text-sm text-gray-500 lg:table-cell']">{{ user.email }}</td>
                                    <td :class="[userIdx !== users.length - 1 ? 'border-b border-gray-200' : '', 'whitespace-nowrap px-3 py-4 text-sm text-gray-500']">{{ user.role }}</td>
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
import Dropdown from "@/Components/Default/Dropdown.vue";
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'


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
    sort: String,
    direction: Boolean,
});


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
        device: device
    }, {
        only: ['users', 'sort', 'direction', 'filters'],
        preserveState: true,
        preserveScroll: true,
    })
};

function updateSort(field) {
    const isSameField = props.sort === field // check if clicking on a column for 2nd+ time
    const sortField = isSameField && !props.direction ? 'name' : field; // Resets to name on 3rd click
    const newDirection = isSameField ? !props.direction : true; // switch to desc (false) on same field.
    const role = props.filters.role;
    const device = props.filters.device;

    router.get(route('admin.dashboard.staff'), {
        sort: sortField,
        direction: newDirection,
        role: role,
        device: device
    }, {
        only: ['users', 'sort', 'direction', 'filters'],
        preserveState: true,
        preserveScroll: true,
    })
}

</script>
