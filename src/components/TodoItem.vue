<template>
    <div class="todo-item">
        <div class="todo-item-left">
          <input type="checkbox" 
                v-model="completed"
                @change="doneEditing"
            >
            <div 
              v-if="!editing" 
              @dblclick="editTodo" 
              class="todo-item-label"
              :class="completed ? 'completed' : ''"
            >{{ title }}
            </div>
            <input v-else type="text" 
                v-focus 
                @blur="doneEditing" 
                @keyup.enter="doneEditing" 
                class="todo-item-edit " 
                v-model="title"
                @keyup.esc="cancelEdit"
                >
        </div>  
        <div class="remove-item" @click="removeTodo(index)">
          &times;
        </div>
    </div>
</template>

<script>
export default {
    props: ['todo', 'index', 'checkAll'],
    
    data() {
        return {
            id: this.todo.id,
            title: this.todo.title,
            completed: this.todo.completed,
            editing: this.todo.editing,
            beforeEditCache: ''
        }
    },

    watch: {
        checkAll() {
            if(this.checkAll) {
                this.completed = true
            }
            else {
                this.completed = this.todo.completed
            }
        }
    },

    directives: {
        focus: {
            inserted: function(el) {
                el.focus()
            }
        }
    },

    methods: {
        editTodo() {
            this.beforeEditCache = this.title
            this.editing = true
        },

        doneEditing() {
            if(this.title.trim() == '') {
                this.title = this.beforeEditCache
            }
            this.editing = false
            this.$store.dispatch('updateTodo', {
                id: this.id,
                title: this.title,
                completed: this.completed,
                editing: this.editing
            })
        },

        cancelEdit() {
            this.title = this.beforeEditCache
            this.editing = false
        },
        
        removeTodo(index) {
            this.$store.dispatch('deleteTodo', index)
        }
    }
}
</script>