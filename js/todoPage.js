// Initialize the page with allTasks button active
document.addEventListener('DOMContentLoaded', function() {
    showAllTasks();
    
    // Add deadline button functionality
    const deadlineBtn = document.getElementById('deadlineBtn');
    const calendarContainer = document.getElementById('calendarContainer');
    
    deadlineBtn.addEventListener('click', function() {
        calendarContainer.classList.toggle('hidden');
    });
    
    // Load and display tasks
    loadTasks();
    
    // Add form submission handler to update counts after adding a task
    const taskForm = document.querySelector('form');
    if (taskForm) {
        taskForm.addEventListener('submit', function() {
            // We'll update counts after the form is submitted and page reloads
            // But we could also use AJAX to submit the form without page reload
            localStorage.setItem('refreshTaskCounts', 'true');
        });
    }
});

// Function to load tasks from the server
function loadTasks() {
    fetch('../includes/getTasks.php')
        .then(response => response.json())
        .then(data => {
            renderTasks(data);
            updateTaskCounts();
        })
        .catch(error => {
            console.error('Error loading tasks:', error);
        });
}

// Function to update task counts
function updateTaskCounts() {
    fetch('../includes/getTaskCounts.php')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.bg-blue-100 + div p.text-xl').textContent = data.today + ' tasks';
            document.querySelector('.bg-orange-100 + div p.text-xl').textContent = data.upcoming + ' tasks';
            document.querySelector('.bg-red-100 + div p.text-xl').textContent = data.overdue + ' tasks';
        })
        .catch(error => {
            console.error('Error updating task counts:', error);
        });
}

// Function to render tasks in the UI
function renderTasks(tasks) {
    const taskContainer = document.getElementById('taskContainer');
    
    if (!taskContainer) {
        console.error('Task container not found');
        return;
    }
    
    // Clear existing tasks
    taskContainer.innerHTML = '';
    
    if (tasks.length === 0) {
        taskContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12">
                <div class="rounded-full bg-gray-100 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>                              
                </div>
                <h3 class="mt-2 text-s font-medium text-gray-900">No tasks</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding a task</p>
            </div>
        `;
        return;
    }
    
    // Create task list
    tasks.forEach(task => {
        const taskElement = document.createElement('div');
        taskElement.className = 'task-item flex items-start justify-between p-4 hover:bg-gray-50 border-b border-gray-200';
        taskElement.dataset.status = task.status;
        
        // Format deadline if exists
        let deadlineHtml = '';
        if (task.deadline) {
            const deadlineDate = new Date(task.deadline);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const isOverdue = deadlineDate < today;
            
            const formattedDate = deadlineDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
            
            const deadlineClass = isOverdue ? 'text-red-600' : 'text-gray-500';
            deadlineHtml = `<span class="${deadlineClass} text-sm ml-2">${formattedDate}</span>`;
        }
        
        // Create task HTML
        taskElement.innerHTML = `
            <div class="flex items-start">
                <input type="checkbox" class="task-checkbox mt-1 h-4 w-4 rounded border-gray-300" 
                    ${task.status === 'completed' ? 'checked' : ''} data-task-id="${task.id}">
                <div class="ml-3">
                    <p class="text-sm font-medium ${task.status === 'completed' ? 'line-through text-gray-500' : 'text-gray-900'}">${task.title}</p>
                    ${task.details ? `<p class="mt-1 text-sm text-gray-500">${task.details}</p>` : ''}
                    ${deadlineHtml}
                </div>
            </div>
            <div>
                <button class="delete-task text-gray-400 hover:text-red-500" data-task-id="${task.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </button>
            </div>
        `;
        
        taskContainer.appendChild(taskElement);
    });
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.task-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            updateTaskStatus(taskId, this.checked ? 'completed' : 'active');
        });
    });
    
    // Add event listeners to delete buttons
    document.querySelectorAll('.delete-task').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            deleteTask(taskId);
        });
    });
}

// Function to update task status
function updateTaskStatus(taskId, status) {
    fetch('../includes/updateTaskStatus.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            loadTasks();
            updateTaskCounts(); // Update counts after status change
        } else {
            console.error('Error updating task:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to delete task
function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        fetch('../includes/deleteTask.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `task_id=${taskId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadTasks();
                updateTaskCounts(); // Update counts after deletion
            } else {
                console.error('Error deleting task:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function showAllTasks() {
    // Update button styling
    document.getElementById('allTasks').classList.add('bg-black', 'text-white');
    document.getElementById('allTasks').classList.remove('bg-gray-200', 'text-gray-600');
    
    document.getElementById('active').classList.add('bg-gray-200', 'text-gray-600');
    document.getElementById('active').classList.remove('bg-black', 'text-white');
    
    document.getElementById('completed').classList.add('bg-gray-200', 'text-gray-600');
    document.getElementById('completed').classList.remove('bg-black', 'text-white');
    
    // Show all tasks
    document.querySelectorAll('.task-item').forEach(task => {
        task.style.display = 'flex';
    });
}

function showActive() {
    // Update button styling
    document.getElementById('allTasks').classList.add('bg-gray-200', 'text-gray-600');
    document.getElementById('allTasks').classList.remove('bg-black', 'text-white');
    
    document.getElementById('active').classList.add('bg-black', 'text-white');
    document.getElementById('active').classList.remove('bg-gray-200', 'text-gray-600');
    
    document.getElementById('completed').classList.add('bg-gray-200', 'text-gray-600');
    document.getElementById('completed').classList.remove('bg-black', 'text-white');
    
    // Show only active tasks
    document.querySelectorAll('.task-item').forEach(task => {
        if (task.dataset.status === 'active') {
            task.style.display = 'flex';
        } else {
            task.style.display = 'none';
        }
    });
}

function showCompleted() {
    // Update button styling
    document.getElementById('allTasks').classList.add('bg-gray-200', 'text-gray-600');
    document.getElementById('allTasks').classList.remove('bg-black', 'text-white');
    
    document.getElementById('active').classList.add('bg-gray-200', 'text-gray-600');
    document.getElementById('active').classList.remove('bg-black', 'text-white');
    
    document.getElementById('completed').classList.add('bg-black', 'text-white');
    document.getElementById('completed').classList.remove('bg-gray-200', 'text-gray-600');
    
    // Show only completed tasks
    document.querySelectorAll('.task-item').forEach(task => {
        if (task.dataset.status === 'completed') {
            task.style.display = 'flex';
        } else {
            task.style.display = 'none';
        }
    });
}