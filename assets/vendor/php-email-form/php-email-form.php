<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
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

  private $smtp_host = 'smtp.gmail.com'; 
  private $smtp_user = 'baljinderkaur1374@gmail.com'; 
  private $smtp_pass = 'nljbyuxcdfmppvcu'; 
  private $smtp_port = 587;

  public function add_message($content, $label = '', $length = 0) {
    if (!empty($content)) {
      $message = ($label ? "$label: " : '') . trim($content) . "\n";
      $this->messages[] = $message;
    }
  }

  public function send() {
    $mail = new PHPMailer(true);

    try {

      $mail->isSMTP();
      $mail->Host       = $this->smtp_host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $this->smtp_user;
      $mail->Password   = $this->smtp_pass;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = $this->smtp_port;

      // Mail details
      $mail->setFrom($this->from_email, $this->from_name);
      $recipients = is_array($this->to) ? $this->to : [$this->to];
      foreach ($recipients as $email) {
        $mail->addAddress($email);
      }
      $mail->Subject = $this->subject;
      $mail->Body    = implode("\n", $this->messages);
      $mail->isHTML(false);

      $mail->send();
      return 'OK';
    } catch (Exception $e) {
      return 'Mail Exception: ' . $mail->ErrorInfo;
    }
  }
}
