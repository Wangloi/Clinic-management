document.querySelector('button').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('hidden');
    document.querySelector('.sidebar').classList.toggle('fixed');
    document.querySelector('.sidebar').classList.toggle('z-40');
    document.querySelector('.sidebar').classList.toggle('w-[280px]');
});