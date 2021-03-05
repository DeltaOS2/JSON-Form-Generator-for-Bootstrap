# JSON Form Generator for Bootstrap*
*(Bootstrap or other CSS Frameworks)

JSON Form Generator for Bootstrap is an HTML Form Generator from JSON. It takes a JSON file or JSON string and generates a form based on the principles of Bootstrap 5 or comparable CSS frameworks.

It assumes that you are familiar with the basic principles of creating a web form.

All types of standard `<INPUT>` plus `<SELECT>`, `<TEXTAREA>`, `<BUTTON>` and `<DIV>` are available.

It follows the standards regarding how a label should be defined on a form element.

For example: If an ID is given, the label will be before or after the form element (1st code example) and if no ID is given, the form element will be enveloped into a label (2nd code example).

If you use help text to describe the `<input>` and you give the help text an ID attribute, the ID will be used in an `aria-describedby` attribute for the `<input>`.

If something goes wrong with your JSON or something is missing, it will show you some (hopefully useful) error messages.

The class takes one parameter. That can be PHP variable or a file containing valid JSON code.  

Here are two use case scenarios. At first we use an external JSON file. The class is testing if the parameter is ending with ".json" and assumes it is a file. If not we treat it as internal code.  
The second part shows the use with internal JSON code.  

While the class works perfectly without being enclosed into a try-catch phrase, you should consider using it.

```
 1  require_once 'Form.php';
 2 
 3 
 4  echo "<h1>Example of using JSON file</h1>\n";
 5 
 6  $form = new Form('example_form.json');
 7  try {
 8    $form->show();
 9  } catch (JsonException $je) {
10    echo $je->getMessage();
11  }
12 
13 
14 
15  echo "<h1>Example of using internal JSON code</h1>\n";
16 
17  try {
18    $form = new Form($formData);
19    $form->show();
20  } catch (ErrorException $e) {
21    echo $e->getMessage();
22  } catch (JsonException $je) {
23    echo $je->getMessage();
24  }
```

(Example 1)

```
<div class='mb-3'>
<label for='firstName' class='form-label w-100'>First name</label>
<input aria-describedby='firstNameHelp' class='form-control' id='firstName' name='firstName' pattern='.{3,}' required type='text' value='test'>
<div id='lastName-feedback' class='invalid-feedback'>Something went wrong!</div>
<div id='firstNameHelp' class='form-text'>Max character = 20</div>
</div>
```

(Example 2)

```
<div class='mb-3'>
<label class='form-label w-100'>Tell your story
<textarea aria-describedby='storyHelp' class='form-control mt-2' name='story' rows='5'></textarea>
<span id='storyHelp' class='form-text'>Max character = 512</span>
</label>
</div>
```


# Code example and parameter description

## Start the FORM

```
1  "description": "Example for internal JSON code",
2  "name": "formData",
3  "method": "post",
4  "action": "?",
5  "class" : "needs-validation",
6  "novalidate": true,
7  "properties": {
```

### Description for the code above:

```
1  Optional Description for the JSON code
2  Optional Name of the form
3  Optional Method to use
4  Optional Action to use
5  Optional Class
6  Optional validation tag
7  Properties starts here
```

## Minimalistic JSON content with minimum INPUT element

```
 1  {
 2    "properties": {
 3      "hiddenElement": {
 4        "type": "hidden",
 5        "value": "secretValue"     
 6      }
 7    }
 8  }
```

### Description for the code above:

This is the bare minimum of an INPUT element. It will generate this HTML code:

```
<form>
<input type='hidden' value='secretValue'>
</form>
```

Input type hidden is the only input that is **_NOT_** expecting a label - 
All other input elements will expect a label. The label is used for the `<label>` tag and for the `aria-label`  attribute.  
If you set `"aria-label": true`  the content of `"label": "blabla"` will be used.  
The result will be `aria-label='blabla'` and no `<label>` will be generated.

While generating a checkbox or a radio button, the label text will always be located after the checkbox or radio button.

## TEXT INPUT with help text and validation feedback text

```
 1  "firstName": {
 2    "type": "text",
 3    "label": "First name",
 4    "name":"firstName",
 5    "id":"firstName",
 6    "value": "test",
 7    "required": true,
 8    "onchange":"console.log(this.value)",
 9    "class": "form-control",
10    "labelClass": "form-label w-100",
11    "aria-describedby": "firstNameHelp",
12    "help": "Max character = 20",
13    "helpClass": "form-text",
14    "divClass": "mb-3",
15    "feedback": "Something went wrong!",
16    "feedbackID": "lastName-feedback",
17    "feedbackClass": "invalid-feedback"
18    },
```

### Description for the code above:

```
 1  Unique name for JSON element
 2  Input type (all standard plus button, submit, reset, textarea, select, div)
 3  Text for LABEL
 4  Name
 5  ID
 6  Value
 7  Required (true / false)
 8  onChange action
 9  Class for form element
10  Class for label element
11  Connection to help text
12  Help text
13  Class for help text element
14  Class for enveloping DIV element
15  Validation feedback text
16  ID for feedback element (DIV / SPAN)
17  Class for feedback element (DIV / SPAN)
```

## Example enveloping BUTTONS in to a DIV

```
 1  "buttonGridOpen": {
 2    "type": "div",
 3    "open": true,
 4    "class": "d-grid gap-2 d-flex justify-content-end"
 5    },
 6  "clear": {
 7    "type": "button",
 8    "label": "Clear Form",
 9    "name":"clear",
10    "onclick": "form.reset()",
11    "class": "btn btn-danger"
12    },
13  "submit": {
14    "type": "submit",
15    "label": "Send Form",
16    "name":"submit",
17    "class": "btn btn-success me-2"
18    },
19  "buttonGridClose": {
20    "type": "div",
21    "open": false
22    },
```


### Description for the code above:

```
 1  Unique name for JSON element
 2  Input type DIV
 3  Define position of DIV ("open": true) opens the DIV
 4  Class for DIV
 5  
 6  Unique name for JSON element
 7  Type
 8  Text for LABEL
 9  Name
10  onClick action
11  Class for form element
12  
13  Unique name for JSON element
14  Type
15  Text for LABEL
16  Name
17  Class for form element
18  
19  Unique name for JSON element
20  Input type DIV
21  Define position of DIV ("open": false) closes the DIV
```

The code will produce this HTML code:

```
<div class='d-grid gap-2 d-flex justify-content-end'>
<button class='btn btn-danger' name='clear' onclick='form.reset()' type='button'>Clear Form</button>
<button class='btn btn-success me-2' name='submit' type='submit'>Send Form</button>
</div>
```

# About the use of the "pattern" attribute

Don't use pattern for email. Set the "type" attribute to "email" instead.  
`type='email'` has already some useful email verification.

Don't use pattern for date. Set the "type" attribute to "date" or "datetime" instead. They include already all kind of national and international date formats. If you still want to use the pattern attribute here, be aware that you have to be very specific and give the user an help text with precise information about the expected format.
 
Don't use pattern for phone. Set the "type" attribute to "tel" instead.  
This includes already all kind of national and international phone number formats. If you still want to use the pattern attribute here, be aware that you have to be very specific and give the user an help text with precise information about the expected format.

If you need real input control use a JavaScript to verify the data and give the user useful feedback on validation.

# Limitations

For now the Bootstrap input groups are not supported.  
This may follow in a later version.

The minimum PHP version is 7.4

