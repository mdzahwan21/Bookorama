<?php
include('../header.php');
require_once('../lib/db_login.php');
?>

<div class="card mt-5">
    <div class="card-header">Books Data</div>
    <div class="card-body">
        <a href="add_book.php" class="btn btn-primary mb-4">+ Add Book Data</a>
        <a href="../order_data.php" class="btn btn-dark mb-4">Check Order</a>
        <a href="view_books_by_categories.php" class="btn btn-dark mb-4">View by Categories</a>
        <a href="recap_book.php" class="btn btn-dark mb-4">View Books Recap</a>
        <a href="../logout.php" class="btn btn-danger mb-4 float-right">Logout</a>
        <div class="form-group">
            <form action="view_books.php" method="get">
                <input type="text" class="form-control mb-2" name="search_key" placeholder="Search your books">
                <select name="category" id="category" class="form-control mb-2">
                    <option value="" selected disabled>--Select a Category--</option>
                    <?php
                    $query = 'SELECT name FROM categories';
                    $result = $db->query($query);

                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $category_name = $row['name'];
                            echo "<option value=\"$category_name\">$category_name</option>";
                        }

                        $result->free_result();
                    } else {
                        echo 'Error: ' . $db->error;
                    }
                    ?>
                </select>
                <input type="number" class="form-control mb-2" name="min_price" placeholder="Min Price" step="0.01">
                <input type="number" class="form-control mb-2" name="max_price" placeholder="Max Price" step="0.01">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>
        <br>

        <?php
        // Inisialisasi variabel pencarian
        $searched = false;

        // Memeriksa apakah ada parameter pencarian yang diberikan
        if (
            isset($_GET['search_key']) ||
            isset($_GET['category']) ||
            isset($_GET['min_price']) ||
            isset($_GET['max_price'])
        ) {
            $searched = true;
        }
        ?>

        <?php if ($searched): ?>
            <?php
            // TODO 1: Tuliskan dan eksekusi query
            $query = 'SELECT 
                                books.isbn, 
                                books.title, 
                                categories.name AS category_name, 
                                books.author, 
                                books.price 
                            FROM books 
                            INNER JOIN categories ON books.categoryid = categories.categoryid
                        ';

            $search_key = '';
            $category_filter = '';
            $min_price = '';

            if (isset($_GET['search_key']) && !empty($_GET['search_key'])) {
                $search_key = $_GET['search_key'];
                $query .= " WHERE (books.isbn LIKE '%$search_key%' 
                                OR books.title LIKE '%$search_key%'
                                OR books.author LIKE '%$search_key%')";
            }

            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category_filter = $_GET['category'];
                if ($search_key != '') {
                    $query .= " AND categories.name = '$category_filter'";
                } else {
                    $query .= " WHERE categories.name = '$category_filter'";
                }
            }

            if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                $min_price = $_GET['min_price'];
                if ($search_key != '' || $category_filter != '') {
                    $query .= " AND books.price >= '$min_price'";
                } else {
                    $query .= " WHERE books.price >= '$min_price'";
                }
            }

            if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                $max_price = $_GET['max_price'];
                if ($search_key != '' || $category_filter != '' || $min_price != '') {
                    $query .= " AND books.price <= '$max_price'";
                } else {
                    $query .= " WHERE books.price <= '$max_price'";
                }
            }

            $query .= ' ORDER BY books.isbn';

            $result = $db->query($query);
            if (!$result) {
                die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $query);
            }
            ?>

            <?php if ($result->num_rows > 0): ?>
                <table class="table table-striped">
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    $i = 1;
                    while ($row = $result->fetch_object()):
                        ?>
                        <tr>
                            <td>
                                <?= $row->isbn ?>
                            </td>
                            <td>
                                <?= $row->title ?>
                            </td>
                            <td>
                                <?= $row->category_name ?>
                            </td>
                            <td>
                                <?= $row->author ?>
                            </td>
                            <td>$
                                <?= $row->price ?>
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="detail_book.php?isbn=<?= $row->isbn ?>">Detail</a>&nbsp;&nbsp;
                                <a class="btn btn-primary btn-sm" href="../show_cart.php?isbn=<?= $row->isbn ?>">+
                                    Cart</a>&nbsp;&nbsp;
                                <a class="btn btn-warning btn-sm" href="edit_book.php?isbn=<?= $row->isbn ?>">Edit</a>&nbsp;&nbsp;
                                <a class="btn btn-danger btn-sm" href="confirm_delete_book.php?isbn=<?= $row->isbn ?>">Delete</a>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    endwhile;
                    ?>

                </table>
                <br />
                Total Rows =
                <?= $result->num_rows ?>
            <?php else: ?>
                <p>Book not found!</p>
            <?php endif; ?>
        <?php else: ?>
            <?php
            // Tampilkan semua data jika tidak ada parameter pencarian
            $query = 'SELECT 
                            books.isbn, 
                            books.title, 
                            categories.name AS category_name, 
                            books.author, 
                            books.price 
                        FROM books 
                        INNER JOIN categories ON books.categoryid = categories.categoryid
                        ORDER BY books.isbn';

            $result = $db->query($query);
            if (!$result) {
                die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $query);
            }
            ?>

            <?php if ($result->num_rows > 0): ?>
                <table class="table table-striped">
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    $i = 1;
                    while ($row = $result->fetch_object()):
                        ?>
                        <tr>
                            <td>
                                <?= $row->isbn ?>
                            </td>
                            <td>
                                <?= $row->title ?>
                            </td>
                            <td>
                                <?= $row->category_name ?>
                            </td>
                            <td>
                                <?= $row->author ?>
                            </td>
                            <td>$
                                <?= $row->price ?>
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="detail_book.php?isbn=<?= $row->isbn ?>">Detail</a>&nbsp;&nbsp;
                                <a class="btn btn-primary btn-sm" href="../show_cart.php?isbn=<?= $row->isbn ?>">+
                                    Cart</a>&nbsp;&nbsp;
                                <a class="btn btn-warning btn-sm" href="edit_book.php?isbn=<?= $row->isbn ?>">Edit</a>&nbsp;&nbsp;
                                <a class="btn btn-danger btn-sm" href="confirm_delete_book.php?isbn=<?= $row->isbn ?>">Delete</a>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    endwhile;
                    ?>

                </table>
                <br />
                Total Rows =
                <?= $result->num_rows ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        $result->free();
        $db->close();
        ?>
    </div>
</div>
<script>
    window.onload = function () {
        history.replaceState({}, document.title, 'view_books.php');
    }
</script>


<script src="ajax_search_books.js"></script>

<?php include('../footer.php') ?>