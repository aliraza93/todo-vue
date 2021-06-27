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
        :key="todo.id"
        :todo="todo"
        :index="index"
        :checkAll="!anyRemaining"
        >
      </todo-item>
    </transition-group>

    <div class="extra-container">
      <todo-check-all :anyRemaining="anyRemaining"></todo-check-all>
      <todo-item-remaining :remaining="remaining"></todo-item-remaining>
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
      filter: 'all',
      beforeEditCache: '',
      todos: [
        {
          'id' : 1,
          'title': 'Finish Vue Screentests',
          'completed': false,
          'editing': false
        },
        {
          'id' : 2,
          'title': 'Take over world',
          'completed': false,
          'editing': false
        }
      ]
    }
  },

  computed: {
    remaining() {
      return this.todos.filter(todo => !todo.completed).length
    },

    anyRemaining() {
      return this.remaining != 0
    },

    todosFiltered() {
      if(this.filter == 'all') {
        return this.todos
      }

      else if(this.filter == 'active') {
        return this.todos.filter(todo => !todo.completed)
      }

      else if(this.filter == 'completed') {
        return this.todos.filter(todo => todo.completed)
      }

      return this.todos
    },

    showClearCompletedButton() {
      return this.todos.filter(todo => todo.completed).length > 0
    }
  },
  
  created() {
    eventBus.$on('removedTodo', (index) => this.removeTodo(index))
    eventBus.$on('finishedEditing', (data) => this.finishedEdit(data))
    eventBus.$on('check-all-changed', (checked) => this.checkAllTodos(checked))
    eventBus.$on('filterChanged', (filter) => this.filter = filter)
    eventBus.$on('clearCompleted', () => this.clearCompleted())
  },

  beforeDestroy() {
    eventBus.$off('removedTodo', (index) => this.removeTodo(index))
    eventBus.$off('finishedEditing', (data) => this.finishedEdit(data))
    eventBus.$off('check-all-changed', (checked) => this.checkAllTodos(checked))
    eventBus.$off('filterChanged', (filter) => this.filter = filter)
    eventBus.$off('clearCompleted', () => this.clearCompleted())
  },

  methods: {
    addTodo() {
      if(this.newTodo.trim().length == 0) {
        return
      }

      this.todos.push({
        id: this.idForTodo,
        title: this.newTodo,
        completed: false,
        editing: false
      })
      
      this.newTodo = ''
      this.idForTodo++
    },

    removeTodo(index) {
      this.todos.splice(index, 1  )
    },

    finishedEdit(data) {
      this.todos.splice(data.index, 1, data.todo)
    },  

    checkAllTodos() {
      this.todos.forEach((todo) => todo.completed = 
      event.target.checked)
    },

    clearCompleted() {
      this.todos = this.todos.filter(todo => !todo.completed)
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
