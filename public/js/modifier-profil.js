document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.edit-input');
    const buttons = document.querySelectorAll('.edit-btn');

    // Effet ripple sur les inputs
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });

        input.addEventListener('blur', function() {
            if(!this.value) {
                this.style.background = '#f9f9f9';
            }
        });
    });

    // Ripple effect sur les boutons
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.width = '10px';
            ripple.style.height = '10px';
            ripple.style.background = 'rgba(255, 255, 255, 0.6)';
            ripple.style.borderRadius = '50%';
            ripple.style.pointerEvents = 'none';
            ripple.style.transform = 'translate(-50%, -50%)';

            const animation = ripple.animate([
                { transform: 'translate(-50%, -50%) scale(1)', opacity: 1 },
                { transform: 'translate(-50%, -50%) scale(30)', opacity: 0 }
            ], {
                duration: 600,
                easing: 'ease-out'
            });

            this.style.position = 'relative';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});