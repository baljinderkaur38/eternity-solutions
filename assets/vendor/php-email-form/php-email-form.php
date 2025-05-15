<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If Composer is used
// require 'vendor/autoload.php';

// If manually included
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';
require_once __DIR__ . '/phpmailer/Exception.php';

class PHP_Email_Form {
  public $to = '';
  public $from_name = '';
  public $from_email = '';
  public $subject = '';
  public $ajax = false;
  public $messages = array();
  //public $smtp = false; // contains ['host', 'username', 'password', 'port']
  public $smtp =  ['smtp.gmail.com', 'baljinderkaur1374@gmail.com', 'ruuhlzsdmkivbok', '587'];

  public function add_message($content, $label = '', $length = 0) {
    if (!empty($content)) {
      $message = ($label ? "$label: " : '') . trim($content) . "\n";
      $this->messages[] = $message;
    }
  }

  public function send() {
    $mail = new PHPMailer(true);

    try {
      // Server settings
      if ($this->smtp && is_array($this->smtp)) {
        $mail->isSMTP();
        $mail->Host = $this->smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtp['username'];
        $mail->Password = $this->smtp['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->smtp['port'];
      }

      // Recipients
      $mail->setFrom($this->from_email, $this->from_name);
      $mail->addAddress($this->to);

      // Content
      $mail->isHTML(false);
      $mail->Subject = $this->subject;
      $mail->Body = implode("\n", $this->messages);

      $mail->send();
      return 'OK';
    } catch (Exception $e) {
      return 'Mailer Error: ' . $mail->ErrorInfo;
    }
  }
}
