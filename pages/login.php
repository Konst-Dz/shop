<?php

include "../elems/link.php";

function loginForm()
{
    $content = "<form method=\"POST\">
        <input type=\"text\" name=\"login\">Login <br>
        <input type=\"password\" name=\"password\" id=\"\">Password <br>
        <input type=\"submit\"><br>
    </form>";
    include "../elems/layout.php";
}

function loginIn($connect){
    if (!isset($_SESSION['auth'])) {
        if (!empty($_POST['login']) and !empty($_POST['password'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];

            //селект по логину
            $query = "SELECT *,user.id as userId FROM user LEFT JOIN status ON user.id_status = status.id WHERE login = '$login' ";
            $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
            $user = mysqli_fetch_assoc($result);

            //проверка логина
            if ($user) {
                //проверка на бан
                if($user['banned'] == 0 ){
                $hash = $user['password'];
                //проверка хешей
                if (password_verify($password, $hash)) {
                    $_SESSION['id'] = $user['userId'];
                    $_SESSION['auth'] = true;
                    $_SESSION['login'] = $user['login'];
                    //статус
                    $_SESSION['status'] = $user['name'];

                    $_SESSION['message'] = ['text' => 'Вы авторизованы',
                        'status' => 'success'];
                    header('Location:../index.php');
                    die();


                } else {
                    echo "Wrong login or password";
                }

                }else{
                    echo "Вы забанены";
                }

            } else {
                echo "Wrong login or password";
            }

        }else{
            loginForm();
        }


    } else {
        $_SESSION['message'] = ['text' => 'You are already loggined.',
            'status' => 'success'];
        header('Location:../index.php');
        die();
    }
}

loginIn($connect);