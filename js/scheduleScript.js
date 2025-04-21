function showCases() {
    document.getElementById('cases').classList.remove('hidden');
    document.getElementById('appointments').classList.add('hidden');
    
    document.querySelector('#caseBtn button:nth-child(1)').classList.add('bg-white');
    document.querySelector('#caseBtn button:nth-child(2)').classList.remove('bg-white');
}

function showAppointments() {
    document.getElementById('cases').classList.add('hidden');
    document.getElementById('appointments').classList.remove('hidden');
    
    document.querySelector('#caseBtn button:nth-child(1)').classList.remove('bg-white');
    document.querySelector('#caseBtn button:nth-child(2)').classList.add('bg-white');
}

function openStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.toggle('hidden');
}

function openCaseModal(){ 
    const modal = document.getElementById('addCaseModal');
    modal.classList.toggle('hidden');
}

function closeCaseModal() {
    const modal = document.getElementById('addCaseModal');
    modal.classList.toggle('hidden');
}

function selectStatus(status) {
    console.log('Selected role: ', role);
    document.getElementById('statusModal').classList.add('hidden');
}

function openAppointmentModal() {
    const modal = document.getElementById('appointmentModal');
    modal.classList.toggle('hidden');
}

function closeAppointmentModal() {
    const modal = document.getElementById('appointmentModal');
    modal.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const statusModal = document.getElementById('statusModal');
    const statusBtn = document.querySelector('[onclick = "openStatusModal()"]');
    if(!statusModal.contains(e.target) && !statusBtn.contains(e.target)) {
        statusModal.classList.add('hidden');
    }

    const caseModal = document.getElementById('addCaseModal');
    const caseBtn = document.querySelector('[onclick="openCaseModal()"]');

    // If modal is open and the click is outside of both the modal content and the button
    if (caseModal && !caseModal.classList.contains('hidden')) {
        const modalContent = caseModal.querySelector('.relative'); // or use a more specific selector
        if (!modalContent.contains(e.target) && !caseBtn.contains(e.target)) {
            caseModal.classList.add('hidden');
        }
    }
});