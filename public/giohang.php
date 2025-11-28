<?php
session_start();
include("../config/db.php");

$action = $_GET['action'] ?? '';

if ($action == "add") {
    $id = $_POST['id'];
    $session = session_id();

    $check = $conn->query("SELECT * FROM cart WHERE product_id=$id AND session_id='$session'");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE product_id=$id AND session_id='$session'");
    } else {
        $conn->query("INSERT INTO cart(product_id, quantity, session_id)
                      VALUES($id, 1, '$session')");
    }

    header("Location: cart.php");
    exit;
}

$session = session_id();
$result = $conn->query("
    SELECT cart.*, products.name, products.price, products.image 
    FROM cart 
    JOIN products ON cart.product_id = products.id
    WHERE session_id='$session'
");
?>
