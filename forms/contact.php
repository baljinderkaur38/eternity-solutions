<?php
require_once '../assets/vendor/php-email-form/php-email-form.php';

// Where the message will be received

$contact = new PHP_Email_Form;
$contact->ajax = true;
$contact->to = [
  'eternitysolutioncompany@gmail.com',
  'baljinderkaur1374@gmail.com',
  'info@eternitysolution.in'
];

// User's input from the form
$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$phone   = $_POST['phone'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

$contact->from_name = $name;
$contact->from_email = $email;
$contact->subject = "New Contact Form Submission";

// Format the email message as an HTML template
$formatted_message = "
You have received a new contact form submission:

------------------------------------------------------------
🧑 Name: $name
📧 Email: $email
📌 Subject: $subject
📞 Phone: $phone
💬 Message:
$message
------------------------------------------------------------
";

// Add to the PHPMailer body
$contact->add_message($formatted_message, '', 0);

// Send
echo $contact->send();
