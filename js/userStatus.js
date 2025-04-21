function openRoleModal() {
    const modal = document.getElementById('roleModal');
    modal.classList.toggle('hidden');
}

function openStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.toggle('hidden');
}

function selectRole(role) {
    console.log('Selected role:', role);
    document.getElementById('roleModal').classList.add('hidden');
}

document.addEventListener('click', function(e) {
    const roleModal = document.getElementById('roleModal');
    const button = document.querySelector('[onclick = "openRoleModal()"]');
    if(!roleModal.contains(e.target) && !button.contains(e.target)) {
        roleModal.classList.add('hidden');
    }

    const statusModal = document.getElementById('statusModal');
    const statusBtn = document.querySelector('[onclick = "openStatusModal()"]');
    if(!statusModal.contains(e.target) && !statusBtn.contains(e.target)) {
        statusModal.classList.add('hidden');
    }
});


function selectRole(role) {
    
    document.getElementById('roleModal').classList.add('hidden');
    
    var selectedRole = role.toLowerCase();
    
    var rows = document.querySelectorAll('.user-row');
    rows.forEach(function(row) {
        var userRole = row.getAttribute('data-role').toLowerCase();
        if (selectedRole === 'all roles' || userRole === selectedRole) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function selectStatus(status) {
    document.getElementById('statusModal').classList.add('hidden');
    var selectedStatus = status.toLowerCase();
    var rows = document.querySelectorAll('.user-row');
    rows.forEach(function(row) {
        var userStatus = row.querySelector('.col-span-2:last-child div').textContent.trim().toLowerCase();
        if (selectedStatus === 'all statuses' || userStatus === selectedStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}


//AJAX searchbar
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.querySelector('input[placeholder="Search by name"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var query = this.value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/FinalProject/adminPages/searchUsers.php?q=' + encodeURIComponent(query), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText); // Add this line
                    var container = document.getElementById('user-table-container');
                    if (container) {
                        var header = container.querySelector('.grid.font-bold');
                        container.innerHTML = '';
                        if (header) container.appendChild(header);
                        container.innerHTML += xhr.responseText;
                    }
                }
            };
            xhr.send();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('role-select')) {
            var userId = e.target.getAttribute('data-user-id');
            var newRole = e.target.value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/FinalProject/adminPages/updateUserRole.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        
                    } else {
                        alert('Failed to update role');
                    }
                }
            };
            xhr.send('user_id=' + encodeURIComponent(userId) + '&role=' + encodeURIComponent(newRole));
        }
    });
});
