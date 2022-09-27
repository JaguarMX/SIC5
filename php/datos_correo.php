<?php
#DATOS DEL SERVIDOR

	$correo->SMTPDebug = SMTP::DEBUG_SERVER;
	$correo->isSMTP();
	$correo->Host = 'sicsom.com';
	$correo->SMTPAuth = true;
	$correo->Username = 'cortes@sicsom.com';
	$correo->Password = '3.NiOYNE(Txj';
	$correo->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	$correo->Port = 465;
?>