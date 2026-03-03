 // Temps d'affichage du message d'erreur, succes, avertissement
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});