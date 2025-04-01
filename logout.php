<?php
session_start(); 

// Détruire toutes les variables de session
$_SESSION = [];

session_destroy();

header("Location: index.php");
exit();

