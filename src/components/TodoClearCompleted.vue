<template>
    <div>
        <transition name="fade">
          <button
            v-if="showClearCompletedButton"
            @click="clearCompleted"
            >
            Clear Completed
          </button>
        </transition>
    </div>
</template>

<script>
export default {
    props: ['showClearCompletedButton'],

    methods: {
        clearCompleted() {
            eventBus.$emit('clearCompleted')
        }
    }

}
</script>