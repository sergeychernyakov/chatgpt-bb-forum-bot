function togglePasswordVisibility(fieldId) {
  var field = document.getElementById(fieldId);
  var icon = field.nextElementSibling;
  if (field.type === "password") {
      field.type = "text";
      icon.classList.remove('dashicons-visibility');
      icon.classList.add('dashicons-hidden');
  } else {
      field.type = "password";
      icon.classList.remove('dashicons-hidden');
      icon.classList.add('dashicons-visibility');
  }
}
