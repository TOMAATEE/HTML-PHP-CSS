const todoList = document.getElementById('completed-todo-list');

// Lade gespeicherte To-Dos aus der Datenbank
fetch('/Projektarbeit_Scrum/scripts/todos.php', { method: 'GET' }) // Abrufen aller To-Dos
    .then(response => response.json())
    .then(todos => {
        todos.sort(function(a, b){return a.priority - b.priority});
        todos.forEach(addTodoItem); // Existierende To-Dos zur Liste hinzufügen
    })
    .catch(error => console.error('Fehler beim Laden der To-Dos:', error));

// Funktion: To-Do zur Liste hinzufügen
function addTodoItem(todo) {
    if (todo.erledigt == 0) {
        return;
    }
    const li = document.createElement('li');
    const itemWrapper = document.createElement('div');
    itemWrapper.classList.add('todo-item-wrapper');

    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.checked = true;

    const prio_label = document.createElement('span');
    prio_label.textContent = todo.priority;
    prio_label.classList.add('prio-label');

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
            body: JSON.stringify({ erledigt: checkbox.checked ? 0 : 1 })
        })
        .catch(error => console.error('Fehler beim Aktualisieren des Status:', error));
    });

    actions.appendChild(editButton);
    actions.appendChild(deleteButton);

    itemWrapper.appendChild(checkbox);
    itemWrapper.appendChild(prio_label);
    li.appendChild(itemWrapper);
    li.appendChild(label);
    li.appendChild(actions);
    todoList.appendChild(li);
}
