/**
 * install.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:34
 */
$(document).ready(function () {
  $('form').submit(function (event) {
    var currentField, error = false;
    $('div.text-danger').remove();
    for (var i = 0; i < 7; i++) {
      currentField = $(event.target[i]);
      currentField.val(currentField.val().trim());
      if (currentField.attr('type') == 'text') {
        if (currentField.val().match(/[!@#$%^&*()+\-=\[\]{};'"\\|,<>\/?\s]/) != null || currentField.val() == '') {
          formError(currentField);
          error = true;
        }
      } else if (currentField.attr('type') == 'number') {
        currentField.val(currentField.val().trim());
        if (currentField.val().match(/[a-z!@#$%^&*()_+\-=\[\]{};'"\\|,.<>\/?\s]/i) != null || currentField.val() == '') {
          formError(currentField);
          error = true;
        }
      } else if (currentField.attr('type') == 'password') {
        if (currentField.val().match(/[()=\[\]\^{};:'"\\|,.<>\/\s]/) != null || currentField.val() == '') {
          formError(currentField);
          error = true;
        } else {
          //noinspection JSCheckFunctionSignatures,JSUnresolvedVariable
          currentField.val(SparkMD5.hash(currentField.val()));
        }
      }
    }

    if (error) {
      event.preventDefault();
    }
  });
});

function formError(field) {
  field.parent('div').addClass('has-error').prepend('<div class="text-danger">Something is wrong in this field</div>');
}