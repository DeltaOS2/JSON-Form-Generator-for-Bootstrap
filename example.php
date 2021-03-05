<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JSON Form Generator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">


<?php
$formData = <<<EOF
{
  "description": "Example for internal JSON code",
  "name": "formData",
  "method": "post",
  "action": "?",
  "class" : "needs-validation",
  "novalidate": true,
  "properties": {
    "formRowOpen": {
      "type": "div",
      "open": true,
      "class": "row justify-content-start"
      },
    "formColOpen": {
      "type": "div",
      "open": true,
      "class": "col-5"
      },
    "hiddenElement": {
      "type": "hidden",
      "value": "eins"
      },
    "firstName": {
      "type": "text",
      "label": "First name",
      "name":"firstName",
      "id":"firstName",
      "value": "test",
      "required": true,
      "onchange":"console.log(this.value)",
      "class": "form-control",
      "labelClass": "form-label w-100",
      "aria-describedby": "firstNameHelp",
      "help": "Max character = 20",
      "helpClass": "form-text",
      "divClass": "mb-3",
      "feedback": "Something went wrong!",
      "feedbackID": "lastName-feedback",
      "feedbackClass": "invalid-feedback"
      },
    "lastName": {
      "type": "text",
      "aria-label": true,
      "label": "Last name",
      "name":"lastName",
      "class": "form-control mt-2",
      "placeholder": "Enter Your Last Name",
      "required": true,
      "divClass": "mb-3",
      "help": "Max character = 30",
      "helpClass": "form-text",
      "feedback": "Something went wrong!",
      "feedbackID": "lastName-feedback",
      "feedbackClass": "invalid-feedback"
      },
    "emailAddress": {
      "type": "email",
      "label": "E-Mail Address",
      "name":"email",
      "required": true,
      "onChange":"console.log(this.value)",
      "aria-label": true,
      "class": "form-control",
      "labelClass": "form-label w-100",
      "aria-describedby": "emailHelp",
      "help": "name@server.com",
      "helpClass": "form-text",
      "divClass": "mb-3",
      "feedback": "Something went wrong!",
      "feedbackID": "lastName-feedback",
      "feedbackClass": "invalid-feedback"
      },
    "test": {
      "type": "text",
      "placeholder": "Placeholder Text",
      "class": "form-control mb-3"
      
      },
    "textarea": {
      "type": "textarea",
      "label": "Tell your story",
      "name": "story",
      "class": "form-control mt-2",
      "labelClass": "form-label w-100",
      "aria-describedby": "storyHelp",
      "help": "Max character = 512",
      "helpClass": "form-text",
      "divClass": "mb-3"
      },
    "language": {
      "type": "select",
      "label": "Language",
      "name": "lang",
      "id": "lang",
      "options": {
        "en": ["English", false],
        "fa": ["Farsi", false],
        "de": ["German", true]
        },
      "class": "form-select mt-2",
      "labelClass": "form-label w-100",
      "divClass": "mb-3",
      "help": "Select your language",
      "helpClass": "form-text"
      },
    "buttonGridOpen": {
      "type": "div",
      "open": true,
      "class": "d-grid gap-2 d-flex justify-content-end"
      },
    "clear": {
      "type": "button",
      "label": "Clear Form",
      "name":"clear",
      "onclick": "form.reset()",
      "class": "btn btn-danger"
      },
    "submit": {
      "type": "submit",
      "label": "Send Form",
      "name":"submit",
      "class": "btn btn-success me-2"
      },
    "buttonGridClose": {
      "type": "div",
      "open": false
      },
    "formColClose": {
      "type": "div",
      "open": false
      },
    "formRowClose": {
      "type": "div",
      "open": false
      }
  }
}
EOF;


// Include the class
require_once 'Form.php';

// Use a JSON file
echo "<h1>Example of using JSON file</h1>\n";
$form = new Form('example_form.json');
try {
  $form->show();
} catch (JsonException $je) {
  echo $je->getMessage();
}


//Use the internal JSON code
echo "<h1 class='mt-5'>Example of using internal JSON code</h1>\n";
try {
  $form = new Form($formData);
  $form->show();
} catch (ErrorException $e) {
  echo $e->getMessage();
} catch (JsonException $je) {
  echo $je->getMessage();
}
?>


</div><!-- end container -->
<br><br><br>
<script>
  // Example starter JavaScript for disabling form submissions if there are invalid fields
  (function () {
    'use strict'
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
  })()
</script>

<!--  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>-->
</body>
</html>