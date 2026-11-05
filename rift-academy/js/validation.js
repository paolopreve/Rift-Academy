// Validazione lato client dei form con attributo data-validate.
// Miglioramento progressivo: senza JavaScript restano la validazione nativa
// del browser e, soprattutto, la validazione lato server in PHP.
(function () {
  'use strict';

  function errorBox(field) {
    var id = field.id + '-error';
    var box = document.getElementById(id);
    if (!box) {
      box = document.createElement('p');
      box.id = id;
      box.className = 'field-error';
      field.parentNode.appendChild(box);
    }
    return box;
  }

  function setError(field, message) {
    var box = errorBox(field);
    box.textContent = message;
    field.setAttribute('aria-invalid', 'true');
    field.setAttribute('aria-describedby', box.id);
  }

  function clearError(field) {
    var box = document.getElementById(field.id + '-error');
    if (box && box.parentNode) { box.parentNode.removeChild(box); }
    field.removeAttribute('aria-invalid');
    field.removeAttribute('aria-describedby');
  }

  function validateField(field) {
    var value = (field.value || '').trim();
    if (field.required && value === '') {
      setError(field, 'Compila questo campo.');
      return false;
    }
    var min = parseInt(field.getAttribute('data-minlength'), 10);
    if (min && value !== '' && value.length < min) {
      setError(field, 'Inserisci almeno ' + min + ' caratteri.');
      return false;
    }
    if (field.type === 'email' && value !== '' && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value)) {
      setError(field, 'Inserisci un indirizzo email valido.');
      return false;
    }
    clearError(field);
    return true;
  }

  document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('form[data-validate]');
    Array.prototype.forEach.call(forms, function (form) {
      form.setAttribute('novalidate', 'novalidate');
      form.addEventListener('submit', function (event) {
        var fields = form.querySelectorAll('input, select, textarea');
        var firstInvalid = null;
        Array.prototype.forEach.call(fields, function (field) {
          if (!field.id || field.type === 'hidden' || field.type === 'submit') { return; }
          if (!validateField(field) && !firstInvalid) { firstInvalid = field; }
        });
        if (firstInvalid) {
          event.preventDefault();
          firstInvalid.focus();
        }
      });
    });
  });
}());
