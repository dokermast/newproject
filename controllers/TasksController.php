<?php

include_once ROOT. '/models/WebTask.php';
require_once ROOT.'/vendor/autoload.php';
require_once ROOT.'/classes/Validation.php';

class TasksController
{
    /**
     * return Main Page
     */
    public function actionIndex()
    {
        define("PER_PAGE", 3);
        $pagin = [];

        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        } else {
            if (isset($_SESSION['sort'])) {
                $sort = $_SESSION['sort'];
            } else {
                $sort = 'id';
            }
        }

        if (isset($_GET['page'])) {
            $pagin['curpage'] = $_GET['page'];
        } else {
            if (isset($_SESSION['page'])) {
                $pagin['curpage'] = $_SESSION['page'];
            } else {
                $pagin['curpage'] = 1;
            }
        }

        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        } else {
            if (isset($_SESSION['order'])) {
                $order = $_SESSION['order'];
            } else {
                $order = 'ASC';
            }
        }

        $pagin['offset'] = ($pagin['curpage'] * PER_PAGE) - PER_PAGE;
        $pagin['link'] = "http://".$_SERVER['HTTP_HOST']."/".SITE."/?sort=".$sort."&order=".$order."&page=";

        $data = WebTask::getTasksList($sort, $pagin['offset'], PER_PAGE,  $order);
        $tasks = $data['tasks'];
        $taskscount = $data['count'];

        $pagin['pagecount'] = ceil((int)$taskscount / PER_PAGE);
        $pagin['next'] = ceil($taskscount/PER_PAGE - $pagin['curpage']);

        $loader = new \Twig\Loader\FilesystemLoader('views');
        $twig = new \Twig\Environment($loader);

        /** session BLOCk */
        $_SESSION["sort"] = $sort;
        $_SESSION["page"] = $pagin['curpage'];

        $messages = [];
        if(isset($_SESSION["session_messages"])){
            $messages = $_SESSION["session_messages"];
            unset($_SESSION['session_messages']);
        }

        $username = null;
        if(isset($_SESSION["session_username"])){
            $username = $_SESSION["session_username"];
        }

        $sort_name_up = "http://".$_SERVER['HTTP_HOST']."/".SITE."/list/?sort=name&order=ASC&page=".$pagin['curpage'];
        $sort_email_up= "http://".$_SERVER['HTTP_HOST']."/".SITE."/list/?sort=email&order=ASC&page=".$pagin['curpage'];
        $sort_status_up = "http://".$_SERVER['HTTP_HOST']."/".SITE."/list/?sort=status&order=ASC&page=".$pagin['curpage'];
        $sort_name_down = "http://".$_SERVER['HTTP_HOST']."/".SITE."/list/?sort=name&order=DESC&page=".$pagin['curpage'];
        $sort_email_down = "http://".$_SERVER['HTTP_HOST']."/".SITE."/list/?sort=email&order=DESC&page=".$pagin['curpage'];
        $sort_status_down = "http://".$_SERVER['HTTP_HOST']."/".SITE."/list/?sort=status&order=DESC&page=".$pagin['curpage'];

        $template = $twig->load('task/list.html');

        echo $template->render([
            'SITE'             => SITE,
            'tasks'            => $tasks,
            'messages'         => $messages,
            'username'         => $username,
            'sort_name_up'     => $sort_name_up,
            'sort_email_up'    => $sort_email_up,
            'sort_status_up'   => $sort_status_up,
            'sort_name_down'   => $sort_name_down,
            'sort_email_down'  => $sort_email_down,
            'sort_status_down' => $sort_status_down,
            'pagecount'        => $pagin['pagecount'],
            'paginlink'        => $pagin['link'],
            'curpage'          => $pagin['curpage'],
            'next'             => $pagin['next']
        ]);

        return true;
    }

    /**
     * return Create Task Form
     */
    public function actionCreate()
    {
        $loader = new \Twig\Loader\FilesystemLoader('views');
        $twig = new \Twig\Environment($loader);

        $messages = [];
        if(isset($_SESSION["session_messages"])){
            $messages = $_SESSION["session_messages"];
            unset($_SESSION['session_messages']);
        }

        $template = $twig->load('task/create.html');

        echo $template->render([
            'SITE'    => SITE,
            'messages'=>$messages,
        ]);

        return true;
    }

    /**
     *  save Task
     */
    public function actionSave()
    {
        if(isset($_POST) && count($_POST)>0 ) {

            if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['text'])) {

                $name = Validation::clear_input($_POST['name']);
                $email = Validation::clear_input($_POST['email']);
                $text = Validation::clear_input($_POST['text']);

                $error_messages = [];

                $validateEmailResult = Validation::validateEmail($email);
                if (isset($validateEmailResult) && count($validateEmailResult) > 0){
                    $error_messages[] = $validateEmailResult;
                }

                if (count($error_messages) == 0) {

                    if (WebTask::addTask($name, $email, $text)) {

                        $messages[] = [
                            "status" => "success",
                            'message' => "task was created"
                        ];

                        $_SESSION['session_messages'] = $messages;
                        header("Location: http://" . $_SERVER['HTTP_HOST']."/".SITE."/");
                        exit();
                    }

                } else {
                    $messages = $error_messages;

                    $_SESSION['session_messages'] = $messages;
                    header("Location: http://" . $_SERVER['HTTP_HOST']."/".SITE."/create");
                    exit();
                }

            } else {

                $messages[] = [
                    "status" => "warning",
                    "message" => 'ALL_FIELDS_REQUIRED'
                ];

                $_SESSION['session_messages'] = $messages;
                header("Location: http://" . $_SERVER['HTTP_HOST']."/".SITE."/create");
                exit();
            }
        } else {
            $messages[] = [
                "status" => "warning",
                "message" => 'ALL_FIELDS_ARE+EMPTY'
            ];

            $_SESSION['session_messages'] = $messages;
            header("Location: http://" . $_SERVER['HTTP_HOST']."/".SITE."/create");
            exit();
        }
    }

    /**
    * return Task edit Form
    */
    public function actionEdit($id)
    {
        if(isset($_SESSION["session_username"]) && $_SESSION["session_username"] == 'admin'){

            $task = WebTask::getTaskByID($id);

            $loader = new \Twig\Loader\FilesystemLoader('views');
            $twig = new \Twig\Environment($loader);

            $messages = [];
            if(isset($_SESSION["session_messages"])){
                $messages = $_SESSION["session_messages"];
                unset($_SESSION['session_messages']);
            }

            $checked = ($task['status']) ? 'checked' : null;

            $template = $twig->load('task/edit.html');
            echo $template->render([
                'SITE'          => SITE,
                'task'          => $task,
                'messages'      => $messages,
                'checked'       => $checked
            ]);

            return true;

        } else {

            header("Location: http://". $_SERVER['HTTP_HOST']."/".SITE."/login");
            exit();
        }
    }

    /**
     * render Update TASK Data
     */
    public function actionUpdate()
    {
        if(isset($_SESSION["session_username"]) && $_SESSION["session_username"] == 'admin'){

            if(isset($_POST["form"])){

                $id = $_POST['id'];
                $task = WebTask::getTaskById($id);
                $old_text = $task['text'];
                $edited = $task['edited'];

                if(!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['text'])) {

                    $name = Validation::clear_input($_POST['name']);
                    $email = Validation::clear_input($_POST['email']);
                    $text = Validation::clear_input($_POST['text']);
                    $status = (isset($_POST['status'])) ? (int)$_POST['status'] : 0 ;

                    if (!$edited) {
                        $edited = ($old_text != $text) ? 1 : 0;
                    }

                    $error_messages = [];

                    $validateEmailResult = Validation::validateEmail($email);
                    if (isset($validateEmailResult) && count($validateEmailResult) > 0){
                        $error_messages[] = $validateEmailResult;
                    }

                    if (count($error_messages) == 0) {

                        if(WebTask::updateTask($id, $name, $email, $text, $status, $edited)){

                            $messages[] = [
                                "status" => "success",
                                "message" => "TASK was updated successfully"
                            ];
                            $_SESSION['session_messages'] = $messages;
                        }

                        header("Location: http://".$_SERVER['HTTP_HOST']."/".SITE."/");
                        exit();

                    } else {

                        $messages = $error_messages;
                        $_SESSION['session_messages'] = $messages;
                        header("Location: http://".$_SERVER['HTTP_HOST']."/".SITE."/edit/".$id);
                        exit();
                    }
                } else {
                    $messages[] = [
                        "status" => "warning",
                        "message" => 'ALL_FIELDS_REQUIRED'
                    ];

                    $_SESSION['session_messages'] = $messages;
                    header("Location: http://" . $_SERVER['HTTP_HOST']."/".SITE."/edit/".$id);
                    exit();
                }
            }
        } else {

            header("Location: http://". $_SERVER['HTTP_HOST']."/".SITE."/login");
            exit();
        }
    }
}
