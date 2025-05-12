/**
 * AI Solutions - Main JavaScript
 * Contains all interactive functionality for the website
 * Last Updated: March 23, 2025
 */

// Global confirmation functions for different entity types
window.confirmDeleteStaff = function(formId) {
  if (confirm('Are you sure you want to delete this staff member?')) {
    document.getElementById(formId).submit();
  }
  return false;
};

window.confirmDeleteFAQ = function(formId) {
  if (confirm('Are you sure you want to delete this FAQ?')) {
    document.getElementById(formId).submit();
  }
  return false;
};

window.confirmDeleteEvent = function(formId) {
  if (confirm('Are you sure you want to delete this event?')) {
    document.getElementById(formId).submit();
  }
  return false;
};

window.confirmDeleteDemo = function(formId) {
  if (confirm('Are you sure you want to delete this demo?')) {
    document.getElementById(formId).submit();
  }
  return false;
};

document.addEventListener('DOMContentLoaded', function() {
  // Initialize all UI components
  initNavbar();
  initLoginValidation();
  initFAQAccordion();
  initEventRegistration();
  initDemoForm();
  initManageFeedback();
  initManageStaff();
  initManageFAQ();
  initManageEvents();
  initManageDemos();
  
  console.log('All scripts initialized');
});

/**
 * Navbar toggle functionality
 */
function initNavbar() {
  const navbarToggle = document.getElementById('navbar-toggle');
  const navbarMenu = document.getElementById('navbar-menu');
  
  if (navbarToggle && navbarMenu) {
    navbarToggle.addEventListener('click', function() {
      navbarMenu.classList.toggle('hidden');
    });
  }
}

/**
 * Login form validation
 */
function initLoginValidation() {
  const loginForm = document.getElementById('loginForm');
  
  if (loginForm) {
    loginForm.addEventListener('submit', function(event) {
      const emailField = document.getElementById('email');
      const passwordField = document.getElementById('password');
      let isValid = true;
      
      if (!emailField.value.trim()) {
        emailField.classList.add('ring-2', 'ring-red-500');
        isValid = false;
      } else {
        emailField.classList.remove('ring-2', 'ring-red-500');
      }
      
      if (!passwordField.value.trim()) {
        passwordField.classList.add('ring-2', 'ring-red-500');
        isValid = false;
      } else {
        passwordField.classList.remove('ring-2', 'ring-red-500');
      }
      
      if (!isValid) {
        event.preventDefault();
        alert('Please fill in all required fields');
      }
    });
  }
}

/**
 * FAQ accordion functionality
 */
function initFAQAccordion() {
  // Define global function used by the FAQ page
  window.toggleAnswer = function(element) {
    // Toggle the active class on the question
    element.classList.toggle('bg-gray-50');
    element.classList.toggle('bg-blue-50');
    
    // Toggle the icon
    const icon = element.querySelector('.toggle-icon');
    if (icon.textContent === '+') {
      icon.textContent = '−';
    } else {
      icon.textContent = '+';
    }
    
    // Toggle the answer visibility
    const answer = element.nextElementSibling;
    if (answer.style.maxHeight) {
      answer.style.maxHeight = null;
    } else {
      answer.style.maxHeight = answer.scrollHeight + "px";
    }
  };
}

/**
 * Event registration functionality
 */
function initEventRegistration() {
  console.log('Event registration initialized');
  
  // Make function globally available for inline onclick handlers
  window.registerForEvent = function(eventId, eventTitle) {
    const eventIdInput = document.getElementById('event');
    const eventTitleInput = document.getElementById('event_title');
    const registrationFormContainer = document.getElementById('registrationFormContainer');
    
    if (eventIdInput && registrationFormContainer) {
      // Update form fields
      eventIdInput.value = eventId;
      if (eventTitleInput) {
        eventTitleInput.value = eventTitle;
      }
      
      // Update the heading to show the event title
      const formHeading = document.querySelector('#registrationFormContainer h2');
      if (formHeading) {
        formHeading.textContent = 'Register for ' + eventTitle;
      }
      
      // Show registration form
      registrationFormContainer.classList.remove('hidden');
      registrationFormContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      console.error('Registration form elements not found');
    }
  };
  
  // Setup cancel registration button
  setupCancelRegistration();
  
  // Handle any registration errors when page loads
  handleRegistrationErrors();
}

/**
 * Setup cancel registration button
 */
function setupCancelRegistration() {
  const cancelRegistrationBtn = document.getElementById('cancelRegistration');
  const registrationFormContainer = document.getElementById('registrationFormContainer');
  
  if (cancelRegistrationBtn && registrationFormContainer) {
    cancelRegistrationBtn.addEventListener('click', function() {
      // Hide registration form
      registrationFormContainer.classList.add('hidden');
      
      // Scroll back to events list
      document.querySelector('h1').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }
}

/**
 * Handle registration errors, particularly duplicate registrations
 */
function handleRegistrationErrors() {
  // Check for errors on page load and ensure form display is correct
  const errorContainer = document.querySelector('.bg-red-50.border-l-4.border-red-500');
  const eventIdInput = document.getElementById('event');
  const eventTitleInput = document.getElementById('event_title');
  
  // If we have errors, make sure heading is correct
  if (errorContainer && eventIdInput && eventIdInput.value) {
    // Find the form heading
    const formHeading = document.querySelector('#registrationFormContainer h2');
    
    // If heading shows "Register for [number]", fix it
    if (formHeading && formHeading.textContent.match(/Register for \d+$/)) {
      const eventTitle = eventTitleInput ? eventTitleInput.value : 'Event Registration';
      formHeading.textContent = eventTitle ? 'Register for ' + eventTitle : 'Event Registration';
    }
    
    // Make sure error is visible
    const registrationFormContainer = document.getElementById('registrationFormContainer');
    if (registrationFormContainer) {
      registrationFormContainer.classList.remove('hidden');
      
      // Scroll to error message
      errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
}

/**
 * Demo form validation and functionality
 */
function initDemoForm() {
  const demoForm = document.getElementById('demoForm');
  const promoButton = document.querySelector(".bg-white.hover\\:bg-gray-100");
  
  if (promoButton) {
    promoButton.addEventListener("click", function() {
      window.location.href = "events.php";
    });
  }
  
  if (demoForm) {
    demoForm.addEventListener("submit", function(event) {
      // Reset previous error states
      const errorFeedbacks = document.querySelectorAll(".error-feedback");
      errorFeedbacks.forEach(element => element.remove());
      
      const errorFields = document.querySelectorAll(".error");
      errorFields.forEach(field => field.classList.remove("error"));
      
      // Check required fields
      let isValid = true;
      
      // Validate required fields
      ["name", "lastname", "email", "company", "country", "interests"].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && !field.value.trim()) {
          field.classList.add("ring-2", "ring-red-500");
          
          const errorDiv = document.createElement("div");
          errorDiv.className = "error-feedback text-red-500 text-sm mt-1";
          errorDiv.textContent = "This field is required";
          field.parentNode.appendChild(errorDiv);
          
          isValid = false;
        }
      });
      
      // Validate email format
      const emailField = document.getElementById("email");
      if (emailField && emailField.value.trim() && !isValidEmail(emailField.value.trim())) {
        emailField.classList.add("ring-2", "ring-red-500");
        
        const errorDiv = document.createElement("div");
        errorDiv.className = "error-feedback text-red-500 text-sm mt-1";
        errorDiv.textContent = "Please enter a valid email address";
        emailField.parentNode.appendChild(errorDiv);
        
        isValid = false;
      }
      
      // Stop form submission if validation fails
      if (!isValid) {
        event.preventDefault();
      }
    });
  }
}

/**
 * Manage Feedback page modal functionality
 */
function initManageFeedback() {
  const modal = document.getElementById('feedbackModal');
  const closeModalBtn = document.getElementById('closeModal');
  
  if (modal && closeModalBtn) {
    // Global function to show feedback details in modal
    window.showFeedbackDetails = function(id, name, email, phone, type, rating, feedback) {
      // Populate modal with feedback details
      document.getElementById('modalTitle').textContent = `Feedback #${id} Details`;
      document.getElementById('modalName').textContent = name;
      document.getElementById('modalEmail').textContent = email;
      document.getElementById('modalPhone').textContent = phone || 'No phone provided';
      document.getElementById('modalType').textContent = type;
      document.getElementById('modalRating').innerHTML = rating > 0 
        ? generateStarRating(rating) 
        : '<span class="text-gray-400 text-sm">Not rated</span>';
      document.getElementById('modalFeedback').textContent = feedback;
      
      // Show modal
      modal.classList.remove('hidden');
    };
    
    // Close modal
    closeModalBtn.addEventListener('click', function() {
      modal.classList.add('hidden');
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
      if (event.target === modal) {
        modal.classList.add('hidden');
      }
    });
  }
  
  // Helper function to generate star rating HTML
  function generateStarRating(rating) {
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
      if (i <= rating) {
        starsHtml += '<svg class="w-5 h-5 fill-current text-yellow-400" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>';
      } else {
        starsHtml += '<svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>';
      }
    }
    return starsHtml;
  }
}

/**
 * Manage Staff page functionality
 */
function initManageStaff() {
  const staffForm = document.getElementById('staffForm');
  const staffFormTitle = document.getElementById('staff-form-title');
  const staffIdField = document.getElementById('staff_id');
  const firstNameField = document.getElementById('firstname');
  const lastNameField = document.getElementById('surname');
  const genderField = document.getElementById('gender');
  const dobField = document.getElementById('dob');
  const emailField = document.getElementById('email');
  const passwordField = document.getElementById('password');
  const confirmPasswordField = document.getElementById('confirm_password');
  const roleField = document.getElementById('roleid');
  const countryField = document.getElementById('countryid');
  const resetFormBtn = document.getElementById('reset-form');
  
  // Handle edit button clicks
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
      const staffId = this.getAttribute('data-id');
      
      // Redirect to the same page with ID parameter
      window.location.href = `?id=${staffId}`;
    });
  });
  
  // Set minimum date for DOB field (after 1900)
  if (dobField) {
    dobField.setAttribute('min', '1901-01-01');
    
    // Add event listener to validate the date on change
    dobField.addEventListener('change', function() {
      const selectedDate = new Date(this.value);
      const birthYear = selectedDate.getFullYear();
      const today = new Date();
      
      // Calculate age
      let age = today.getFullYear() - birthYear;
      const m = today.getMonth() - selectedDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < selectedDate.getDate())) {
        age--;
      }
      
      // Validate birth year is after 1900
      if (birthYear <= 1900) {
        alert('Birth year must be after 1900!');
        this.value = '';
        this.focus();
        return;
      }
      
      // Validate minimum age of 16
      if (age < 16) {
        alert('Staff members must be at least 16 years old!');
        this.value = '';
        this.focus();
        return;
      }
    });
  }
  
  // Handle reset button
  if (resetFormBtn) {
    resetFormBtn.addEventListener('click', function() {
      // Clear form by redirecting to the page without parameters
      window.location.href = window.location.pathname;
    });
  }
  
  // Password reset checkbox functionality
  const resetPasswordCheckbox = document.getElementById('reset_password_checkbox');
  const passwordFields = document.querySelectorAll('#password, #confirm_password');
  
  if (resetPasswordCheckbox) {
    resetPasswordCheckbox.addEventListener('change', function() {
      if (this.checked) {
        passwordFields.forEach(field => {
          field.setAttribute('required', 'required');
        });
      } else {
        passwordFields.forEach(field => {
          field.removeAttribute('required');
        });
      }
    });
  }
  
  // Form validation before submit
  if (staffForm) {
    staffForm.addEventListener('submit', function(event) {
      // Full form validation for add/update
      let isValid = true;
      
      // Basic validation for required fields
      if (!firstNameField.value.trim()) {
        alert('First name is required!');
        firstNameField.focus();
        isValid = false;
      } else if (!lastNameField.value.trim()) {
        alert('Last name is required!');
        lastNameField.focus();
        isValid = false;
      } else if (!genderField.value) {
        alert('Please select a gender!');
        genderField.focus();
        isValid = false;
      } else if (!dobField.value) {
        alert('Date of birth is required!');
        dobField.focus();
        isValid = false;
      } 
      // Validate birth year and age
      else if (dobField.value) {
        const selectedDate = new Date(dobField.value);
        const birthYear = selectedDate.getFullYear();
        const today = new Date();
        
        // Calculate age
        let age = today.getFullYear() - birthYear;
        const m = today.getMonth() - selectedDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < selectedDate.getDate())) {
          age--;
        }
        
        if (birthYear <= 1900) {
          alert('Birth year must be after 1900!');
          dobField.focus();
          isValid = false;
        } else if (age < 16) {
          alert('Staff members must be at least 16 years old!');
          dobField.focus();
          isValid = false;
        }
      } else if (!emailField.value.trim()) {
        alert('Email address is required!');
        emailField.focus();
        isValid = false;
      } else if (!roleField.value) {
        alert('Please select a role!');
        roleField.focus();
        isValid = false;
      } else if (!countryField.value) {
        alert('Please select a country!');
        countryField.focus();
        isValid = false;
      }
      
      // Password validation for new staff or when password reset is checked
      if (isValid) {
        const isAddMode = document.getElementById('add-staff-btn') && 
                         !document.getElementById('add-staff-btn').classList.contains('hidden');
        const isPasswordReset = document.getElementById('reset_password_checkbox') ? 
                               document.getElementById('reset_password_checkbox').checked : false;
        
        if (isAddMode || isPasswordReset) {
          if (!passwordField.value) {
            alert('Password is required!');
            passwordField.focus();
            isValid = false;
          } else if (passwordField.value.length < 6) {
            alert('Password must be at least 6 characters long!');
            passwordField.focus();
            isValid = false;
          } else if (passwordField.value !== confirmPasswordField.value) {
            alert('Passwords do not match!');
            confirmPasswordField.focus();
            isValid = false;
          }
        }
      }
      
      if (!isValid) {
        event.preventDefault();
        return false;
      }
      return true;
    });
  }
}

/**
 * Manage FAQ page functionality
 */
function initManageFAQ() {
  const faqForm = document.getElementById('faqForm');
  const faqFormTitle = document.getElementById('faq-form-title');
  const faqIdField = document.getElementById('faq_id');
  const questionField = document.getElementById('question');
  const answerField = document.getElementById('answer');
  const resetFormBtn = document.getElementById('reset-form');
  
  // Handle edit button clicks
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
      const faqId = this.getAttribute('data-id');
      
      // Redirect to the same page with ID parameter
      window.location.href = `?id=${faqId}`;
    });
  });
  
  // Handle reset button
  if (resetFormBtn) {
    resetFormBtn.addEventListener('click', function() {
      // Clear form by redirecting to the page without parameters
      window.location.href = window.location.pathname;
    });
  }
  
  // Form validation before submit
  if (faqForm) {
    faqForm.addEventListener('submit', function(event) {
      // Basic form validation
      let isValid = true;
      
      // Check required fields
      if (!questionField.value.trim()) {
        alert('Question is required!');
        questionField.focus();
        isValid = false;
      } else if (!answerField.value.trim()) {
        alert('Answer is required!');
        answerField.focus();
        isValid = false;
      }
      
      if (!isValid) {
        event.preventDefault();
        return false;
      }
      return true;
    });
  }
}

/**
 * Manage Events page functionality
 */
function initManageEvents() {
  const eventForm = document.getElementById('eventForm');
  const eventFormTitle = document.getElementById('event-form-title');
  const eventIdField = document.getElementById('event_id');
  const eventTitleField = document.getElementById('event_title');
  const eventVenueField = document.getElementById('event_venue');
  const eventDateField = document.getElementById('event_date');
  const eventTimeField = document.getElementById('event_time');
  const eventDescriptionField = document.getElementById('event_description');
  const resetFormBtn = document.getElementById('reset-form');
  
  // Handle edit button clicks
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
      const eventId = this.getAttribute('data-id');
      
      // Redirect to the same page with ID parameter
      window.location.href = `?id=${eventId}`;
    });
  });
  
  // Handle reset button
  if (resetFormBtn) {
    resetFormBtn.addEventListener('click', function() {
      window.location.href = window.location.pathname;
    });
  }
  
  // Form validation
  if (eventForm) {
    eventForm.addEventListener('submit', function(event) {
      let isValid = true;
      
      // Basic validation for required fields
      if (!eventTitleField.value.trim()) {
        alert('Event title is required!');
        eventTitleField.focus();
        isValid = false;
      } else if (!eventVenueField.value.trim()) {
        alert('Venue is required!');
        eventVenueField.focus();
        isValid = false;
      } else if (!eventDateField.value) {
        alert('Date is required!');
        eventDateField.focus();
        isValid = false;
      } else if (!eventTimeField.value) {
        alert('Time is required!');
        eventTimeField.focus();
        isValid = false;
      } else if (!eventDescriptionField.value.trim()) {
        alert('Description is required!');
        eventDescriptionField.focus();
        isValid = false;
      }
      
      if (!isValid) {
        event.preventDefault();
        return false;
      }
      return true;
    });
  }
}

/**
 * Manage Demos page functionality
 */
function initManageDemos() {
  console.log('Demonstration management page loaded');
  
  // Handle edit button clicks for demos
  document.querySelectorAll('.edit-demo-btn').forEach(button => {
    button.addEventListener('click', function() {
      const demoId = this.getAttribute('data-id');
      window.location.href = `?id=${demoId}`;
    });
  });
}

/**
 * Helper function to validate email
 */
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}
