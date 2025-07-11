<template>
    <div>
        <button
            @click="toggleDropdown"
            class="mr-1 lg:w-64 flex text-left lg:inline-flex px-2 py-2
            bg-primary-bg text-primary-text focus:outline-none focus:ring-2 focus:ring-accent relative
            rounded-none rounded-l-md border-0  text-primary-gray ring-1
                           ring-inset ring-primary-gray placeholder:text-secondary-text focus:ring-2 focus:ring-inset
                           focus:ring-accent sm:hidden"
        >
            {{ currentCategory ? currentCategory.name : 'All Categories' }}
            <ChevronDownIcon class="-mr-1 h-5 w-5 absolute right-4" :class="{ 'text-accent' : currentCategory, 'text-primary-gray' : !currentCategory }" aria-hidden="true" />
        </button>

        <div v-if="isDropdownOpen"
             class="absolute bg-secondary-bg border border-primary-gray shadow-light-lg z-50 inset-x-0 py-1 w-full
                    rounded-b-xl overflow-auto max-h-52 thin-scrollbar
">
            <DropdownItem
                @click="updatePosts(null)"
                :active="!currentCategory"
            >
                All Categories
            </DropdownItem>

            <DropdownItem
                v-for="category in categories"
                :key="category.slug"
                :active="currentCategory && currentCategory.slug === category.slug"
                @click="updatePosts(category.slug)"
            >
                {{ category.name }}
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

const categories = computed(() => page.props.categories)
const currentCategory = computed(() => page.props.currentCategory)
const isDropdownOpen = ref(false)
const search = computed(() => page.props.search)

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const updatePosts = (categorySlug) => {
    isDropdownOpen.value = false

    router.get('/', {
        category: categorySlug,
        search: search.value,
    })
}
</script>
