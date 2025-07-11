<template>
    <div
        @click="$emit('click')"
        :class="[
            'px-4 py-2 text-sm text-gray-700 cursor-pointer hover:bg-indigo-50 hover:text-indigo-600',
            active ? 'bg-indigo-100 text-indigo-700' : ''
        ]"
    >
        <slot />
    </div>
</template>

<script setup>
defineProps({
    active: {
        type: Boolean,
        default: false,
    },
});
</script>
