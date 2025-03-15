<?php
// Include database connection
include 'db_connect.php';

// Initialize variables
$total_sales = 0;
$total_income = 0;
$total_staff = 0;

// Get total sales (count of orders)
try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM `order`");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $total_sales = $row['total'];
    }
    $stmt->close();
} catch (Exception $e) {
    // Handle error
    echo "Error fetching total sales: " . $e->getMessage();
}



// Get total income (sum of order costs)
try {
    $stmt = $conn->prepare("SELECT SUM(price) AS total FROM order_details");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $total_income = $row['total'] ? number_format($row['total'], 2) : 0;
    }
    $stmt->close();
} catch (Exception $e) {
    // Handle error
}

// Get total staff count
try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM employee");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $total_staff = $row['total'];
    }
    $stmt->close();
} catch (Exception $e) {
    // Handle error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview - Dulcis Maison</title>
    <link rel="stylesheet" href="overview.css">
    <script type="module" src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-container">
                <img src="images/Logo.png" alt="Dulcis Maison Logo" class="logo-img">
                <div class="logo-text">Dulcis Maison</div>
            </div>
            <ul class="nav-menu">
                <li class="active"><a href="index.php"><ion-icon name="home-outline"></ion-icon> Overview</a></li>
                <li><a href="staff.php"><ion-icon name="people-outline"></ion-icon> Staff</a></li>
                <li><a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a></li>
                <li><a href="order.php"><ion-icon name="cart-outline"></ion-icon> Order</a></li>
                <li><a href="inventory.php"><ion-icon name="cube-outline"></ion-icon> Inventory</a></li>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="content">
            <h2>Overview</h2>
            <p>Track and manage inventory and sales</p>
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <ion-icon name="bar-chart-outline"></ion-icon>
                    <h3>Total Sales:</h3>
                    <p>₱ <?php echo number_format($total_sales); ?></p>
                </div>
                <div class="card">
                    <ion-icon name="cash-outline"></ion-icon>
                    <h3>Total Income:</h3>
                    <p>₱ <?php echo number_format($total_income); ?></p>
                </div>
                <div class="card">
                    <ion-icon name="person-outline"></ion-icon>
                    <h3>Total Staff:</h3>
                    <p><?php echo $total_staff; ?></p>
                </div>
            </div>
            <!-- Banner Image -->
            <div class="banner">
                <img src="images/DULCIS MAISON.png" alt="Sample Pic">
            </div>
        </main>
    </div>
</body>
</html>