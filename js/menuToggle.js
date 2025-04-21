document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if(!e.target.closest('.sidebar') && !e.target.closest('.menu-toggle')) {
            document.querySelector('.sidebar').classList.remove('active');
        }
    });
});