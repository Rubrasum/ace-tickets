<template>
    <div class="relative">
        <button
            @click="toggleDropdown"
            class="mr-1 lg:w-64 flex items-center justify-between px-2 py-2 bg-white text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-600"
        >
            {{ selectedLabel || `All ${props.title}` }}
            <ChevronDownIcon class="h-5 w-5 text-gray-500 absolute right-2" aria-hidden="true" />
        </button>

        <div
            v-if="isDropdownOpen"
            class="absolute z-50 w-full bg-white border border-gray-300 shadow-lg rounded-b-md overflow-auto max-h-52 thin-scrollbar"
        >
            <DropdownItem
                @click="updateFilter(null)"
                :active="!currentValue"
            >
                All {{ props.title }}
            </DropdownItem>
            <DropdownItem
                v-for="option in options"
                :key="option.value"
                :active="currentValue === option.value"
                @click="updateFilter(option.value)"
            >
                {{ option.label }}
            </DropdownItem>
        </div>
    </div>
</template>

<script setup>
import {computed, ref} from 'vue'
// import Icon from './Icon.vue'
import DropdownItem from './DropdownItem.vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import {router, usePage} from '@inertiajs/vue3'

const page = usePage()


const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    filterKey: {
        type: String,
        required: true,
    },
    options: {
        type: Array,
        default: () => [], // Default to empty array to prevent undefined
    },
    currentValue: {
        type: [String, null],
        default: null,
    },
});
const emit = defineEmits(['update:currentValue']);
const isDropdownOpen = ref(false)

const selectedLabel = computed(() => {
    const selectedOption = props.options.find(option => option.value === props.currentValue);
    return selectedOption ? selectedOption.label : null;
});

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const updateFilter = (value) => {
    isDropdownOpen.value = false;
    emit('update:currentValue', value);
};
</script>
