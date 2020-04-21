<?php
if($_SERVER['REQUEST_URI'] == '/' or $_SERVER['REQUEST_URI'] == '/index.php'){
    $mainPage = '';
}
else {
    $mainPage = "<a href=\"/index.php\">Главная</a><br>";
}
if(!empty($_SESSION['auth'])){
    $id = $_SESSION['id'];
    $login = $_SESSION['login'];

    echo "Добрый день, $login ({$_SESSION['status']})<br>";
    echo $mainPage;
    echo "<a href=\"../pages/logout.php\">Выйти</a><br>";
    if($_SESSION['status'] == 'moder' or $_SESSION['status'] == 'admin'){
        echo "<a href=\"/../admin/index.php\">Админка</a><br>";
    }
    echo "<a href=\"../pages/profile.php\">Личный кабинет</a><br>";

}
else{
    echo "Добрый день, Гость <br>";
    echo $mainPage;
    echo  "<a href=\"../pages/login.php\">Login</a>";
    echo  "<a href=\"../pages/registration.php\">Registration</a>";
}

