<?php
/*
THIS FILE USES PHPMAILER INSTEAD OF THE PHP MAIL() FUNCTION
AND ALSO SMTP TO SEND THE EMAILS
*/

require 'PHPMailer/PHPMailerAutoload.php';

/*
*  CONFIGURE EVERYTHING HERE
*/

// an email address that will be in the From field of the email.

$fromEmail = $_REQUEST['email'];
$fromName = $_REQUEST['name'];

// an email address that will receive the email with the output of the form
$sendToEmail = 'info@zel.cash';
$sendToName = 'GetListed';

// subject of the email
$subject = 'New listing from ZelTreZ website';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name' => 'Name', 'ticker' => 'Ticker', 'fork' => 'Coin', 'other' => 'If Other', 'why' => 'Why get listed?', 'email' => 'Email', 'github' => 'Github', 'website' => 'Website', 'blockexplorer' => 'Blockexplorer', 'discord' => 'Discord', 'message' => 'Message');

// message that will be displayed when everything is OK :)
$okMessage = 'Thank you, We will get back to you soon!';

// If something goes wrong, we will display this message.
$errorMessage = 'There was an error while submitting the form. Please try again later';

/*
*  LET'S DO THE SENDING
*/

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try
{
    
    if(count($_POST) == 0) throw new \Exception('Form is empty');
    
    $emailTextHtml = "<h1>New listing from ZelTreZ website</h1><hr>";
    $emailTextHtml .= "<table>";
    
    foreach ($_POST as $key => $value) {
        // If the field exists in the $fields array, include it in the email
        if (isset($fields[$key])) {
            $emailTextHtml .= "<tr><th>$fields[$key]</th><td>$value</td></tr>";
        }
    }
    $emailTextHtml .= "</table><hr>";
    $emailTextHtml .= "<p>Best regards,<br>Zel Team</p>";
    
    $mail = new PHPMailer;
    
    $mail->setFrom($fromEmail);
    $mail->addAddress($sendToEmail, $sendToName); // you can add more addresses by simply adding another line with $mail->addAddress();
    $mail->addReplyTo($fromEmail);
    
    $mail->isHTML(true);
    
    $mail->Subject = $subject;
    $mail->Body    = $emailTextHtml;
    $mail->msgHTML($emailTextHtml); // this will also create a plain-text version of the HTML email, very handy
    
    
    $mail->isSMTP();
    
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    
    //Set the hostname of the mail server
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6
    $mail->Host = 'ssl://smtp.gmail.com';
    
    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 465;
    
    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'ssl';
    
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    
    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = 'info@zel.cash';
    
    //Password to use for SMTP authentication
    $mail->Password = '';
    
    
    if(!$mail->send()) {
        throw new \Exception('Could not send the email. ' . $mail->ErrorInfo);
    }
    
    $responseArray = array('type' => 'success', 'message' => $okMessage);
}
catch (\Exception $e)
{
    // $responseArray = array('type' => 'danger', 'message' => $errorMessage);
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
}


// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);
    
    header('Content-Type: application/json');
    
    echo $encoded;
}
// else just display the message
else {
    echo $responseArray['message'];
}