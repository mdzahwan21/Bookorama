<?php
session_start();

if (isset($_GET['id']) && $_GET['id'] != "") {
    $id = $_GET['id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
}

include('./header.php');
require_once('../Bookorama-App/lib/db_login.php');
$sum_qty = 0; // Inisialisasi total item di shopping cart
$sum_price = 0; // Inisialisasi total price di shopping cart
$order_number = 0; // Inisialisasi nomor order

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

?>

<br>
<div class="card mt-4">
    <div class="card-header">Order Books</div>
    <div class="card-body">
        <br>
        <form method="POST">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            <button type="submit">Filter</button>
        </form>
        <br>

        <?php if (empty($_SESSION['cart'])) : ?>
            <p>No order!</p>
        <?php else : ?>
            <table class="table table-striped">
                <tr>
                    <th>No Order</th>
                    <th>Date</th>
                    <th>Detail Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                <?php
                foreach ($_SESSION['cart'] as $id => $quantity) {
                    $query = "SELECT * FROM books WHERE isbn='" . $id . "'";
                    $result = $db->query($query);
                    if (!$result) {
                        die("Could not query the database: <br>" . $db->error . "<br>Query: " . $query);
                    }
                    while ($row = $result->fetch_object()) {
                        $order_date = date("Y-m-d");
                        $total_price = $row->price * $quantity;
                        if (!isset($start_date) || !isset($end_date) || ($order_date >= $start_date && $order_date <= $end_date)) {
                            $order_number++;
                            echo '<tr>';
                            echo '<td>' . $order_number . '</td>';
                            echo '<td>' . $order_date . '</td>';
                            echo '<td>';
                            echo '<p>ISBN: ' . $row->isbn . '</p>';
                            echo '<p>Title: ' . $row->title . '</p>';
                            echo '<p>Author: ' . $row->author . '</p>';
                            echo '</td>';
                            echo '<td>' . $quantity . '</td>';
                            $price = $row->price;
                            echo '<td>' . $price . '</td>';
                            echo '<td>' . $total_price . '</td>';
                            echo '</tr>';
                        }
                    }
                }
                ?>
            </table>
        <?php endif; ?>
        <a href="./book_handler/view_books.php" class="btn btn-primary">Back</a>
    </div>
</div>