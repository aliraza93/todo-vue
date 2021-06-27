<template>
    <div>{{ remaining }} items left</div>
</template>

<script>
export default {
    computed: {
        remaining() {
            return this.$store.getters.remaining
        },
    }
}
</script>