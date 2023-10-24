<?php
require_once('../lib/db_login.php');

// Inisialisasi pesan error
$errors = array(
    'isbn' => '',
    'author' => '',
    'title' => '',
    'price' => '',
    'category' => ''
);

function isbn_exists_in_database($isbn)
{
    global $db; // Variabel koneksi ke database

    $query = "SELECT COUNT(*) AS count FROM books WHERE isbn = '$isbn'";
    $result = $db->query($query);

    if (!$result) {
        die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $query);
    }

    $row = $result->fetch_assoc();
    $count = $row['count'];

    return $count > 0;
}

if (isset($_POST['submit'])) {
    $valid = true;

    $isbn = test_input($_POST['isbn']);
    if (empty($isbn)) {
        $errors['isbn'] = 'ISBN is required';
        $valid = false;
    } elseif (isbn_exists_in_database($isbn)) {
        $errors['isbn'] = 'ISBN already exists in the database';
        $valid = false;
    }

    $author = test_input($_POST['author']);
    if (empty($author)) {
        $errors['author'] = 'Author is required';
        $valid = false;
    }

    $title = test_input($_POST['title']);
    if (empty($title)) {
        $errors['title'] = 'Title is required';
        $valid = false;
    }

    $price = test_input($_POST['price']);
    if (empty($price)) {
        $errors['price'] = 'Price is required';
        $valid = false;
    }

    $category = $_POST['category'] ?? '';
    if (empty($category)) {
        $errors['category'] = 'Category is required';
        $valid = false;
    }

    if ($valid) {
        // Kueri
        $query = "INSERT INTO books 
                    VALUES ('" . $isbn . "', 
                            '" . $author . "', 
                            '" . $title . "', 
                            '" . $price . "', 
                            (SELECT categoryid FROM categories WHERE name = '" . $category . "'))
                ";

        // Eksekusi kueri
        $result = $db->query($query);
        if (!$result) {
            die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $query);
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
    <div class="card-header">Add Book Data</div>
    <div class="card-body">
        <form action="add_book.php" method="POST" autocomplete="on">
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" class="form-control" id="isbn" name="isbn" maxlength="13" value="<?= isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : '' ?>">
                <?php if (!empty($errors['isbn'])) : ?>
                    <div class="alert alert-danger"><?= $errors['isbn'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" class="form-control" id="author" name="author" maxlength="50" value="<?= isset($_POST['author']) ? htmlspecialchars($_POST['author']) : '' ?>">
                <?php if (!empty($errors['author'])) : ?>
                    <div class="alert alert-danger"><?= $errors['author'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="100" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                <?php if (!empty($errors['title'])) : ?>
                    <div class="alert alert-danger"><?= $errors['title'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>">
                <?php if (!empty($errors['price'])) : ?>
                    <div class="alert alert-danger"><?= $errors['price'] ?></div>
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
                            $isSelected = isset($_POST['category']) && $_POST['category'] === $categoryName ? 'selected' : '';
                            echo "<option value=\"$categoryName\" $isSelected>$categoryName</option>";
                        }

                        $result->free_result();
                    } else {
                        echo 'Error:' . $db->error;
                    }
                    ?>
                </select>
                <?php if (!empty($errors['category'])) : ?>
                    <div class="alert alert-danger"><?= $errors['category'] ?></div>
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