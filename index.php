<?php
include "elems/link.php";

var_dump($_SERVER['REQUEST_URI']);
$uri = $_SERVER['REQUEST_URI'];

if (isset($_GET['cat'])){
    getForumCategory($connect);
}
elseif(isset($_GET['topic'])){
    getTopicPosts($connect);
}
else{
    getMainPage($connect);
}


function pagination($connect,$rows,$page,$partHref)
{
//вычисление колв страниц
    $pages = ceil($rows / PAGES);

    $prev = $page - 1;
    $next = $page + 1;
    $pagination = '';
    $pagination .= "<div class=\"pages\">";
    //нерабочая ссылка
    if ($page != 1) {
        $disabled = '';
    } else {
        $disabled = 'disabled';
    }
    $pagination .= "<a href=\"{$partHref}page=$prev\" class=\" $disabled prev\" $disabled>Назад</a>";
    for ($i = 1; $i <= $pages; $i++) {
        //текущая страница
        if ($i == $page) {
            $active = 'active';
        } else {
            $active = "";
        }
        $pagination .= "<a class=\"$active\" href=\"{$partHref}page=$i\">$i</a>";
    }
    if ($page == $pages) {
        $dis = 'disabled';
    } else {
        $dis = '';
    }
    $pagination .= "<a href=\"{$partHref}page=$next\" class=\"$dis prev\">Вперед</a>";
    $pagination .= "</div>";
    return $pagination;

}

function add($connect,$category){
    if (isset($_GET['add'])){
            if (!empty($_POST['name']) and !empty($_POST['text'])) {
                $name = $_POST['name'];
                $text = $_POST['text'];
                $userId = $_SESSION['id'];
                $query = "INSERT INTO topic SET name = '$name', last_post = NOW() ,
            id_category = '$category'";
                mysqli_query($connect, $query) or die(mysqli_error($connect));
                $id = mysqli_insert_id($connect);
                $query = "INSERT INTO post SET text = '$text', date = NOW() ,
            id_topic = '$id' id_user = '$userId' ";
                mysqli_query($connect, $query) or die(mysqli_error($connect));
                $_SESSION['message'] = ['text' => 'Вы успешно создали тему',
                    'status' => 'success'];
                //header('Location:../index.php');
            } else{
                $name = $_POST['name'] ?? '';
                $text = $_POST['text'] ?? '';
            }


        $content = "<form method=\"POST\" action=\"\">";
        $content .= "Новая тема:<br>";
        $content .= "<input type=\"text\" name=\"name\" value=\"$name\" required><br>";
        $content .= "Пост:<br>";
        $content .= "<textarea name=\"text\" cols=\"30\" rows=\"10\" required>$text</textarea><br>";
        $content .= "<input type=\"submit\" ><br></form>";
        return $content;
    }
    else{
        return "<a href=\"?cat={$category}&add=in\">Создать тему</a>";
    }
}

function getTopicPosts($connect){
    $category = $_GET['topic'];
    $query = "SELECT * FROM topic WHERE id = '$category' ";
    $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
    $page = mysqli_fetch_assoc($data);
    $title = $page['name'];
    $partHref = "?topic={$category}&";
    if($page) {
        $page = $_GET['page'] ?? 1;
        $from = ($page - 1) * PAGES;
        $pages = PAGES;

        $content = addPost($connect,$category);
        $content = deletePost($connect);
        $query = "
            SELECT *,post.id as postId,user.id as usid,(SELECT COUNT(*) as count FROM post WHERE id_topic = '$category') as count
                 FROM post LEFT JOIN user ON user.id = post.id_user WHERE id_topic = '$category' ORDER BY date LIMIT $from,$pages ";
        $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
        $rows = mysqli_fetch_assoc($result)['count'];
        for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

        $content .= "<p><h2>Тема:$title</h2></p>";

        foreach ($data as $item) {
            $userId = $item['usid'];
            $postId = $item['postId'];
            $content .= "<p>{$item['login']} написал :</p>";
            $content .= "<p>{$item['text']}</p><hr>";
                if ($_SESSION['id'] == $userId or $_SESSION['status'] == 'admin' or $_SESSION['status'] == 'moder'){
                    $post = 'postdel';
                    $content .= buttonDelete($postId,$post);
                    if($_SESSION['id'] == $userId ) {
                        $content .= "<a href=\"edit={$postId}\">{$item['title']}</a>";
                    }
            }


        }
        if(!empty($_SESSION['auth'])){
            $content .= buttonAddPost($category);
            }
        $content .= pagination($connect, $rows, $page,$partHref);

        include "elems/layout.php";
    }
}

function addPost($connect,$id)
{
    if (isset($_GET['post'])) {
        if (!empty($_POST['text'])) {
            $text = $_POST['text'];
            $userId = $_SESSION['id'];
            $query = "INSERT INTO post SET text = '$text', date = NOW() ,
            id_topic = '$id', id_user = '$userId' ";
            mysqli_query($connect, $query) or die(mysqli_error($connect));
            $_SESSION['message'] = ['text' => 'Вы успешно отправили сообщение',
                'status' => 'success'];
            //header('Location:../index.php');
        }
    }
}

function deletePost($connect){
        if(isset($_POST['postdel'])) {
            $id = $_POST['postdel'];
            $query = "DELETE FROM post WHERE id = '$id' ";
            $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
        }
    if(isset($_POST['topdel'])) {
        $id = $_POST['topdel'];
        $query = "DELETE FROM topic WHERE id = '$id' ";
        $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
    }
}

function buttonAddPost($id){
    if(isset($_GET['post'])){
        $content = "<form method=\"POST\" action=\"\">";
        $content .= "Пост:<br>";
        $content .= "<textarea name=\"text\" cols=\"30\" rows=\"10\" required></textarea><br>";
        $content .= "<input type=\"submit\" ><br></form>";
        return $content;
    }
    else{
        return "<a href=\"?topic={$id}&post=in\">Ответить в тему</a>";
    }
}

function buttonDelete($postId,$post){
    $content = "<form method=\"POST\" action=\"\">";
    $content .= "<button name=\"$post\" value=\"$postId\">Удалить</button></form>";
    return $content;
}










