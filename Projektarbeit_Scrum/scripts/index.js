const todoForm = document.getElementById('todo-form');
const todoInput = document.getElementById('todo-input');
const todoList = document.getElementById('todo-list');
let todos = JSON.parse(localStorage.getItem('todos')) || [];
let completedTodos = JSON.parse(localStorage.getItem('completedTodos')) || [];

// Lade gespeicherte To-Dos
todos.forEach(addTodoItem);

todoForm.addEventListener('submit', function(event) {
    event.preventDefault();
    const taskText = todoInput.value.trim();
    if (taskText) {
        todos.push(taskText);
        localStorage.setItem('todos', JSON.stringify(todos));
        addTodoItem(taskText);
        todoInput.value = '';
    }
});

function addTodoItem(taskText) {
    const li = document.createElement('li');
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';

    const label = document.createElement('span');
    label.textContent = taskText;

    const actions = document.createElement('div');
    actions.classList.add('actions');

    const editButton = document.createElement('button');
    editButton.textContent = 'Bearbeiten';
    editButton.classList.add('edit-btn');
    editButton.addEventListener('click', function() {
        const newTaskText = prompt('Bearbeiten:', label.textContent);
        if (newTaskText !== null && newTaskText.trim() !== '') {
            label.textContent = newTaskText.trim();
            const index = todos.indexOf(taskText);
            if (index > -1) todos[index] = newTaskText.trim();
            localStorage.setItem('todos', JSON.stringify(todos));
        }
    });

    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Löschen';
    deleteButton.classList.add('delete-btn');
    deleteButton.addEventListener('click', function() {
        todoList.removeChild(li);
        todos = todos.filter(todo => todo !== taskText);
        localStorage.setItem('todos', JSON.stringify(todos));
    });

    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            completedTodos.push(taskText);
            todos = todos.filter(todo => todo !== taskText);
            localStorage.setItem('todos', JSON.stringify(todos));
            localStorage.setItem('completedTodos', JSON.stringify(completedTodos));
            todoList.removeChild(li);
        }
    });

    actions.appendChild(editButton);
    actions.appendChild(deleteButton);

    li.appendChild(checkbox);
    li.appendChild(label);
    li.appendChild(actions);
    todoList.appendChild(li);
}