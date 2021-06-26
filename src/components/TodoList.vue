<template>
  <div class="container">
    <input type="text" v-model="newTodo" @keyup.enter="addTodo" class="todo-input" placeholder="What needs to be done">
    
    <transition-group
      name="fade"
      enter-active-class="animate__fadeInUp"
      leave-active-class="animate__fadeInDown"
      >
      <div v-for="(todo, index) in todosFiltered" :key="todo.id" class="todo-item">
        <div class="todo-item-left">
          <input type="checkbox" v-model="todo.completed">
          <div 
              v-if="!todo.editing" 
              @dblclick="editTodo(todo)" 
              class="todo-item-label"
              :class="todo.completed ? 'completed' : ''"
              >{{ todo.title }}</div>
          <input v-else type="text" 
                v-focus 
                @blur="doneEditing(todo)" 
                @keyup.enter="doneEditing(todo)" 
                class="todo-item-edit " 
                v-model="todo.title"
                @keyup.esc="cancelEdit(todo)"
                >
        </div>  
        <div class="remove-item" @click="removeTodo(index)">
          &times;
        </div>
      </div>
    </transition-group>

    <div class="extra-container">
      <div>
        <label>
          <input type="checkbox" 
                :checked="!anyRemaining"
                @change="checkAllTodos"
                >Check All
        </label>
      </div>
      <div>{{ remaining }} items left</div>
    </div>

    <div class="extra-container">
      <div>
        <button
          :class="{ active: filter == 'all' }"
          @click="filter = 'all'"
          >
          All
        </button>
        <button
          :class="{ active: filter == 'active' }"
          @click="filter = 'active'"
          >
          Active
        </button>
        <button
          :class="{ active: filter == 'completed' }"
          @click="filter = 'completed'"
          >
          Completed
        </button>
      </div>
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
    </div>

  </div>
</template>

<script>
export default {
  name: 'TodoList',
  data () {
    return {
      newTodo: '',
      idForTodo: 3,
      beforeEditCache: '',
      filter: 'all',
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

  directives: {
    focus: {
      inserted: function(el) {
        el.focus()
      }
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

    editTodo(todo) {
      this.beforeEditCache = todo.title
      todo.editing = true
    },

    doneEditing(todo) {
      if(todo.title.trim() == '') {
        todo.title = this.beforeEditCache
      }
      todo.editing = false
    },

    cancelEdit(todo) {
      todo.title = this.beforeEditCache
      todo.editing = false
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
