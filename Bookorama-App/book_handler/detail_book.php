<?php
require_once('../lib/db_login.php');

$isbn = $_GET['isbn'];
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

if (!isset($_POST['submit'])) {
} else {
    $valid = TRUE;
    $review = test_input($_POST['review']);
    if ($review == '') {
        $error_rev = 'Please write the review';
        $valid = FALSE;
    }

    if ($valid) {
        $insert = "INSERT INTO book_reviews (isbn, review) VALUES ('" . $isbn . "', '" . $review . "')";
        $insert_result = $db->query($insert);
        if (!$insert_result) {
            die("Could not query the database: <br />" . $db->error . '<br>Query: ' . $insert);
        } else {
            $db->close();
            header('Location: detail_book.php?isbn=' . $isbn);
        }
    }
}

?>
<?php include('../header.php') ?>
<br>
<div class="card mt-4">
    <div class="card-header">Detail Book</div>
    <div class="card-body">
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?isbn=' . $isbn ?>" method="post" autocomplete="on">
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" class="form-control" id="isbn" name="isbn" value="<?= $isbn; ?>" disabled>
                <div class="error">
                    <?php if (isset($error_isbn))
                        echo $error_isbn ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" class="form-control" id="author" name="author" value="<?= $author; ?>" disabled>
                <div class="error">
                    <?php if (isset($error_author))
                        echo $error_author ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= $title; ?>" disabled>
                <div class="error">
                    <?php if (isset($error_title))
                        echo $error_title ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?= $price; ?>"
                    disabled>
                <div class="error">
                    <?php if (isset($error_price))
                        echo $error_price ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category" class="form-control" disabled>
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
                <div class="error">
                    <?php if (isset($error_category))
                        echo $error_category ?>
                    </div>
                </div>
                <br>
                <h5>Reviews <span class="badge badge-secondary">New</span></h5>
                <table class="table table-striped">
                    <tr>
                        <th>No.</th>
                        <th>Review</th>
                    </tr>

                    <?php
                    $getReviews = "SELECT a.isbn, a.review FROM book_reviews a WHERE a.isbn='" . $isbn . "'";
                    $reviews = $db->query($getReviews);
                    if (!$reviews) {
                        die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $getReviews);
                    }

                    $i = 1;
                    while ($row = $reviews->fetch_object()) {
                        echo '<tr>';
                        echo '<td>' . $i . '</td>';
                        echo '<td>' . $row->review . '</td>';
                        echo '</tr>';
                        $i++;
                    }

                    $reviews->free();
                    ?>
            </table>

            <div class="form-group">
                <label for="author">Write Review:</label>
                <input type="text" class="form-control" id="review" name="review" value="">
                <?php if (!empty($error_rev)): ?>
                    <div class="alert alert-danger">
                        <?= $error_rev ?>
                    </div>
                <?php endif; ?>
            </div>
            <br />
            <button type="submit" class="btn btn-primary" name="submit" value="submit">+ Add Review</button>
            <a href="view_books.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
<?php include('../footer.php') ?>
<?php
$db->close();
?>