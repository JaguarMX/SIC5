<?php
#DATOS DEL SERVIDOR
$Host = 'smtp.gmail.com';
$Username = 'sic.redes.som@gmail.com';
$Password = 'Respif_rede5';
$SMTPSecure = 'tls';
$Port = 587;

$mail = new PHPMailer\PHPMailer\PHPMailer();

#AGREGAMOS LOS ATRIBUTOS DEL CORREO DESDE EL CUAL SE ENVIARA
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = $Host;
$mail->SMTPAuth = true;
$mail->Username = $Username;
$mail->Password = $Password;
$mail->SMTPSecure = $SMTPSecure;
$mail->Port = $Port;
?>