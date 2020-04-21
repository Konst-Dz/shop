<?php
include "../elems/link.php";

if(isset($_SESSION['auth']) and $_SESSION['status'] == 'admin'){
    if(isset($_GET['users'])){

        changeStatus($connect);
        banUser($connect);
        getUsers($connect);
    }
    elseif (isset($_GET['goods'])){
        if(isset($_GET['edit'])){
            editItem($connect);
        }
        else{
            deleteItem($connect);
            getItems($connect);
        }
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
    $content = '';
    $content .= "<a href=\"?users=true\">Пользователи</a><br>";
    $content .= "<a href=\"?goods=true\">Товары</a><br>";
    $content .= "<a href=\"?statistics=true\">Статистика</a><br>";
    $title = "Main admin page";
    include_once "dir/layout.php";
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
    $query = "SELECT *,item.name as it_name,item.uri as it_uri,item.id as item_id,category.name as cat_name,sub_category.name as sub_name,category.id as cat_id FROM item LEFT JOIN sub_category ON item.id_sub = sub_category.id LEFT JOIN 
    category ON sub_category.id_category = category.id ";
    $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

     $content = '';
     $content = "<table><tr><th>Товар</th><th>Категория</th><th>Подкатегория</th>
     <th>Описание</th><th>Картинка</th><th>Цена</th><th>Uri</th><th>Редактировать</th><th>Удалить</th></tr>";
     foreach ($data as $item) {

     $content .= "<tr><td>{$item['it_name']}</td><td>{$item['cat_name']}</td><td>{$item['sub_name']}</td>
     <td>{$item['description']}</td><td>...</td><td>{$item['price']}</td><td>{$item['it_uri']}</td>
     <td><a href=\"?goods=true&edit={$item['item_id']}&cat={$item['cat_id']}\">Редактировать</a></td>
     <td><a href=\"?goods=true&del={$item['item_id']}\">Удалить</a></td></tr>";

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
            $category = $_POST['category'];
            if(!empty($_POST['name']) and !empty($_POST['description']) and !empty($_POST['price']) and !empty($_POST['uri'])){
                $query = "SELECT * FROM item WHERE name = '$name' ";
                $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
                $user = mysqli_fetch_assoc($data)['count'];
                if (!$user){
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
                return inputForm($connect,$name,$text,$price,$uri,$category,$sub);
            }
        }
        return inputForm($connect);
    }
    else{
        return buttonAddForm();
    }
}

function inputForm($connect,$name='',$text='',$price='',$uri='',$category = '',$sub=''){
    $content = '';
    $content .= "<form method=\"POST\" action=\"\">";
    $content .= "<input type=\"text\" name=\"name\" value=\"$name\">Название товара<br><br>";
    $content .= selectCategory($connect,$category,$name='category');
    $content .= "<br>";
    $content .= selectCategory($connect,$sub,$name='sub_category');
    $content .= "<br>";
    $content .= "Описание:<br><textarea type=\"text\" name=\"description\">$text</textarea><br><br>";
    //$content .= "<input type=\"text\" name=\"image\" value=\"$image\">Image<br><br>";
    $content .= "<input type=\"number\" name=\"price\" value=\"$price\">Цена<br><br>";
    $content .= "<input type=\"text\" name=\"uri\" value=\"$uri\">Uri<br><br>";
    $content .= "<input type=\"submit\" ><br></form>";
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
        $checked = '';
        if($category) {
            //Проверка на selected для edit
            if ($datum['id'] == $category) {
                $checked = 'selected';
            }
        }


        $form .= "<option value=\"{$datum['id']}\" $checked>{$datum['name']}</option>";
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

function editItem($connect){
    if(isset($_GET['edit']) and isset($_GET['cat'])){
        $id = $_GET['edit'];
        $category = $_GET['cat'];
        $query = "SELECT * FROM item WHERE id = '$id' ";
        $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
        $user = mysqli_fetch_assoc($data);
        if (isset($user)){
            $name = $user['name'];
            $text = $user['description'] ?? '';
            $price = $user['price'];
            $uri = $user['uri'];
            $sub = $user['id_sub'];
            $content = inputForm($connect,$name,$text,$price,$uri,$category,$sub);
            if (!empty($_POST['name']) and !empty($_POST['description']) and !empty($_POST['price'])
                and !empty($_POST['uri'])){
                $name = $_POST['name'];
                $text = $_POST['description'];
                $price = $_POST['price'];
                $uri = $_POST['uri'];
                $sub = $_POST['sub_category'];
                $category = $_POST['category'];
                $query = "SELECT COUNT(*) as count FROM item WHERE name = '$name' OR uri = '$uri'  ";
                $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
                $user = mysqli_fetch_assoc($data)['count'];
                if ($user == 1 ){
                    $query = "UPDATE item SET name = '$name',description = '$text',price = '$price' ,uri = '$uri',id_sub='$sub' WHERE id = '$id' ";
                    mysqli_query($connect, $query) or die(mysqli_error($connect));
                    $_SESSION['message'] = ['text' => 'Запись успешно обновилась',
                    'status' => 'success'];
                    header('Location:/admin/index.php');
                }
                else{
                    $_SESSION['message'] = ['text' => 'Запись с таким названием или uri существует',
                    'status' => 'error'];
                    $content = inputForm($connect,$name,$text,$price,$uri,$category,$sub);
                }
            }

        }
    }
    $title = "Edit page";
    include_once "dir/layout.php";
}

function deleteItem($connect){
    if(isset($_GET['del'])){
        $id = $_GET['del'];
        $query = "DELETE FROM item WHERE id = '$id'";
        mysqli_query($connect, $query) or die(mysqli_error($connect));
        $_SESSION['message'] = ['text' => 'Удалено успешно',
        'status' => 'success'];
    }
}