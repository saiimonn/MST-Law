const menuToggle = document.querySelector('.menu-toggle');
const leftNav = document.querySelector('.left');
const rightNav = document.querySelector('.right');

menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    leftNav.classList.toggle('active');
    rightNav.classList.toggle('active');
});

// Close menu when clicking outside
document.addEventListener('click', (e) => {
    if (!menuToggle.contains(e.target) && 
        !leftNav.contains(e.target) && 
        !rightNav.contains(e.target)) {
        menuToggle.classList.remove('active');
        leftNav.classList.remove('active');
        rightNav.classList.remove('active');
    }
});