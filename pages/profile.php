<?php
include "../elems/link.php";
var_dump($_SESSION['cart']);
Cart($connect);

function Cart($connect){

    if(isset($_SESSION['cart'])){
        $cart = $_SESSION['cart'];
        $cart = array_count_values($cart);
        $itemsId=implode(',',$cart);
        var_dump($itemsId);

        $query = "SELECT * FROM item WHERE id IN ('$itemsId') ";
        $data = mysqli_query($connect, $query) or die(mysqli_error($connect));
        $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
        for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;


            $content = '';
            $content = "<table><tr><th>Товар</th><th>Цена</th><th>Колличество</th></tr>";
            foreach ($data as $item) {
                 $key = $item['id'];
                 $content .= "<tr><td>{$item['name']}</td><td>{$item['price']}</td><td>$cart[$key]</td>
                 </td></tr>";

            }
            $content .= "</table>";
    }
    include "../elems/layout.php";
}