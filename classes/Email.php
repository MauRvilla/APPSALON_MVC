<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email{

    public $email;
    public $nombre;
    public $token;

    public function __construct($email,$nombre,$token)
    {
        $this->email=$email;
        $this->nombre=$nombre;
        $this->token=$token;
    }

    public function enviarConfirmacion(){
        //crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '2ea48d3467179d';
        $mail->Password = 'e2926022cfcd66';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject='Confirma tu cuenta';

        // Set HTML 
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido="<html>";
        $contenido.="<p><strong>Hola ".$this->nombre."</strong> Has creado tu cuenta en App salon,
        solo debes confirmarla presionando el siguiente enlace </p>";
        $contenido.="<p>Presiona aqui: <a href='http://localhost:3000/confirmar-cuenta?token=". $this->token ."'>
        Confirmar Cuenta </a> </p>";
        $contenido .="<p>Si tu no solicitaste este correo, puedes ignorar el mensaje";
        $contenido.="</html>";

        $mail->Body=$contenido;

        //Enviar el email
        $mail->send();
    }

    public function enviarInstrucciones(){
        //crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '2ea48d3467179d';
        $mail->Password = 'e2926022cfcd66';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject='Restablece tu password';

        // Set HTML 
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido="<html>";
        $contenido.="<p><strong>Hola ".$this->nombre."</strong> Has solicitado restablecer tu password presiona
        el siguiente enlace </p>";
        $contenido.="<p>Presiona aqui: <a href='http://localhost:3000/recuperar?token=". $this->token ."'>
        Restablecer Password </a> </p>";
        $contenido .="<p>Si tu no solicitaste este correo, puedes ignorar el mensaje";
        $contenido.="</html>";

        $mail->Body=$contenido;

        //Enviar el email
        $mail->send();
    }

}

?>