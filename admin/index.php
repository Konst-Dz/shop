<?php
include "../elems/link.php";

if(isset($_SESSION['auth']) and $_SESSION['status'] == 'admin'){
    changeStatus($connect);
    banUser($connect);
    getUsers($connect);
}
else{
    header('Location:../pages/login.php');die();
}

function getUsers($connect){

     $title = 'Админка';
     $query = "SELECT *,user.id as usid FROM user LEFT JOIN status ON user.id_status = status.id";
     $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
     for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

         $content = '';
         $content = "<table><tr><th>Логин</th><th>Статус</th><th>Блокировка</th><th>Смена статуса</th>
         <th>Блокировка</th></tr>";
         foreach ($data as $item) {

         $banned = $item['banned'];
         $statusBlock = $banned ? "Забанен" : "Нет";
         $block = $banned ? "Разбанить" : "Забанить";

         $content .= "<tr><td>{$item['login']}</td><td>{$item['name']}</td>
         <td>$statusBlock</td>
         <td><a href=\"?change={$item['usid']}&status={$item['id_status']}\">Сменить статус</a></td>
         <td><a href=\"?block={$item['usid']}&ban={$banned}\">$block</a></td></tr>";

                 }
         $content .= "</table>";

         include_once "dir/layout.php";
}

function banUser($connect){
    if (isset($_GET['block']) and isset($_GET['ban'])){
        $id = $_GET['block'];
        $ban = $_GET['ban'];
        $ban = ($ban) ? 0 : 1 ;

        $query = "UPDATE user SET banned = '$ban' WHERE id = '$id' ";
        mysqli_query($connect, $query) or die(mysqli_error($connect));
        $_SESSION['message'] = ['text' => 'Статус пользователя изменен',
        'status' => 'success'];
    }
}

function changeStatus($connect){
    if (isset($_GET['change']) and isset($_GET['status'])){
        $id = $_GET['change'];
        $status = $_GET['status'];

        /*$status = ($status == 1) ?  2 :  1 ;*/

        switch ($status){
            case 1:
                $status = 2;
                break;
            case 2:
                $status = 3;
                break;
            case 3:
                $status = 1;
                break;
        }

        $query = "UPDATE user SET id_status = '$status' WHERE id = '$id' ";
        mysqli_query($connect, $query) or die(mysqli_error($connect));
        $_SESSION['message'] = ['text' => 'Статус пользователя изменен',
            'status' => 'success'];
    }
}
