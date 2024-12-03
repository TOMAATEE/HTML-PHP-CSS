const completedTodoList = document.getElementById('completed-todo-list');
const completedTodos = JSON.parse(localStorage.getItem('completedTodos')) || [];

completedTodos.forEach(task => {
    const li = document.createElement('li');
    li.textContent = task;

    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Löschen';
    deleteButton.classList.add('delete-btn');
    deleteButton.addEventListener('click', function() {
        completedTodoList.removeChild(li);
        const index = completedTodos.indexOf(task);
        if (index > -1) {
            completedTodos.splice(index, 1);
            localStorage.setItem('completedTodos', JSON.stringify(completedTodos));
        }
    });

    li.appendChild(deleteButton);
    completedTodoList.appendChild(li);
});