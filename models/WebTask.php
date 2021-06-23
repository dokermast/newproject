<?php


class WebTask
{
    /**
     * return single user with specified id
     * @rapam integer &id
     */
    public static function getTaskById($id)
    {
        $id = intval($id);

        if ($id) {

            $db = Db::getConnection();
            $query = $db->prepare('SELECT * FROM tasks WHERE `id` = ?;');
            $query->execute(array($id));
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $task = $query->fetch();

            return $task;
        }
    }

    /**
    * return Tasks list
    */
    public static function getTasksList($sort, $offset = 0, $limit = 3, $order)
    {
        $db = Db::getConnection();
        $query = $db->prepare("SELECT count(id) AS count FROM `tasks`");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $count = $query->fetch();
        $count = (int)$count['count'];

        if ($count){
            $db = Db::getConnection();
            $sort = (isset($sort) && $sort != null) ? $sort : "id";
            $sql = "SELECT * FROM `tasks` ORDER BY {$sort} {$order} ";
            $sql .= "LIMIT {$offset}, {$limit}";
            $query = $db->prepare($sql);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $tasks = $query->fetchAll();

            return [
                'tasks' => $tasks,
                'count' => $count
                ];
        } else {

            return false;
        }
    }

     /**
     * add new Task
     */
    public static function addTask($name, $email, $text)
    {
        $db = Db::getConnection();
        $insert_row = $db->prepare("INSERT INTO tasks ( name, email, text ) VALUES( :name, :email, :text )");
        $insert_row->execute(array('name'=>$name,'email'=>$email, 'text'=>$text));

        return true;
    }

    /**
     * update Task data
     */
    public static function updateTask($id, $name, $email, $text, $status, $edited)
    {
        $db = Db::getConnection();
        $query = $db->prepare("UPDATE tasks SET `name`=:name, `email`=:email, `text`=:text, `status`=:status, `edited`=:edited WHERE `id` =:id");
        $query->execute(array('name'=>$name, 'email'=>$email,'text'=>$text, 'status'=>$status, 'edited'=>$edited, 'id'=>$id));

        return true;
    }
}
