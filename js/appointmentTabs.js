document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.appointment-tabs button');
    const contents = document.querySelectorAll('.appointment-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const target = this.getAttribute('data-tab');
            contents.forEach(content => {
                if (content.id === target) {
                    content.style.display = 'block';
                } else {
                    content.style.display = 'none';
                }
            });
        });
    });
});