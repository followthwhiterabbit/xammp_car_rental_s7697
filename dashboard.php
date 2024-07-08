<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

require 'db.php';

$username = $_SESSION['username'];

$stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rent_car'])) {
        $car_id = $_POST['car_id'];
        $conn->query("UPDATE cars SET status = 'rented' WHERE id = $car_id");
        $conn->query("INSERT INTO rentals (user_id, car_id) VALUES ($user_id, $car_id)");
    } elseif (isset($_POST['return_car'])) {
        $rental_id = $_POST['rental_id'];
        $car_id = $_POST['car_id'];
        $conn->query("UPDATE cars SET status = 'available' WHERE id = $car_id");
        $conn->query("UPDATE rentals SET return_date = NOW() WHERE id = $rental_id");
    }
}

$available_cars = $conn->query("SELECT * FROM cars WHERE status = 'available'");
$current_rentals = $conn->query("SELECT rentals.id as rental_id, cars.id as car_id, cars.model, rentals.rental_date FROM cars JOIN rentals ON cars.id = rentals.car_id WHERE rentals.user_id = $user_id AND rentals.return_date IS NULL");
$past_rentals = $conn->query("SELECT cars.model, rentals.rental_date, rentals.return_date FROM cars JOIN rentals ON cars.id = rentals.car_id WHERE rentals.user_id = $user_id AND rentals.return_date IS NOT NULL");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
    <a href="logout.php">Logout</a>

    <h3>Available Cars</h3>
    <ul>
        <?php while ($car = $available_cars->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($car['model']); ?>
                <form method="post" action="dashboard.php">
                    <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                    <button type="submit" name="rent_car">Rent</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <h3>Current Rentals</h3>
    <ul>
        <?php while ($rental = $current_rentals->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($rental['model'] . ' (Rented on: ' . $rental['rental_date'] . ')'); ?>
                <form method="post" action="dashboard.php">
                    <input type="hidden" name="rental_id" value="<?php echo $rental['rental_id']; ?>">
                    <input type="hidden" name="car_id" value="<?php echo $rental['car_id']; ?>">
                    <button type="submit" name="return_car">Return</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <h3>Rental History</h3>
    <ul>
        <?php while ($rental = $past_rentals->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($rental['model'] . ' (Rented on: ' . $rental['rental_date'] . ', Returned on: ' . $rental['return_date'] . ')'); ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
