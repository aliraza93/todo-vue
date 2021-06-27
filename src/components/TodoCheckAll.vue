<template>
    <div>
        <label>
          <input type="checkbox" 
                :checked="!anyRemaining"
                @change="allChecked"
                >Check All
        </label>
    </div>
</template>

<script>
export default {
    props: ['anyRemaining'],

    methods: {
        allChecked() {
            eventBus.$emit('check-all-changed', this.anyRemaining)
        }
    }
}
</script>