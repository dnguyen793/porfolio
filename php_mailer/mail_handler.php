<?php
require_once('email_config.php');
require('phpmailer/PHPMailer/PHPMailerAutoload.php');


//validate POST inputs
$message = [];
$output = [
    'success' => null,
    'messages' => []
];

//sanitize name field
$message['contactName'] = filter_var($_POST['contactName'], FILTER_SANITIZE_STRING);
if(empty($message['contactName'])){
    $output['success'] = false;
    $output['messages'][] = ' Missing contact name';
}

//sanitize email field
$message['contactEmail'] = filter_var($_POST['contactEmail'], FILTER_VALIDATE_EMAIL);
if(empty($message['contactEmail'])){
    $output['success'] = false;
    $output['messages'][] = ' Missing contact email';
}

//sanitize subject field
$message['contactSubject'] = filter_var($_POST['contactSubject'], FILTER_SANITIZE_STRING);
if(empty($message['contactSubject'])){
    $output['success'] = false;
    $output['messages'][] = ' Missing contact subject';
}

//sanitize message field
$message['contactMessage'] = filter_var($_POST['contactMessage'], FILTER_SANITIZE_STRING);
if(empty($message['contactMessage'])){
    $output['success'] = false;
    $output['messages'][] = ' Missing contact message';
}



if($output['success'] !== null){
    http_response_code(400);
    echo json_encode($output);
    exit();
}

//set up mail object
$mail = new PHPMailer;
$mail->SMTPDebug = 0;           // Enable verbose debug output. Change to 0 to disable debugging output.

$mail->isSMTP();                // Set mailer to use SMTP.
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
$mail->SMTPAuth = true;         // Enable SMTP authentication


$mail->Username = EMAIL_USER;   // SMTP username
$mail->Password = EMAIL_PASS;   // SMTP password
$mail->SMTPSecure = 'tls';      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
$mail->Port = 587;              // TCP port to connect to
$options = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->smtpConnect($options);
$mail->From = $message['contactEmail'];  // sender's email address (shows in "From" field)
$mail->FromName = $message['contactName'];   // sender's name (shows in "From" field)


$mail->addAddress(EMAIL_USER);  // Add a recipient
//$mail->addAddress('ellen@example.com');                        // Name is optional
$mail->addReplyTo($message['contactEmail'], $message['contactName']);                          // Add a reply-to address
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $message['contactSubject'];
$message['contactMessage'] = nl2br($message['contactMessage']);
$mail->Body    = $message['contactMessage'];
$mail->AltBody = htmlentities($message['contactMessage']);

if(!$mail->send()) {
    $output['success'] = false;
    $output['message'][] = $mail->ErrorInfo;
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $output['success'] = true;
    echo 'Message has been sent';
}   
    echo json_encode($output);
?>
