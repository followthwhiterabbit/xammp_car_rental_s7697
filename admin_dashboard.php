<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header('Location: index.php');
    exit;
}

require 'db.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['remove_car'])) {
        $car_id = $_POST['car_id'];

        // Check if the car is currently rented
        $stmt = $conn->prepare("SELECT * FROM rentals WHERE car_id = ? AND return_date IS NULL");
        $stmt->bind_param('i', $car_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Car is currently rented, cannot be removed
            $error = "Car is currently rented and cannot be removed.";
        } else {
            // Remove the car from available cars
            $stmt = $conn->prepare("UPDATE cars SET status = 'removed' WHERE id = ? AND status = 'available'");
            $stmt->bind_param('i', $car_id);
            if ($stmt->execute()) {
                $message = "Car removed successfully.";
            } else {
                $error = "Failed to remove the car.";
            }
        }
    }
}

$available_cars = $conn->query("SELECT * FROM cars WHERE status = 'available'");
$rented_cars = $conn->query("SELECT cars.*, users.username FROM cars JOIN rentals ON cars.id = rentals.car_id JOIN users ON rentals.user_id = users.id WHERE rentals.return_date IS NULL");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .car-details {
            display: none;
        }
        .car-item.expanded .car-details {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, Admin</h2>
        <a href="logout.php" class="logout">Logout</a>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="section">
            <h3>Available Cars</h3>
            <ul class="car-list">
                <?php while ($car = $available_cars->fetch_assoc()): ?>
                    <li class="car-item" onclick="toggleDetails(this)">
                        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['model']); ?>">
                        <div>
                            <h4><?php echo htmlspecialchars($car['model']); ?></h4>
                            <form method="post" action="admin_dashboard.php">
                                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                <button type="submit" name="remove_car" class="remove-car">Remove</button>
                            </form>
                        </div>
                        <div class="car-details">
                            <p>Manufacturer: <?php echo htmlspecialchars($car['manufacturer']); ?></p>
                            <p>Brand: <?php echo htmlspecialchars($car['brand']); ?></p>
                            <p>Model: <?php echo htmlspecialchars($car['model']); ?></p>
                            <p>Registration Plate: <?php echo htmlspecialchars($car['registration_plate']); ?></p>
                            <p>Type: <?php echo htmlspecialchars($car['type']); ?></p>
                            <p>Fuel Type: <?php echo htmlspecialchars($car['fuel_type']); ?></p>
                            <p>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></p>
                            <p>Mileage: <?php echo htmlspecialchars($car['mileage']); ?> kilometers</p>
                            <p>Additional Info: <?php echo htmlspecialchars($car['additional_info']); ?></p>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="section">
            <h3>Rented Cars</h3>
            <ul class="rental-list">
                <?php while ($car = $rented_cars->fetch_assoc()): ?>
                    <li class="rental-item" onclick="toggleDetails(this)">
                        <div>
                            <h4><?php echo htmlspecialchars($car['model']); ?> (Rented by: <?php echo htmlspecialchars($car['username']); ?>)</h4>
                        </div>
                        <div class="car-details">
                            <p>Manufacturer: <?php echo htmlspecialchars($car['manufacturer']); ?></p>
                            <p>Brand: <?php echo htmlspecialchars($car['brand']); ?></p>
                            <p>Model: <?php echo htmlspecialchars($car['model']); ?></p>
                            <p>Registration Plate: <?php echo htmlspecialchars($car['registration_plate']); ?></p>
                            <p>Type: <?php echo htmlspecialchars($car['type']); ?></p>
                            <p>Fuel Type: <?php echo htmlspecialchars($car['fuel_type']); ?></p>
                            <p>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></p>
                            <p>Mileage: <?php echo htmlspecialchars($car['mileage']); ?> kilometers</p>
                            <p>Additional Info: <?php echo htmlspecialchars($car['additional_info']); ?></p>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <script>
        function toggleDetails(element) {
            element.classList.toggle('expanded');
        }
    </script>
</body>
</html>
