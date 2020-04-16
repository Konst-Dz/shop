<?php
function getMainPage($connect){
    $query = "SELECT * FROM category";
    $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

    $aside = "<p><h2>Категории товара:</h2></p>";

    foreach ($data as $item) {
        $aside .= "<p><a href=\"/{$item['uri']}/\">{$item['name']}</a></p>";
    }
    $title = "The shop";
    include "elems/layout.php";
}

function getSubCategory($connect,$uri){
    $query = "SELECT * FROM category WHERE uri = '$uri' ";
    $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
    $page = mysqli_fetch_assoc($data);
    if($page){
        $title = $page['name'];
        $id = $page['id'];
        $query = "SELECT * FROM sub_category WHERE id_category = '$id' ";
        $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
        for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

        $aside = "<p><h2>Категория {$title} :</h2></p>";

        foreach ($data as $item) {
            $aside .= "<p><a href=\"{$item['uri']}/\">{$item['name']}</a></p>";
        }
        include "elems/layout.php";

    }
    else{
        echo "net";
        //header("HTTP/1.0 404 Not Found");
    }
}

function showListItems($connect,$uri,$subUri){
    $query = "SELECT * FROM sub_category WHERE uri = '$subUri' ";
    $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
    $page = mysqli_fetch_assoc($data);
    if($page){
        $title = $page['name'];
        $id = $page['id'];
        $query = "SELECT * FROM item WHERE id_sub = '$id' ";
        $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
        for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

        $aside = "<p><h2>Подкатегория {$title} :</h2></p>";

        foreach ($data as $item) {
            $aside .= "<p><a href=\"{$item['uri']}/\">{$item['name']}</a></p>";
        }
        include "elems/layout.php";

    }
    else{
        echo "net";
        //header("HTTP/1.0 404 Not Found");
    }
}

function getItem($connect,$itemsUri){
    $query = "SELECT * FROM item WHERE uri = '$itemsUri' ";
    $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
    $page = mysqli_fetch_assoc($data);
    if($page){
        $title = $page['name'];
        $id = $page['id'];

        $content = "<p><h2> {$title} </h2></p>";

        $content .= "<p><h5> Описание: {$page['description']} </h5></p>";
        $content .= "<p><h5> Картинка: </h5></p>";
        $content .= "<p><h5> Цена: {$page['price']} р.</h5></p>";
        $content .= buttonCart($id);

        include "elems/layout.php";

    }
    else{
        echo "net";
        //header("HTTP/1.0 404 Not Found");
    }
}

function buttonCart($id){
    $content = "<form method=\"POST\" action=\"\">";
    $content .= "<button name=\"\" value=\"\">В корзину</button></form>";
    return $content;
}