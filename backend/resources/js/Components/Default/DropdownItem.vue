<template>
    <a :class="classes">
        <slot></slot>
    </a>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    active: {
        type: Boolean,
        default: false
    }
})

const classes = computed(() => {
    let baseClasses = "block text-left px-3 text-sm leading-6 hover:text-accent " +
        "hover:bg-primary-bg"
    if (props.active) {
        baseClasses += ' bg-primary-bg text-accent'
    } else {
        baseClasses += ' text-primary-text'
    }
    return baseClasses
})
</script>
