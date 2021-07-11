<template>
  <div class="container">
    <input type="text" v-model="newTodo" @keyup.enter="addTodo" class="todo-input" placeholder="What needs to be done">
    
    <transition-group
      name="fade"
      enter-active-class="animate__fadeInUp"
      leave-active-class="animate__fadeInDown"
      >
      <todo-item 
        v-for="(todo, index) in todosFiltered" 
        :key="index"
        :todo="todo"
        :index="index"
        :checkAll="!anyRemaining"
        >
      </todo-item>
    </transition-group>

    <div class="extra-container">
      <todo-check-all></todo-check-all>
      <todo-item-remaining></todo-item-remaining>
    </div>

    <div class="extra-container">
      <todo-filtered></todo-filtered>
      <todo-clear-completed :showClearCompletedButton="showClearCompletedButton"></todo-clear-completed>
    </div>

  </div>
</template>

<script>
import TodoCheckAll from './TodoCheckAll.vue'
import TodoClearCompleted from './TodoClearCompleted.vue'
import TodoFiltered from './TodoFiltered.vue'
import TodoItem from './TodoItem.vue'
import TodoItemRemaining from './TodoItemRemaining.vue'
export default {
  components: { 
    TodoItem, 
    TodoItemRemaining,
    TodoCheckAll,
    TodoFiltered,
    TodoClearCompleted, 
  },
  name: 'TodoList',
  data () {
    return {
      newTodo: '',
      idForTodo: 3,
      beforeEditCache: '',
    }
  },

  computed: {
    remaining() {
      return this.$store.getters.remaining
    },

    anyRemaining() {
      return this.$store.getters.anyRemaining
    },

    todosFiltered() {
      return this.$store.getters.todosFiltered
    },

    showClearCompletedButton() {
      return this.$store.getters.showClearCompletedButton
    }
  },
  
  created() {
    this.$store.dispatch('retrieveTodos')
    eventBus.$on('removedTodo', (index) => this.removeTodo(index))
    eventBus.$on('finishedEditing', (data) => this.finishedEdit(data))
    eventBus.$on('check-all-changed', (checked) => this.checkAllTodos(checked))
  },

  beforeDestroy() {
    eventBus.$off('removedTodo', (index) => this.removeTodo(index))
    eventBus.$off('finishedEditing', (data) => this.finishedEdit(data))
    eventBus.$off('check-all-changed', (checked) => this.checkAllTodos(checked))
  },

  methods: {
    addTodo() {
      if(this.newTodo.trim().length == 0) {
        return
      }

      this.$store.dispatch('addTodo', {
        id: this.idForTodo,
        title: this.newTodo,
        completed: false,
        editing: false
      })
      this.newTodo = ''
      this.idForTodo++
    },

    removeTodo(index) {
      this.$store.state.todos.splice(index, 1  )
    },

    finishedEdit(data) {
      const index = this.$store.state.todos.findIndex(item => item.id == data.id)
      this.$store.state.todos.splice(index, 1, data.todo)
    },  
  },
}
</script>

<style lang="scss">

  @import url(
    "https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  );

  .todo-input {
    width: 100%;
    padding: 10px 18px;
    font-size: 18px;
    margin-bottom: 16px;

    :focus {
      outline: 0;
    }
  }

  .todo-item {
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    animation-duration: 0.3s;
  }

  .remove-item {
    cursor: pointer;
    margin-left: 14px;
    :hover {
      color: #000;
    }
  }

  .todo-item-left {
    display: flex;
    align-items: center;
  }

  .todo-item-label {
    padding: 10px;
    border: 1px solid white;
    margin-left: 12px;
  }

  .todo-item-edit {
    font-size: 24px;
    color: #2c3e50;
    margin-left: 12px;
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
  
    :focus {
      outline: none;
    }
  }

  .completed {
    text-decoration: line-through;
    color: grey;
  }

  //Extra container styles
  .extra-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 16px;
    border-top: 1px solid lightgray;
    padding-top: 14px;
    margin-bottom: 14px;
  }

  button {
    font-size: 14px;
    background-color: white;
    appearance: none;
    padding: 4px;

    &:hover {
      background: lightgreen;
    }

    &:focus {
      outline: none;
    }
  }

  .active {
    background: lightgreen;
  }

  //CSS Transitions
  .fade-enter-active, .fade-leave-active {
    transition: opacity .2s;
  }

  .fade-enter, .fade-leave-to {
    opacity: 0;
  }

</style>
