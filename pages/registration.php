<?php

//коннект
include "../elems/link.php";
//проверка авторизован или нет
if(empty($_SESSION['auth'])) {

    function getRegForm(){
        $content = '';
        $content .= "<form method=\"POST\" action=\"\">";
        $content .= "<input type=\"text\" name=\"login\"> Login<br><br>";
        $content .= "<input type=\"password\" name=\"password\"> Password<br><br>";
        $content .= "<input type=\"password\" name=\"confirm\"> Confirm Password<br> <br>";
        $content .= "<input type=\"submit\" ><br></form>";

        include "../elems/layout.php";
    }


    function confirmUser($connect){
    //проверка на заполнение
    if (!empty($_POST['login']) and !empty($_POST['password'])) {

        $login = $_POST['login'];
        //хэш
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        //проверка на соотв. пароля
        if ($_POST['password'] == $_POST['confirm']) {
            //длина пароля
            $passwordC = strlen($_POST['password']);

            //проверка длины
            if ($passwordC > 5 and $passwordC < 13) {

                //проверка на минимум
                if (preg_match('#^[a-z0-9]{3,}$#i', $login) == 1) {

                        //запрос на логин в БД
                        $query = "SELECT * FROM user WHERE login = '$login' ";
                        $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
                        $user = mysqli_fetch_assoc($data);

                        if (!$user) {

                            return true;

                        } else {
                            $text = "Логин занят.";
                        }

                } else {
                     $text = "Некорректный логин.";
                }


            }//длина
            else {
                $text = "Пароль должен быть не менее 6 символов и не более 12.";
            }

        } else {
            $text = " Введенные пароли не совпадают.";
        }

    }
    $_SESSION['message'] = ['text' => $text,
        'status' => 'error'];
    }

    function addNewUser($connect)
    {
        if(confirmUser($connect) == true) {

            $login = $_POST['login'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $query = "INSERT INTO user SET login = '$login',password = '$password',id_status = 1,banned = 0 ";
            mysqli_query($connect, $query) or die(mysqli_error($connect));

//немедленная авторизация
            $_SESSION['auth'] = true;
//запрос на ид и запись в сессию
            $id = mysqli_insert_id($connect);
            $_SESSION['login'] = $login;
            $_SESSION['id'] = $id;
            $_SESSION['status'] = 'user';
            $_SESSION['banned'] = 0;

            $_SESSION['message'] = ['text' => 'Регистрация прошла успешно',
                'status' => 'success'];

            header('Location:../index.php');die();
        }
    }
    addNewUser($connect);
    getRegForm($connect);
}else{
header('Location:login.php');die();
}
