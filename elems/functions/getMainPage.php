<?php
function getMainPage($connect){
    $query = "SELECT * FROM category";
    $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

    $content = "<p><h2>Категории форума</h2></p>";

    foreach ($data as $item) {
        $content .= "<p><a href=\"?cat={$item['id']}\">{$item['name']}</a></p>";
    }
    $title = "The shop";
    include "elems/layout.php";
}

