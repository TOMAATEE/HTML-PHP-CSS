const todoForm = document.getElementById('todo-form');
const todoInput = document.getElementById('todo-input');
const todoList = document.getElementById('todo-list');

// Lade gespeicherte To-Dos aus der Datenbank
fetch('/Projektarbeit_Scrum/scripts/todos.php', { method: 'GET' }) // Abrufen aller To-Dos
    .then(response => response.json())
    .then(todos => {
        todos.forEach(addTodoItem); // Existierende To-Dos zur Liste hinzufügen
    })
    .catch(error => console.error('Fehler beim Laden der To-Dos:', error));


// To-Do-Formular absenden
todoForm.addEventListener('submit', function(event) {
    event.preventDefault();
    const taskText = todoInput.value.trim();
    if (taskText) {
        // To-Do in der Datenbank speichern
        fetch('/Projektarbeit_Scrum/scripts/todos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ beschreibung: taskText, erledigt: false, priority: 1 }) // Beispielwerte
        })
        .then(response => response.json())
        .then(newTodo => {
            addTodoItem(newTodo);
            todoInput.value = '';
        })
        .catch(error => console.error('Fehler beim Speichern des To-Dos:', error));
    }
});

// Unterstützt Enter-Tastendruck
todoInput.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        todoForm.dispatchEvent(new Event('submit'));
    }
});

// Funktion: To-Do zur Liste hinzufügen
function addTodoItem(todo) {
    const li = document.createElement('li');
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.checked = todo.erledigt;

    const label = document.createElement('span');
    label.textContent = todo.beschreibung;

    const actions = document.createElement('div');
    actions.classList.add('actions');

    const editButton = document.createElement('button');
    editButton.textContent = 'Bearbeiten';
    editButton.classList.add('edit-btn');
    editButton.addEventListener('click', function() {
        const newTaskText = prompt('Bearbeiten:', label.textContent);
        if (newTaskText !== null && newTaskText.trim() !== '') {
            fetch(`/Projektarbeit_Scrum/scripts/todos.php/${todo.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ beschreibung: newTaskText.trim() })
            })
            .then(response => response.json())
            .then(updatedTodo => {
                label.textContent = updatedTodo.beschreibung;
            })
            .catch(error => console.error('Fehler beim Bearbeiten des To-Dos:', error));
        }
    });

    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Löschen';
    deleteButton.classList.add('delete-btn');
    deleteButton.addEventListener('click', function() {
        fetch(`/Projektarbeit_Scrum/scripts/todos.php/${todo.id}`, { method: 'DELETE' })
            .then(() => {
                todoList.removeChild(li);
            })
            .catch(error => console.error('Fehler beim Löschen des To-Dos:', error));
    });

    checkbox.addEventListener('change', function() {
        fetch(`/Projektarbeit_Scrum/scripts/todos.php/${todo.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ erledigt: checkbox.checked })
        })
        .catch(error => console.error('Fehler beim Aktualisieren des Status:', error));
    });

    actions.appendChild(editButton);
    actions.appendChild(deleteButton);

    li.appendChild(checkbox);
    li.appendChild(label);
    li.appendChild(actions);
    todoList.appendChild(li);
}
