<?php
session_start();

$host = "localhost";
$db   = "casinova_demo";
$user = "casinova_user";
$pass = "12345"; // geçici, sonra gerçek veritabanına göre değiştireceğiz

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>
