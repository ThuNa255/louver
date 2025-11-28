<?php
include("../config/db.php");

$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
?>

<h2><?= $product['name'] ?></h2>
<img src="assets/images/<?= $product['image'] ?>">
<p>Giá: <?= number_format($product['price']) ?>đ</p>
<p><?= $product['description'] ?></p>

<form action="cart.php?action=add" method="POST">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <button type="submit">Thêm vào giỏ</button>
</form>
