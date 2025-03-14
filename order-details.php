<?php
// Include database connection
include 'db_connect.php';

// Simplified query to get all orders without the join
$sql = "SELECT order_id, date, customer_id, quantity, price FROM `order`";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Dulcis Maison</title>
    <link rel="stylesheet" href="order.css">
    <script type="module" src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.js"></script>
    <style>
        .order-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-details-table th, .order-details-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .order-details-table th {
            background-color: #5b6d3d;
            color: white;
        }
        .order-details-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .order-details-table tr:hover {
            background-color: #e0e0e0;
        }
        .back-btn {
            background-color: #a3b18a;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            margin: 20px 0;
            text-decoration: none;
            display: inline-block;
        }
        .back-btn:hover {
            background-color: #8c9e6d;
        }
    </style>
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
                <li><a href="/M2_Summative_Assessment/index.php"><ion-icon name="home-outline"></ion-icon> Overview</a></li>
                <li><a href="staff.php"><ion-icon name="people-outline"></ion-icon> Staff</a></li>
                <li><a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a></li>
                <li class="active"><a href="order.php"><ion-icon name="cart-outline"></ion-icon> Order</a></li>
                <li><a href="inventory.php"><ion-icon name="cube-outline"></ion-icon> Inventory</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="content">
            <h2>Order Details</h2>
            <p>All orders in the system</p>
            
            <a href="order.php" class="back-btn">Back to Order Form</a>
            
            <table class="order-details-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer ID</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["order_id"] . "</td>";
                            echo "<td>" . $row["date"] . "</td>";
                            echo "<td>" . $row["customer_id"] . "</td>";
                            echo "<td>" . $row["quantity"] . "</td>";
                            echo "<td>₱ " . $row["price"] . "</td>";
                            echo "<td><a href='edit_order.php?id=" . $row["order_id"] . "'>Edit</a> | <a href='delete_order.php?id=" . $row["order_id"] . "'>Delete</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No orders found or " . $conn->error . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>