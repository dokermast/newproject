<?php


class Validation
{
    /**
     * cheking Password
     * @param $password
     * @return array
     */
    public static function checkUserPassword($password){

        $db = Db::getConnection();
        $query = $db->prepare("SELECT * FROM users WHERE `password` = ? ");
        $query->execute(array(md5($password)));
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $user = $query->fetch();

        if ($user) {

            return [
                "result" => true,
            ];

        } else {

            return  [
                "result" => false,
                "status" => "danger",
                "message" => "Password is not correct!"
            ];
        }
    }

    /**
     * checking
     * @param $name
     * @param $password
     * @return array
     */
    public static function checkUserNameAndPassword($name, $password){

        $db = Db::getConnection();
        $query = $db->prepare("SELECT * FROM users WHERE `name` = ? ");
        $query->execute(array($name));
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $userName = $query->fetch();

        $messages = [];

        if (!$userName) {
            $message = [
                "result" => false,
                "status" => "warning",
                "message" => 'USER_NOT_REGISTERED'
            ];
            $messages[] = $message;
        }

        if ($userName && $userName['password'] != md5($password)) {
            $message = [
                "result" => false,
                "status" => "danger",
                "message" => "Password is not correct!"
            ];
            $messages[] = $message;
        }

        if (count($messages) > 0){

            return [
                "result" => false,
                "messages" => $messages
            ];
        } else {

            return [ "result" => true ];
        }
    }

    /**
     *  preparing input data for saving
     * @param $data
     * @return string
     */
    public static function clear_input($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }

    /**
     * validate Email params
     * @param $email
     * @param $lang
     * @return array
     */
    public static function validateEmail($email){

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){

            return [
                'result' => false,
                "status" => "danger",
                "message" => 'EMAIL_NOT_CORRECT'
            ];
        }
    }
}
