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
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
        <a href="logout.php" class="logout">Logout</a>



    <table class="car-table">
        <tbody>
            <?php while ($car = $available_cars->fetch_assoc()): ?>
                <tr class="car-row" data-car-id="<?php echo $car['id']; ?>">
                    <td>
                        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['model']); ?>" style="width: 100px; height: auto;">
                        <h4><?php echo htmlspecialchars($car['model']); ?></h4>
                        <form method="post" action="dashboard.php">
                            <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                            <button type="submit" name="rent_car">Rent</button>
                        </form>
                    </td>
                </tr>
                <tr class="car-details" id="details-<?php echo $car['id']; ?>" style="display: none;">
                    <td colspan="2">
                        <p>Manufacturer: <?php echo htmlspecialchars($car['manufacturer']); ?></p>
                        <p>Brand: <?php echo htmlspecialchars($car['brand']); ?></p>
                        <p>Model: <?php echo htmlspecialchars($car['model']); ?></p>
                        <p>Registration Plate: <?php echo htmlspecialchars($car['registration_plate']); ?></p>
                        <p>Type: <?php echo htmlspecialchars($car['type']); ?></p>
                        <p>Fuel Type: <?php echo htmlspecialchars($car['fuel_type']); ?></p>
                        <p>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></p>
                        <p>Mileage: <?php echo htmlspecialchars($car['mileage']); ?> kilometers</p>
                        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['model']); ?>" style="width: 200px; height: auto;">
                        <p>Free Text: <?php echo htmlspecialchars($car['free_text']); ?></p>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
        


        <div class="section">
            <h3>Current Rentals</h3>
            <ul class="rental-list">
                <?php while ($rental = $current_rentals->fetch_assoc()): ?>
                    <li class="rental-item">
                        <div>
                            <h4><?php echo htmlspecialchars($rental['model'] . ' (Rented on: ' . $rental['rental_date'] . ')'); ?></h4>
                            <form method="post" action="dashboard.php">
                                <input type="hidden" name="rental_id" value="<?php echo $rental['rental_id']; ?>">
                                <input type="hidden" name="car_id" value="<?php echo $rental['car_id']; ?>">
                                <button type="submit" name="return_car">Return</button>
                            </form>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="section">
            <h3>Rental History</h3>
            <ul class="history-list">
                <?php while ($rental = $past_rentals->fetch_assoc()): ?>
                    <li class="history-item">
                        <div>
                            <h4><?php echo htmlspecialchars($rental['model'] . ' (Rented on: ' . $rental['rental_date'] . ', Returned on: ' . $rental['return_date'] . ')'); ?></h4>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>

</body>
</html>

