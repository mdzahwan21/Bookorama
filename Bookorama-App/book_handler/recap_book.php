<?php include('../header.php') ?>
<script src="https://cdn.syncfusion.com/ej2/dist/ej2.min.js" type="text/javascript"></script>

<br>
<?php require_once('../lib/db_login.php'); ?>
<div class="card mt-4">
    <div class="card-header">Rekap Buku</div>
    <div class="card-body">
        <div id="container"></div>
        <?php
        $query = "SELECT c.name AS category_name, COUNT(b.isbn) AS book_count
                            FROM categories c
                            LEFT JOIN books b ON c.categoryid = b.categoryid
                            GROUP BY c.categoryid
                            ORDER BY c.name";
        $result = $db->query($query);
        if (!$result) {
            die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $query);
        }

        //array untuk data grafik
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'category_name' => $row['category_name'],
                'book_count' => (int)$row['book_count']
            );
        }

        //konversi agar bisa digunakan di JS
        $json_data = json_encode($data);

        ?>
        <script>
            var data = <?php echo $json_data; ?>;
            var categories = [];
            var bookCounts = [];
            for (var i = 0; i < data.length; i++) {
                categories.push(data[i].category_name);
                bookCounts.push(data[i].book_count);
            }

            var chart = new ej.charts.Chart({
                chartArea: {
                    border: {
                        width: 0
                    }
                },
                primaryXAxis: {
                    title: 'Categories', //Sumbu x
                    valueType: 'Category'
                },
                primaryYAxis: {
                    title: 'Number of Books', //Sumbu y
                    labelFormat: '{value}', //Tanpa desimal
                    edgeLabelPlacement: 'Shift',
                    interval: 1
                },
                series: [{
                    type: 'Column',
                    dataSource: data,
                    xName: 'category_name',
                    yName: 'book_count',
                    marker: {
                        dataLabel: {
                            visible: true,
                            position: 'Top',
                            font: {
                                fontWeight: '600'
                            }
                        }
                    }
                }],
                legendSettings: {
                    visible: false
                },
                title: 'REKAP JUMLAH BUKU PER KATEGORI'
            });

            chart.appendTo('#container');
        </script>
        <br><br><br><br>

        <!--Grafik Data Buku Terorder-->
        <div id="container2"></div>
        <?php
        $query = "SELECT c.name AS category_name, SUM(subquery.quantity) AS book_count
                    FROM categories c
                    LEFT JOIN (SELECT b.categoryid, oi.quantity FROM order_items oi INNER JOIN books b ON oi.isbn = b.isbn) AS subquery
                    ON c.categoryid = subquery.categoryid
                    GROUP BY c.name
                    ORDER BY c.name;";
        $result = $db->query($query);
        if (!$result) {
            die('Could not query the database: <br/>' . $db->error . '<br>Query:' . $query);
        }

        //array untuk data grafik
        $data2 = array();
        while ($row = $result->fetch_assoc()) {
            $data2[] = array(
                'category_name' => $row['category_name'],
                'book_count' => (int)$row['book_count']
            );
        }

        //konversi agar bisa digunakan di JS
        $json_data = json_encode($data2);

        ?>
        <script>
            var data2 = <?php echo $json_data; ?>;
            var categories = [];
            var bookCounts = [];
            for (var i = 0; i < data2.length; i++) {
                categories.push(data2[i].category_name);
                bookCounts.push(data2[i].book_count);
            }

            var chart = new ej.charts.Chart({
                chartArea: {
                    border: {
                        width: 0
                    }
                },
                primaryXAxis: {
                    title: 'Categories', //Sumbu x
                    valueType: 'Category'
                },
                primaryYAxis: {
                    title: 'Number of Books', //Sumbu y
                    labelFormat: '{value}', //Tanpa desimal
                    edgeLabelPlacement: 'Shift',
                    interval: 1
                },
                series: [{
                    type: 'Column',
                    dataSource: data2,
                    xName: 'category_name',
                    yName: 'book_count',
                    marker: {
                        dataLabel: {
                            visible: true,
                            position: 'Top',
                            font: {
                                fontWeight: '600'
                            }
                        }
                    }
                }],
                legendSettings: {
                    visible: false
                },
                title: 'REKAP JUMLAH BUKU YANG TELAH DIORDER'
            });
            chart.appendTo('#container2');
        </script>
        <br>
        <a href="view_books.php" class="btn btn-primary">Back</a>
        </form>
    </div>
</div>
<?php include('../footer.php') ?>
<?php
$db->close();
?>