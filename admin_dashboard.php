<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header('Location: index.php');
    exit;
}

require 'db.php';

$all_cars = $conn->query("SELECT * FROM cars");
$all_rentals = $conn->query("SELECT users.username, cars.model, rentals.rental_date, rentals.return_date FROM rentals JOIN users ON rentals.user_id = users.id JOIN cars ON rentals.car_id = cars.id");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <a href="logout.php">Logout</a>

    <h3>All Cars</h3>
    <ul>
        <?php while ($car = $all_cars->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($car['model'] . ' (' . $car['status'] . ')'); ?></li>
        <?php endwhile; ?>
    </ul>

    <h3>All Rentals</h3>
    <ul>
        <?php while ($rental = $all_rentals->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($rental['username'] . ' rented ' . $rental['model'] . ' on ' . $rental['rental_date'] . ' (Returned on: ' . $rental['return_date'] . ')'); ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
