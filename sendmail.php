<?php

session_cache_limiter( 'nocache' );
header( 'Expires: ' . gmdate( 'r', 0 ) );
header( 'Content-type: application/json' );
$to         = 'John@example.com';  // put your email here
$email_template = 'simple.html';
$subject    = strip_tags($_POST['vsubject']);
$email       = strip_tags($_POST['vemail']);
$name       = strip_tags($_POST['vname']);
$message    = nl2br( htmlspecialchars($_POST['vmessage'], ENT_QUOTES) );
$result     = array();
            
    
    if(empty($email)){
        $result = array( 'response' => 'error', 'empty'=>'email', 'message'=>'<strong>Error!</strong>&nbsp; Email is empty.' );
        echo json_encode($result );
        die;
    } 
    if(empty($name)){
        $result = array( 'response' => 'error', 'empty'=>'name', 'message'=>'<strong>Error!</strong>&nbsp; Name is empty.' );
        echo json_encode($result );
        die;
    } 
    if(empty($message)){
         $result = array( 'response' => 'error', 'empty'=>'message', 'message'=>'<strong>Error!</strong>&nbsp; Message body is empty.' );
         echo json_encode($result );
         die;
    }
    if(empty($_POST['g-recaptcha-response']))  {
        $result = array( 'response' => 'error', 'empty'=>'message', 'message'=>'<strong>Error!</strong>&nbsp; Please complete the Captcha.' );
         echo json_encode($result );
         die;
    }
 
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        $secret = 'Your-secret-key-here';
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if($responseData->success == true) {
    $headers  = "From: " . $name . ' <' . $email . '>' . "\r\n";
    $headers .= "Reply-To: ". $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $templateTags =  array(
        '{{subject}}' => $subject,
        '{{email}}'=>$email,
        '{{message}}'=>$message,
        '{{name}}'=>$name
        );
    $templateContents = file_get_contents( dirname(__FILE__) . '/inc/'.$email_template);
    $contents =  strtr($templateContents, $templateTags);
    if ( mail( $to, $subject, $contents, $headers ) ) {
        $result = array( 'response' => 'success', 'message'=>'<strong>Thank You!</strong>&nbsp; Your email has been delivered.' );
    } else {
        $result = array( 'response' => 'error', 'message'=>'<strong>Error!</strong>&nbsp; Can\'t Send Mail.'  );
    }
    echo json_encode( $result );
    die;

        }
        else
        {
            echo "<p> Sorry verification valid</p>";
        }
   }
?>