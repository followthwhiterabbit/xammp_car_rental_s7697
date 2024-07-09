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

    if (isset($_POST['add_car'])) {
        $manufacturer = isset($_POST['manufacturer']) ? $_POST['manufacturer'] : '';
        $brand = isset($_POST['brand']) ? $_POST['brand'] : '';
        $model = isset($_POST['model']) ? $_POST['model'] : '';
        $registration_plate = isset($_POST['registration_plate']) ? $_POST['registration_plate'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $fuel_type = isset($_POST['fuel_type']) ? $_POST['fuel_type'] : '';
        $transmission = isset($_POST['transmission']) ? $_POST['transmission'] : '';
        $mileage = isset($_POST['mileage']) ? $_POST['mileage'] : '';
        $additional_info = isset($_POST['additional_info']) ? $_POST['additional_info'] : '';

        // File upload handling
        $target_dir = "uploads/";
        // Create the directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["car_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["car_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
            $error = "File is not an image.";
        }

        // Check file size
        if ($_FILES["car_image"]["size"] > 500000) {
            $uploadOk = 0;
            $error = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $uploadOk = 0;
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $error = "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO cars (manufacturer, brand, model, registration_plate, type, fuel_type, transmission, mileage, image_url, additional_info, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')");
                $stmt->bind_param('ssssssssss', $manufacturer, $brand, $model, $registration_plate, $type, $fuel_type, $transmission, $mileage, $target_file, $additional_info);

                if ($stmt->execute()) {
                    $message = "Car added successfully.";
                } else {
                    $error = "Error adding car: " . $stmt->error;
                }
            } else {
                $error = "Sorry, there was an error uploading your file.";
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

        <!-- Add car form -->
        <div class="section">
            <h3>Add a Car</h3>
            <form method="post" action="admin_dashboard.php" enctype="multipart/form-data">
                <label for="manufacturer">Manufacturer:</label>
                <input type="text" id="manufacturer" name="manufacturer" required>
                <label for="brand">Brand:</label>
                <input type="text" id="brand" name="brand" required>
                <label for="model">Model:</label>
                <input type="text" id="model" name="model" required>
                <label for="registration_plate">Registration Plate:</label>
                <input type="text" id="registration_plate" name="registration_plate" required>
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" required>
                <label for="fuel_type">Fuel Type:</label>
                <input type="text" id="fuel_type" name="fuel_type" required>
                <label for="transmission">Transmission:</label>
                <input type="text" id="transmission" name="transmission" required>
                <label for="mileage">Mileage:</label>
                <input type="number" id="mileage" name="mileage" required>
                <label for="additional_info">Additional Info:</label>
                <textarea id="additional_info" name="additional_info"></textarea>
                <label for="car_image">Upload Car Image:</label>
                <input type="file" id="car_image" name="car_image" accept="image/*" required>
                <button type="submit" name="add_car">Add Car</button>
            </form>
        </div>

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
