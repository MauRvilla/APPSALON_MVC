<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{

    public static function login(Router $router){
        $alertas=[];
        //$auth=new Usuario;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth=new Usuario($_POST);
            $alertas=$auth->validarLogin();//validamos campos de login

            if (empty($alertas)) {
                //comprobar si existe el usuario
                $usuario=Usuario::where('email', $auth->email);

                if ($usuario) {
                    //verificar password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        //Autenticar usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre." ".$usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redirrecionamiento
                        if ($usuario->admin === "1") {
                            $_SESSION['admin']=$usuario->admin ?? null;
                            header('Location: /admin');
                        }else{
                            header('Location: /cita');
                        }
                    }
                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        //cargar alertas
        $alertas=Usuario::getAlertas();
        $router->render('auth/login',[
            'alertas' => $alertas
            //'auth' => $auth
        ]);

    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        //debuguear($_SESSION);
        header('Location: /');
    }

    public static function olvide(Router $router){
        $alertas=[];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas=$auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado === "1") {
                    //generar token
                    $usuario->crearToken();
                    $usuario->guardar();

                    $email=new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu email');
                }else{
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');
                }
                //debuguear($usuario);
            }
        }

        $alertas=Usuario::getAlertas();
        $router->render('auth/olvide-password',[
            'alertas' => $alertas
        ]);    
    
    }

    public static function recuperar(Router $router){
        $alertas=[];
        $error=false;

        $token = s($_GET['token']);

        //Buscar usuario por token
        $usuario= Usuario::where('token',$token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token Invalido');
            $error=true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Leer el nuevo pasword y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if (empty($alertas)) {
                
                $usuario->password=null;
                $usuario->password=$password->password;
                $usuario->hashPassword();
                $usuario->token=null;

                $resultado=$usuario->guardar();

                if ($resultado) {
                    header('Location: /');
                }
            }
            
        }

        $alertas=Usuario::getAlertas();
        $router->render('auth/recuperar-password',[
            'alertas' => $alertas,
            'error' => $error
        ]);

    }

    public static function crear(Router $router){

        $usuario=new Usuario;
        $alertas=[];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $usuario->sincronizar($_POST);
            $alertas=$usuario->validarNuevaCuenta();
            
            //Revisar que alertas este vacio
            if (empty($alertas)) {
                // revisar si ya esta registrado
                $resultado=$usuario->existeUsuario();
                if ($resultado->num_rows) {
                    $alertas=Usuario::getAlertas();
                }else{
                    //hashear el password
                    $usuario->hashPassword();

                    //generar token
                    $usuario->crearToken();

                    //enviar el email
                    $email=new Email($usuario->email,$usuario->nombre,$usuario->token);
                    
                    $email->enviarConfirmacion();

                    //Crear el usuario
                    $resultado=$usuario->guardar();
                    if ($resultado) {
                        header("Location:/mensaje");
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta',[
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas=[];
        $token = s($_GET['token']);//capturamos el token de la url
        
        $usuario=Usuario::where('token',$token);//consultamos la BD con el token
        if (empty($usuario)) {//validamos que el usuario exista con el token
            //si el token no es correcto mostramos mensaje
            Usuario::setAlerta('error','Token Inválido');
        }else{
            //Modificamo a usuario confirmado
            $usuario->confirmado= '1';
            $usuario->token=null;
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta confirmada Correctamente');
            //debuguear($usuario);
        }
        //Obtener alertas
        $alertas = Usuario::getAlertas();

        //renderizar vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}


?>