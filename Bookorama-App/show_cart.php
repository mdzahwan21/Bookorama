<!-- // File         : show_cart.php
// Deskripsi    : Untuk menambahkan item ke shopping cart dan menampilkan isi shopping cart

// TODO 1: Tuliskan session -->
<?php
session_start();
$id = isset($_GET['isbn']) ? $_GET['isbn'] : ""; // Get the ISBN from the URL
if ($id != "") {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
}

?>

<?php include('./header.php') ?>
<br>
<div class="card mt-4">
    <div class="card-header">Shopping Cart</div>
    <div class="card-body">
        <br>
        <table class="table table-striped">
            <tr>
                <th>ISBN</th>
                <th>Author</th>
                <th>Title</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
            <?php
            require_once('../Bookorama-App/lib/db_login.php');
            $sum_qty = 0; //inisialisasi total item di shopping cart
            $sum_price = 0; //inisialisasi total price di shopping cart
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $id => $quantity) {
                    // Pastikan $id adalah integer atau aman dari SQL injection
                    $id = $db->real_escape_string($id);

                    $query = "SELECT * FROM books WHERE isbn='" . $id . "'";
                    $result = $db->query($query);
                    if (!$result) {
                        die("Could not query the database: <br>" . $db->error . "<br>Query: " . $query);
                    }
                    while ($row = $result->fetch_object()) {
                        echo '<tr>';
                        echo '<td>' . $row->isbn . '</td>';
                        echo '<td>' . $row->author . '</td>';
                        echo '<td>' . $row->title . '</td>';
                        echo '<td>' . $row->price . '</td>';
                        echo '<td>' . $quantity . '</td>';
                        $price = $row->price;
                        echo '<td>' . $price * $quantity . '</td>';
                        $sum_price = $sum_price + ($row->price * $quantity);
                        $sum_qty = $sum_qty + $quantity;
                    }
                }
                echo '<tr><td></td><td></td><td></td><td></td><td>' . $sum_qty . '</td><td>' . $sum_price . '</td></tr>';
                $result->free();
                $db->close();
            } else {
                echo '<tr><td colspan="6" align="center">There is no item in shopping cart</td></tr>';
            }
            ?>
        </table>
        Total items = <?php echo $sum_qty; ?><br><br>
        <a class="btn btn-primary" href="./book_handler/view_books.php">Continue Shopping</a>
        <a class="btn btn-danger" href="delete_cart.php">Empty Cart</a><br /><br />
        <div class="float-right"> <!-- Add the float-right class here --><a class="btn btn-success" href="order_data.php">Order</a><br /><br />
        </div>
    </div>
</div>
<?php include('./footer.php') ?>