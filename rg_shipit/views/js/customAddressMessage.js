document.addEventListener("DOMContentLoaded", function () {
    const addressForm = document.querySelector('form[name="address"]');
    const phoneField = document.querySelector('input[name="phone"]');

    if (addressForm && phoneField) {
        addressForm.addEventListener('submit', function (event) {
            if (phoneField.value.trim() === "") {
                event.preventDefault(); // Evita el envío del formulario
                alert("El campo teléfono es obligatorio. Por favor, ingrésalo para continuar.");
                phoneField.focus();
            }
        });
    }
});