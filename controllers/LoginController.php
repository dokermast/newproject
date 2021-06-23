<?php

require_once (ROOT.'/vendor/autoload.php');
require_once (ROOT.'/classes/Validation.php');


class LoginController
{
    /**
     * render Login Page
     */
    public function actionIndex(){

        $messages = NULL;

        if(isset($_SESSION["session_messages"])){
            $messages = $_SESSION['session_messages'];
            unset($_SESSION['session_messages']);
        }

        $loader = new \Twig\Loader\FilesystemLoader('views');
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('login.html');

        echo $template->render([
            'SITE'     => SITE,
            'messages' => $messages
        ]);

        return true;
    }

    /**
     * enter User
     */
    public function actionEnter(){

        if(isset($_POST) && count($_POST)>0 ) {

            if (!empty($_POST['name']) && !empty($_POST['password'])) {

                $name= Validation::clear_input($_POST['name']);
                $password= Validation::clear_input($_POST['password']);

                $check_creds_result = Validation::checkUserNameAndPassword($name, $password);

                if ( $check_creds_result['result'] ){

                    $_SESSION['session_username'] = $name;
                    header("Location: http://".$_SERVER['HTTP_HOST']."/".SITE."/");
                    exit();

                } else {
                    $_SESSION['session_messages'] =  $check_creds_result['messages'];
                    header("Location: http://".$_SERVER['HTTP_HOST']."/".SITE."/login/index");
                    exit();
                }

            } else {

                $messages[] = [
                    "status" => "warning",
                    "message" => 'ALL_FIELDS_REQUIRED'
                ];

                $_SESSION['session_messages'] = $messages;
                header("Location: http://" . $_SERVER['HTTP_HOST']."/".SITE."/login");
                exit();
            }
        }
    }


    /**
     * out User
     */
    public function actionOut(){

        unset($_SESSION['session_username']);
        session_destroy();
        header("Location: http://".$_SERVER['HTTP_HOST']."/".SITE."/");
        exit();
    }
}
