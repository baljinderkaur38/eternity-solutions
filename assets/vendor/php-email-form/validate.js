(function () {
  "use strict";

  let forms = document.querySelectorAll('.php-email-form');

  const fields = [
    { id: 'name-field', error: 'name-error' },
    { id: 'email-field', error: 'email-error' },
    { id: 'phone-field', error: 'phone-error' },
    { id: 'subject-field', error: 'subject-error' },
    { id: 'message-field', error: 'message-error' }
  ];

  forms.forEach(function (form) {

    // Real-time validation on input and blur
    fields.forEach(field => {
      const input = form.querySelector(`#${field.id}`);
      const errorSpan = form.querySelector(`#${field.error}`);

      const validate = () => {
        if (!input.checkValidity()) {
          errorSpan.textContent = input.title || 'Invalid input';
        } else {
          errorSpan.textContent = '';
        }
      };

      input.addEventListener('input', validate);
      input.addEventListener('blur', validate);
    });

    // Validate and show messages on submit button click
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.addEventListener('click', () => {
        fields.forEach(field => {
          const input = form.querySelector(`#${field.id}`);
          const errorSpan = form.querySelector(`#${field.error}`);

          if (!input.checkValidity()) {
            errorSpan.textContent = input.title || 'Invalid input';
          } else {
            errorSpan.textContent = '';
          }
        });
      });
    }

    // Form submit handler with validation and AJAX submission
    form.addEventListener('submit', function (event) {
      event.preventDefault();

      let thisForm = this;
      let action = thisForm.getAttribute('action');
      let recaptcha = thisForm.getAttribute('data-recaptcha-site-key');

      // Clear all field-specific error messages
      thisForm.querySelectorAll('.error-message').forEach(el => el.textContent = '');

      let isValid = true;

      fields.forEach(field => {
        const input = thisForm.querySelector(`#${field.id}`);
        const errorSpan = thisForm.querySelector(`#${field.error}`);

        if (!input.checkValidity()) {
          errorSpan.textContent = input.title || 'Invalid input';
          isValid = false;
        }
      });

      if (!isValid) return;

      // Proceed with AJAX if all fields are valid
      thisForm.querySelector('.loading').classList.add('d-block');
      thisForm.querySelector('.error-message:not(span)').classList.remove('d-block');
      thisForm.querySelector('.sent-message').classList.remove('d-block');

      let formData = new FormData(thisForm);

      if (recaptcha) {
        if (typeof grecaptcha !== "undefined") {
          grecaptcha.ready(function () {
            try {
              grecaptcha.execute(recaptcha, { action: 'php_email_form_submit' })
                .then(token => {
                  formData.set('recaptcha-response', token);
                  php_email_form_submit(thisForm, action, formData);
                });
            } catch (error) {
              displayError(thisForm, error);
            }
          });
        } else {
          displayError(thisForm, 'The reCaptcha JavaScript API URL is not loaded!');
        }
      } else {
        php_email_form_submit(thisForm, action, formData);
      }
    });
  });

  function php_email_form_submit(thisForm, action, formData) {
    fetch(action, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(response => {
        if (response.ok) {
          return response.text();
        } else {
          throw new Error(`${response.status} ${response.statusText} ${response.url}`);
        }
      })
      .then(data => {
        thisForm.querySelector('.loading').classList.remove('d-block');
        if (data.trim() == 'OK') {
          thisForm.querySelector('.sent-message').classList.add('d-block');
          thisForm.reset();
        } else {
          throw new Error(data ? data : 'Form submission failed and no error message returned from: ' + action);
        }
      })
      .catch((error) => {
        displayError(thisForm, error);
      });
  }

  function displayError(thisForm, error) {
    const errorEl = thisForm.querySelector('.error-message:not(span)');
    thisForm.querySelector('.loading').classList.remove('d-block');
    if (errorEl) {
      errorEl.innerHTML = error;
      errorEl.classList.add('d-block');
    }
  }

})();
