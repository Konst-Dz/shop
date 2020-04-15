<?php
session_start();
$_SESSION['auth'] = null;
$_SESSION['message'] = ['text' => 'You have been logout',
    'status' => 'success'];
header('Location:../index.php');