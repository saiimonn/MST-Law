function showOpenCases() {
    document.getElementById('openCasesTable').classList.remove('hidden');
    document.getElementById('closedCasesTable').classList.add('hidden');
    document.getElementById('openCasesBtn').classList.re.gemove('bg-gray-200', 'text-gray-600');
    document.getElementById('openCasesBtn').classList.add('bg-white', 'text-black');
    document.getElementById('closedCasesBtn').classList.remove('bg-white', 'text-black');
    document.getElementById('closedCasesBtn').classList.add('bg-gray-200', 'text-gray-600');
}

function showClosedCases() {
    document.getElementById('openCasesTable').classList.add('hidden');
    document.getElementById('closedCasesTable').classList.remove('hidden');
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

// Set initial state when page loads
document.addEventListener('DOMContentLoaded', function() {
    showOpenCases();
    
    // Add event listener to the form
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
    // If event is passed, prevent default behavior
    if (event) {
        event.stopPropagation();
    }
    
    const dropdown = document.getElementById(`dropdown-${id}`);
    
    // Add transition classes if they don't exist
    if (!dropdown.classList.contains('transition-all')) {
        dropdown.classList.add('transition-all', 'duration-200', 'ease-in-out', 'transform');
    }
    
    // Toggle between hidden/visible with animation
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        dropdown.classList.add('opacity-0', 'scale-95');
        
        // Force a reflow to ensure the transition works
        dropdown.offsetHeight;
        
        // Show with animation
        dropdown.classList.remove('opacity-0', 'scale-95');
        dropdown.classList.add('opacity-100', 'scale-100');
    } else {
        // Hide with animation
        dropdown.classList.remove('opacity-100', 'scale-100');
        dropdown.classList.add('opacity-0', 'scale-95');
        
        // After animation completes, add hidden class
        setTimeout(() => {
            dropdown.classList.add('hidden');
        }, 200); // Match this with the duration-200 class
    }
    
    // Close other open dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
        if (el.id !== `dropdown-${id}` && !el.classList.contains('hidden')) {
            el.classList.remove('opacity-100', 'scale-100');
            el.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                el.classList.add('hidden');
            }, 200);
        }
    });
    
    // Close dropdown when clicking outside
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