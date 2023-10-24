<?php include('../header.php') ?>
<div class="card mt-5">
    <div class="card-header">Books Data by Category</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr class="bg-primary text-white text-center align-middle table-dark">
                <th>Category</th>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Price</th>
            </tr>
            <?php
            // Include our login information
            require_once('../lib/db_login.php');

            // TODO 1: Tuliskan dan eksekusi query
            $book_query = 'SELECT 
                            books.isbn, 
                            books.title,  
                            books.author, 
                            books.price,
                            books.categoryid,
                            categories.name AS category_name 
                        FROM books 
                        INNER JOIN categories ON books.categoryid = categories.categoryid
                        ORDER BY categories.name
                    ';
            $result = $db->query($book_query);
            if (!$result) {
                die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $book_query);
            }

            $counting_array = array();
            $printed = array();

            while ($row = $result->fetch_assoc()) {
                $category_id = $row['categoryid'];

                if (!isset($counting_array[$category_id])) {
                    $counting_array[$category_id]['rowspan'] = 0;
                    $counting_array[$category_id]['name'] = $row['category_name'];
                    $counting_array[$category_id]['books'] = array();
                }

                $counting_array[$category_id]['books'][] = $row;
                $counting_array[$category_id]['rowspan'] += 1;
            }

            foreach ($counting_array as $category_id => $category_info) {
                $rowspan = $category_info['rowspan'];
                $category_name = $category_info['name'];
                $books = $category_info['books'];

                echo '<tr>';

                if (!isset($printed[$category_id])) {
                    echo '<td rowspan="' . $rowspan . '" class="text-black text-center align-middle">' . $category_name . '</td>';
                    $printed[$category_id] = true;
                }

                foreach ($books as $book) {
                    echo '<td class="text-center align-middle">' . $book['isbn'] . '</td>';
                    echo '<td>' . $book['title'] . '</td>';
                    echo '<td>' . $book['author'] . '</td>';
                    echo '<td class="text-center align-middle">' . $book['price'] . '</td>';
                    echo '</tr>';
                }
            }

            $result->free();
            $db->close();
            ?>
        </table>
        <a href="view_books.php" class="btn btn-primary mb-4">Back</a>
    </div>
</div>
<?php include('../footer.php') ?>