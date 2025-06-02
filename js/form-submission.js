document.addEventListener('DOMContentLoaded', function() {
    const devisForm = document.getElementById('devis-form');
    const successMessageDiv = document.getElementById('form-success-message');
    const errorMessageDiv = document.getElementById('form-error-message');

    if (devisForm) {
        devisForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default HTML form submission

            // Hide previous messages
            if (successMessageDiv) successMessageDiv.style.display = 'none';
            if (errorMessageDiv) errorMessageDiv.style.display = 'none';

            const formData = new FormData(devisForm);
            const formAction = devisForm.getAttribute('action') || window.location.pathname; // Default to current page if action is not set

            // Log FormData contents for debugging (optional)
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch(formAction, {
                method: 'POST',
                body: formData,
                Headers might be needed depending on server setup, e.g.,
                headers: {
                  'X-Requested-With': 'XMLHttpRequest' // To identify AJAX requests on the server
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Try to get error message from response body
                    return response.text().then(text => {
                        throw new Error('Network response was not ok. Status: ' + response.status + '. Message: ' + (text || 'No error message from server.'));
                    });
                }
                return response.text(); // Or response.json() if the server sends JSON
            })
            .then(data => {
                if (successMessageDiv) {
                    successMessageDiv.textContent = 'Votre demande a été envoyée avec succès !'; // Or use a message from `data` if provided
                    successMessageDiv.style.display = 'block';
                }
                devisForm.reset(); // Clear the form
            })
            .catch(error => {
                console.error('Error submitting form:', error);
                if (errorMessageDiv) {
                    errorMessageDiv.textContent = 'Une erreur s'est produite: ' + error.message;
                    errorMessageDiv.style.display = 'block';
                }
            });
        });
    }
});
