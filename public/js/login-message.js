document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        if (alert && alert.parentNode) {
            alert.style.animation = 'animate-slide-down 0.4s ease reverse forwards';
            setTimeout(() => alert.remove(), 400);
        }
    }, 5000);
});