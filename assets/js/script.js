document.addEventListener('DOMContentLoaded', function() {
    const demoForm = document.getElementById('demoForm');
    
    demoForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Basic form validation
        const requiredFields = demoForm.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });
        
        if (isValid) {
            // In a real implementation, you would send the form data to your server here
            // For example using fetch API:
            /*
            fetch('your-server-endpoint', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(new FormData(demoForm))),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                showSuccessMessage();
                demoForm.reset();
            })
            .catch(error => {
                console.error('Error:', error);
            });
            */
            
            // For demonstration purposes:
            showSuccessMessage();
            demoForm.reset();
        }
    });
    
    function showSuccessMessage() {
        alert('Thank you for your interest! Our team will contact you shortly to schedule your demo.');
    }
});
