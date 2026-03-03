// Fermer les alertes au clic sur le bouton
document.querySelectorAll('.alert-close').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.alert').style.animation = 'animate-slide-down 0.4s ease reverse forwards';
        setTimeout(() => this.closest('.alert').remove(), 400);
    });
});

// Fermer automatiquement les alertes après 5 secondes
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        if (alert && alert.parentNode) {
            alert.style.animation = 'animate-slide-down 0.4s ease reverse forwards';
            setTimeout(() => alert.remove(), 400);
        }
    }, 5000);
});