function showOpenCases() {
    document.getElementById('openCasesTable').classList.toggle('hidden');
    document.getElementById('closedCasesTable').classList.toggle('hidden');
    document.getElementById('openCasesBtn').classList.remove('bg-gray-200', 'text-gray-600');
    document.getElementById('openCasesBtn').classList.add('bg-white', 'text-black');
    document.getElementById('closedCasesBtn').classList.remove('bg-white', 'text-black');
    document.getElementById('closedCasesBtn').classList.add('bg-gray-200', 'text-gray-600');
}

function showClosedCases() {
    document.getElementById('openCasesTable').classList.toggle('hidden');
    document.getElementById('closedCasesTable').classList.toggle('hidden');
    document.getElementById('closedCasesBtn').classList.remove('bg-gray-200', 'text-gray-600');
    document.getElementById('closedCasesBtn').classList.add('bg-white', 'text-black');
    document.getElementById('openCasesBtn').classList.remove('bg-white', 'text-black');
    document.getElementById('openCasesBtn').classList.add('bg-gray-200', 'text-gray-600');
}

function openModal() {
    document.getElementById('newCaseModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('newCaseModal').classList.toggle('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    showOpenCases();
    
    const form = document.getElementById('addCaseForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const formData = new FormData(this);
            
            fetch('../includes/addCase.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received', response);
                return response.json();
            })
            .then(data => {
                console.log('Data received', data);
                if (data.status === 'success') {
                    alert('Case added successfully!');
                    closeModal();
                    location.reload();
                } else {
                    alert('Error adding case: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding case: ' + error);
            });
        });
    } else {
        console.error('Form element not found');
    }
});

function toggleDropdown(id, event) {
    if (event) {
        event.stopPropagation();
    }
    
    const dropdown = document.getElementById(`dropdown-${id}`);
    
    if (!dropdown.classList.contains('transition-all')) {
        dropdown.classList.add('transition-all', 'duration-200', 'ease-in-out', 'transform');
    }
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        dropdown.classList.add('opacity-0', 'scale-95');
        
        dropdown.offsetHeight;
        
        dropdown.classList.remove('opacity-0', 'scale-95');
        dropdown.classList.add('opacity-100', 'scale-100');
    } else {
        dropdown.classList.remove('opacity-100', 'scale-100');
        dropdown.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            dropdown.classList.add('hidden');
        }, 200);
    }
    
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
        if (el.id !== `dropdown-${id}` && !el.classList.contains('hidden')) {
            el.classList.remove('opacity-100', 'scale-100');
            el.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                el.classList.add('hidden');
            }, 200);
        }
    });
    
    document.addEventListener('click', function closeDropdown(e) {
        if (!e.target.closest(`#dropdown-${id}`) && !e.target.closest(`button[onclick="toggleDropdown(${id})"]`)) {
            dropdown.classList.remove('opacity-100', 'scale-100');
            dropdown.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                dropdown.classList.add('hidden');
                document.removeEventListener('click', closeDropdown);
            }, 200);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // AJAX search by client name
    var searchInput = document.querySelector('input[placeholder="Search by client name"]');
    var openCasesTableBody = document.querySelector('#openCasesTable tbody');
    var closedCasesTableBody = document.querySelector('#closedCasesTable tbody');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var query = this.value;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/FinalProject/lawyerPages/searchCase.php?q=' + encodeURIComponent(query), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (openCasesTableBody && typeof data.open !== "undefined") {
                            openCasesTableBody.innerHTML = data.open;
                        }
                        if (closedCasesTableBody && typeof data.closed !== "undefined") {
                            closedCasesTableBody.innerHTML = data.closed;
                        }
                    } catch (e) {
                        // fallback: just update open cases
                        if (openCasesTableBody) {
                            openCasesTableBody.innerHTML = xhr.responseText;
                        }
                    }
                }
            };
            xhr.send();
        });
    }
});