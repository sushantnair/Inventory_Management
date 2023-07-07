<?php  
include('../connection.php');

    //Include required PHPMailer files
    require 'includes/PHPMailer.php';
    require 'includes/SMTP.php';
    require 'includes/Exception.php';
  //Define name spaces
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

if(isset($_POST['btn-send'])){

$a=$_POST['a'];
$b=$_POST['b'];
$c=$_POST['c'];
$d=$_POST['d'];

$e= $a.$b.$c.$d;

$sql="SELECT * FROM user WHERE vcode='$e'";
$st=mysqli_query($conn,$sql);
$sn=mysqli_num_rows($st);

if($sn>0){
    $sa=mysqli_fetch_array($st);
    $email=$sa['email'];

    $sql1="UPDATE user SET status='0' WHERE vcode='$e'";
    $ro=mysqli_query($conn,$sql1);
    //Create instance of PHPMailer
	  $mail = new PHPMailer();
  //Set mailer to use smtp
    $mail->isSMTP();
  //Define smtp host
    $mail->Host = "smtp.gmail.com";
  //Enable smtp authentication
    $mail->SMTPAuth = true;
  //Set smtp encryption type (ssl/tls)
    $mail->SMTPSecure = "tls";
  //Port to connect smtp
    $mail->Port = "587";
  //Set gmail username
    $mail->Username = "imskjsce@gmail.com";
  //Set gmail password
    $mail->Password = $otppass;
  //Email subject
    $mail->Subject = "OTP Verification";
  //Set sender email
    $mail->setFrom('imskjsce@gmail.com');
  //Enable HTML
    $mail->isHTML(true);
  //Attachment
    // $mail->addAttachment('img/attachment.png');
  //Email body
    $mail->Body = "<h1>Your account has been verified</h1>";
  //Add recipient
    $mail->addAddress($email);
  //Finally send email
      $mail->send(); 
  //Closing smtp connection
    $mail->smtpClose();
    header('Location: ../login.php');

}else{
    echo "WRONG ENTRY";
    header("Location: index.php");
}

}


?>


<html>
    <head>
        <title>OTP Verification</title>
        <link rel="stylesheet" href="style.css">
        <script src="https://cdn.tailwindcss.com"></script>

    </head>
    <body>
        <form action="" method="post">
        <div class="container">
            <h3 class="title">OTP Verification</h3>
            <p class="sub-title">Enter the OTP you received</p>
            <div class="wrapper">
              <input name="a" type="text" class="field 1" maxlength="1">
              <input name="b" type="text" class="field 2" maxlength="1">
              <input name="c" type="text" class="field 3" maxlength="1">
              <input name="d" type="text" class="field 4" maxlength="1">
            </div>
            <!-- <button class="resend">
              Resend OTP
              <i class="fa fa-caret-right"></i>
            </button> -->
            <div class="md:w-10/12 text-center md:pl-6 item-center" >
              <button name='btn-send' class="text-white w-full mx-auto max-w-sm rounded-md text-center bg-indigo-400 py-2 px-4 inline-flex items-center focus:outline-none md:float-center">
                <div class="sn"> &nbsp; &nbsp; &nbsp; Verify OTP</div>
    
              </button>
            </div>
          </div>
      </form>
          <script src="script.js"></script>
    </body>
</html>