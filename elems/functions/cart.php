<?php
function buttonCart($id){
    $content = "<form method=\"POST\" action=\"\">";
    $content .= "<button name=\"cart\" value=\"$id\">В корзину</button></form>";
    return $content;
}

function addInCart(){
    if(isset($_POST['cart'])){
        $id = $_POST['cart'];
        $_SESSION['cart'][] = $id;
        $_SESSION['message'] = ['text' => 'Товар добавлен в корзину',
        'status' => 'success'];
    }
}
