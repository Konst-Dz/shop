<?php
include "../elems/link.php";

if(isset($_SESSION['auth']) and $_SESSION['status'] == 'admin'){
    if(isset($_GET['users'])){

        changeStatus($connect);
        banUser($connect);
        getUsers($connect);
    }
    elseif (isset($_GET['goods'])){
        getItems($connect);
    }
    elseif (isset($_GET['statistics'])){
        getStats($connect);
    }
    else{
        showMainPage();
    }
}
else{
    header('Location:../pages/login.php');die();
}

function showMainPage(){
    echo "<a href=\"?users=true\">Пользователи</a><br>";
    echo "<a href=\"?goods=true\">Товары</a><br>";
    echo "<a href=\"?statistics=true\">Статистика</a><br>";
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

         $content .= "<tr><td>{$item['login']}</td><td>{$item['status']}</td>
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

function changeStatus($connect)
{
    if (isset($_GET['change']) and isset($_GET['status'])) {
        $id = $_GET['change'];
        $status = $_GET['status'];
        $status = ($status == 1) ? 2 : 1;


        $query = "UPDATE user SET id_status = '$status' WHERE id = '$id' ";
        mysqli_query($connect, $query) or die(mysqli_error($connect));
        $_SESSION['message'] = ['text' => 'Статус пользователя изменен',
            'status' => 'success'];
    }
}

function getItems($connect)
{
    $query = "SELECT *,item.uri as it_uri,item.id as item_id,category.name as cat_name,sub_category.name as sub_name FROM item LEFT JOIN sub_category ON item.id_sub = sub_category.id LEFT JOIN 
    category ON sub_category.id_category = category.id ";
    $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

     $content = '';
     $content = "<table><tr><th>Товар</th><th>Категория</th><th>Подкатегория</th>
     <th>Описание</th><th>Картинка</th><th>Цена</th><th>Uri</th><th>Редактировать</th><th>Удалить</th></tr>";
     foreach ($data as $item) {

     $content .= "<tr><td>{$item['name']}</td><td>{$item['cat_name']}</td><td>{$item['sub_name']}</td>
     <td>{$item['description']}</td><td>...</td><td>{$item['price']}</td><td>{$item['it_uri']}</td>
     <td><a href=\"dir/edit.php?edit={$item['item_id']}\">Редактировать</a></td>
     <td><a href=\"?delete={$item['item_id']}\">Удалить</a></td></tr>";

             }
             $content .= "</table>";
    $content .=addItem($connect);


     include_once "dir/layout.php";
}



function addItem($connect){
    if (isset($_GET['form'])) {
        if(isset($_POST['name']) and isset($_POST['description']) and isset($_POST['price']) and isset($_POST['uri'])){
            $name = $_POST['name'];
            $text = $_POST['description'];
            $price = $_POST['price'];
            $uri = $_POST['uri'];
            $sub = $_POST['sub_category'];
            if(!empty($_POST['name']) and !empty($_POST['description']) and !empty($_POST['price']) and !empty($_POST['uri'])){
                $query = "SELECT * FROM item WHERE name = '$name' ";
                $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
                $user = mysqli_fetch_assoc($data)['count'];
                if ($user){
                    $query = "INSERT INTO item SET name = '$name',description = '$text',price = '$price' ,uri = '$uri',id_sub='$sub' ";
                    mysqli_query($connect, $query) or die(mysqli_error($connect));
                    $_SESSION['message'] = ['text' => 'Товар успешно добавлен',
                    'status' => 'success'];
                }
                else{
                    $_SESSION['message'] = ['text' => 'Товар с таким названием существует',
                        'status' => 'error'];
                }
            }
            else{
                $_SESSION['message'] = ['text' => 'Введите все поля',
                'status' => 'error'];
                inputForm($connect,$name,$text,$price,$uri);
            }
        }
        return inputForm($connect);
    }
    else{
        return buttonAddForm();
    }
}

function inputForm($connect,$name='',$text='',$price='',$uri=''){
    $content = '';
    $content .= "<form method=\"POST\" action=\"\">";
    $content .= "<input type=\"text\" name=\"name\" value=\"$name\">Название товара<br><br>";
    $content .= selectCategory($connect,$category = '',$name='category');
    $content .= "<br>";
    $content .= selectCategory($connect,$category = '',$name='sub_category');
    $content .= "<br>";
    $content .= "Описание:<br><textarea type=\"text\" name=\"description\">$text</textarea><br><br>";
    //$content .= "<input type=\"text\" name=\"image\" value=\"$image\">Image<br><br>";
    $content .= "<input type=\"number\" name=\"price\" value=\"$price\">Цена<br><br>";
    $content .= "<input type=\"text\" name=\"uri\" value=\"$uri\">Uri<br><br>";
    $content .= "<input type=\"submit\" ><br></form>";
    var_dump($_POST);
    return $content;
}

function selectCategory($connect,$category = '',$name)
{
    if($name == 'category') {
        $title = 'Категория';
    }
    if ($name == 'sub_category'){
        $title = 'Подкатегория';
    }
    $title = ($name == 'category') ? 'Категория' : 'Подкатегория' ;

    $form = '';
    $form .= "{$title}:
    <select name=\"$name\">";
    $query = "SELECT * FROM $name ";
    $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
    for($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

    foreach ($data as $datum) {
        if($category) {
            //Проверка на selected для edit
            if ($datum['id'] == $category) {
                $checked = 'selected';
            }
            else{
                $checked = '';
            }
        }

        $form .= "<option value=\"{$datum['id']}\">{$datum['name']}</option>";
    }
    $form .= "</select>";
    return $form;
}

function buttonAddForm()
{
    $content = "<form method=\"GET\" action=\"\">";
    $content .= "<input type=\"hidden\" name=\"goods\" value=\"true\">";
    $content .= "<button name=\"form\" value=\"true\">Добавить товар</button></form>";

    return $content;
}