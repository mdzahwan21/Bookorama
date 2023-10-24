<!--File				: order_transact.php
	Date				: 25 September 2023
	Deskripsi			: file PHP dengan tujuan untuk melakukan SQL Transaction
	Anggota Kelompok	: 1. Adri Audifirst (24060121140152)
						  2. Emerio Kevin Aryaputra (24060121120012)
						  3. Mahardika Putra Wardhana (24060121130076)
						  4. Mochammad Dzahwan Fadhloly (24060121140168)
						  5. Yusuf Muhammad Najhan (24060121130048)
-->
<?php include('./header.php') ?>
<br>
<div class="card">
	<div class="card-header">Customers Data</div>
	<div class="card-body">
		<br>
		<p>Contoh SQL Transaction</p>

		<?php
		// Include our login information
		require_once('./lib/db_login.php');

		//start transaction
		$db->autocommit(FALSE);
		$db->begin_transaction();
		$query_ok = TRUE;

		//cek query
		$customerid = 1;
		$amount = 300;
		$date = '2022-06-01';
		$orderid = 2002;
		$books = array(
			'0-672-31697-8' => 1,
			'0-672-31769-9' => 2,
			'0-672-31509-2' => 3
		);

		try {
			// Prepare query1
			$query1 = "INSERT INTO orders VALUES (?, ?, ?, ?)";
			$stmt1 = $db->prepare($query1);

			// Check preparation query1
			if (!$stmt1) {
				$query_ok = FALSE;
				throw new Exception("Could not prepare the query1: <br />" . $db->error . "<br>Query: " . $stmt1);
			}

			// Bind param for query1
			$stmt1->bind_param("iisd", $orderid, $customerid, $amount, $date);

			// Execute query1
			if (!$stmt1->execute()) {
				$query_ok = FALSE;
				throw new Exception("Could not execute query1: <br />" . $stmt1->error . "<br>Query: " . $stmt1);
			}

			// Prepare query2
			$stmt2 = $db->prepare("INSERT INTO order_items VALUES (?, ?, ?)");

			// Check preparation query2
			if (!$stmt2) {
				$query_ok = FALSE;
				throw new Exception("Could not prepare the query2: <br />" . $db->error . "<br>Query: " . $stmt2);
			}

			// Bind param for query2
			$stmt2->bind_param("isi", $orderid, $isbn, $qty);

			foreach ($books as $isbn => $qty) {
				if (!$stmt2->execute()) {
					$query_ok = FALSE;
					throw new Exception("Could not execute query2: <br />" . $stmt2->error . "<br>Query: " . $stmt2);
				}
			}

			if ($query_ok) {
				$db->commit();
				echo "Eksekusi berhasil!!!";
			} else {
				throw new Exception("Eksekusi Gagal!!!");
			}
		} catch (Exception $e) {
			$db->rollback();
			echo $e->getMessage();
		} finally {
			$stmt1->close();
			$stmt2->close();
			$db->close();
		}

		?>
	</div>
</div>
<?php include('./footer.php') ?>