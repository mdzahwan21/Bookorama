<?php
require_once('../lib/db_login.php');

$isbn = $_GET['isbn'];

if (!isset($_POST['submit'])) {
    $query = "SELECT b.isbn, b.author, b.title, b.price, c.name AS category_name 
                    FROM books b
                    INNER JOIN categories AS c ON b.categoryid = c.categoryid
                    WHERE b.isbn='" . $isbn . "'";
    $result = $db->query($query);
    if (!$result) {
        die("Could not query the database: <br />" . $db->error);
    } else {
        while ($row = $result->fetch_object()) {
            $isbn = $row->isbn;
            $author = $row->author;
            $title = $row->title;
            $price = $row->price;
            $category = $row->category_name;
        }
    }
} else {
    $valid = TRUE;

    $author = test_input($_POST['author']);
    if (empty($author)) {
        $error_author = 'Author is required';
        $valid = FALSE;
    }

    $title = test_input($_POST['title']);
    if (empty($title)) {
        $error_title = 'Title is required';
        $valid = FALSE;
    }

    $price = test_input($_POST['price']);
    if (empty($price)) {
        $error_price = 'Price is required';
        $valid = FALSE;
    }

    $category = $_POST['category'] ?? '';
    if (empty($category)) {
        $error_category = 'Category is required';
        $valid = FALSE;
    }

    if ($valid) {
        $query = "UPDATE books 
                    SET author = '" . $author . "',
                        title = '" . $title . "',
                        price = '" . $price . "',
                        categoryid = (SELECT categoryid FROM categories WHERE name = '" . $category . "')
                    WHERE isbn = '" . $isbn . "'
                ";
        $result = $db->query($query);
        if (!$result) {
            die("Could not query the database: <br />" . $db->error . '<br>Query: ' . $query);
        } else {
            $db->close();
            header('Location: view_books.php');
        }
    }
}

?>
<?php include('../header.php') ?>
<br>
<div class="card mt-4">
    <div class="card-header">Edit Book Data</div>
    <div class="card-body">
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?isbn=' . $isbn ?>" method="post" autocomplete="on">
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" class="form-control" id="isbn" name="isbn" value="<?= $isbn; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" class="form-control" id="author" name="author" maxlength="50" value="<?= $author; ?>">
                <?php if (!empty($error_author)) : ?>
                    <div class="alert alert-danger"><?= $error_author ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="100" value="<?= $title; ?>">
                <?php if (!empty($error_title)) : ?>
                    <div class="alert alert-danger"><?= $error_title ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?= $price; ?>">
                <?php if (!empty($error_price)) : ?>
                    <div class="alert alert-danger"><?= $error_price ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" id="category" class="form-control">
                    <option value="" selected disabled>--Select a Category--</option>
                    <?php
                    $query = 'SELECT name FROM categories';
                    $result = $db->query($query);

                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $categoryName = $row['name'];
                            $isSelected = ($category == $categoryName) ? 'selected' : '';

                            echo "<option value=\"$categoryName\" $isSelected>$categoryName</option>";
                        }

                        $result->free_result();
                    } else {
                        echo 'Error:' . $db->error;
                    }
                    ?>
                </select>
                <?php if (!empty($error_category)) : ?>
                    <div class="alert alert-danger"><?= $error_category ?></div>
                <?php endif; ?>
            </div>
            <br>
            <button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>
            <a href="view_books.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?php include('../footer.php') ?>
<?php
$db->close();
?>