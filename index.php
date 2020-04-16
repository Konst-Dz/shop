<?php
include "elems/link.php";
include_once "elems/functions/getMainPage.php";

var_dump($_SERVER['REQUEST_URI']);
$uri = trim(preg_replace('#^(.*/)(\?.*)$#', '$1',$_SERVER['REQUEST_URI']) , '/') ;
$uri = explode('/',$uri);
$categoryUri = $uri[0];
$subUri = $uri[1] ?? null;
$itemsUri = $uri[2] ?? null;
var_dump($uri);

if($categoryUri == "" or $categoryUri == "index.php"){
    getMainPage($connect);
}
elseif (isset($itemsUri)){
    getItem($connect,$itemsUri);
}
elseif(isset($subUri)){
    $uri = "{$categoryUri}/{$subUri}/";
    showListItems($connect,$uri,$subUri);
}
else{
    getSubCategory($connect,$categoryUri);
}
