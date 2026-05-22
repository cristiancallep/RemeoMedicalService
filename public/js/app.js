document.addEventListener('DOMContentLoaded', function () {
    // Validación de formulario de login
    const loginForm = document.querySelector('#login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            const email = document.querySelector('#email').value.trim();
            const password = document.querySelector('#password').value.trim();

            if (!email || !password) {
                event.preventDefault();
                alert('Por favor completa todos los campos.');
            }
        });
    }

    // Ocultar alertas después de 5 segundos
    const alerts = document.querySelectorAll('[style*="background-color: #f8d7da"]');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.display = 'none';
        }, 5000);
    });

    // Confirmación de acciones destructivas
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            if (!confirm(this.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });
});
