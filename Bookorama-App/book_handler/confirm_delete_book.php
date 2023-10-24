<?php
require_once('../lib/db_login.php');

if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];

    $query = "SELECT b.isbn, b.author, b.title, b.price, c.name AS category_name 
                FROM books b
                INNER JOIN categories AS c ON b.categoryid = c.categoryid
                WHERE b.isbn='" . $isbn . "'";
    $result = $db->query($query);
    if (!$result) {
        die("Could not query the database: <br />" . $db->error);
    }

    if ($result->num_rows == 1) {
        $book = $result->fetch_object();
    } else {
        die('Book not found');
    }
}

if (isset($_POST['delete'])) {
    $query = "DELETE FROM books WHERE isbn = '" . $isbn . "'";
    $result = $db->query($query);

    if ($result) {
        $db->close();
        header('Location: view_books.php');
        exit;
    } else {
        die("Error deleting book: " . $db->error);
    }
}

?>
<?php include('../header.php') ?>
<div class="card mt-5">
    <div class="card-header">Confirm Book Deletion</div>
    <div class="card-body">
        <h4>Are you sure you want to delete this book?</h4>
        <p><strong>ISBN: </strong><?= $book->isbn ?></p>
        <p><strong>Title: </strong><?= $book->title ?></p>
        <p><strong>Author: </strong><?= $book->author ?></p>
        <p><strong>Price: </strong><?= $book->price ?></p>
        <p><strong>Category: </strong><?= $book->category_name ?></p>

        <form action="<?= $_SERVER['PHP_SELF'] . '?isbn=' . $isbn ?>" method="post">
            <button type="submit" class="btn btn-danger" name="delete">Delete</button>
            <a class="btn btn-secondary" href="view_books.php">Cancel</a>
        </form>
    </div>
</div>
<?php include('../footer.php') ?>