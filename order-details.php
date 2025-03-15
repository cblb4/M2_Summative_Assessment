<?php
// Include database connection
include 'db_connect.php';

// Fetch all orders with additional information
$query = "SELECT o.order_id, o.date, o.customer_id, o.employee_id, o.quantity, o.price, 
                 d.delivery_id, d.date AS delivery_date, d.departure, d.arrival, d.fee,
                 p.packaging_id, p.quantity AS packaging_quantity, p.type, p.size, p.price AS packaging_price
          FROM `order` o
          LEFT JOIN delivery d ON o.order_id = d.order_id
          LEFT JOIN packaging p ON o.order_id = p.order_id
          ORDER BY o.date DESC";
$result = $conn->query($query);
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
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .back-button {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4a6741;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .order-details-expanded {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin-top: 5px;
            display: none;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        .detail-section {
            background-color: #fff;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .detail-section h4 {
            margin-top: 0;
            color: #4a6741;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }
        .show-details-btn {
            background-color: #4a6741;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        .show-details-btn:hover {
            background-color: #5a7751;
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
                <li><a href="index.html"><ion-icon name="home-outline"></ion-icon> Overview</a></li>
                <li><a href="staff.php"><ion-icon name="people-outline"></ion-icon> Staff</a></li>
                <li><a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a></li>
                <li class="active"><a href="order.php"><ion-icon name="cart-outline"></ion-icon> Order</a></li>
                <li><a href="/M2_Summative_Assessment/inventory.php"><ion-icon name="cube-outline"></ion-icon> Inventory</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="content">
            <h2>Order Details</h2>
            <p>All orders in the system</p>
            
            <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
                <div class="alert alert-success">
                    Order has been successfully deleted.
                </div>
            <?php endif; ?>
            
            <div class="back-button">
                <a href="order.php" class="btn">Back to Order Form</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer ID</th>
                        <th>Employee ID</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr data-order-id="<?php echo $row['order_id']; ?>">
                                <td><?php echo $row['order_id']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($row['date'])); ?></td>
                                <td><?php echo $row['customer_id']; ?></td>
                                <td><?php echo $row['employee_id']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td>₱<?php echo number_format($row['price'], 2); ?></td>
                                <td class="actions">
                                    <button class="show-details-btn" onclick="toggleDetails(<?php echo $row['order_id']; ?>)">Details</button>
                                </td>
                            </tr>
                            <tr data-details-id="<?php echo $row['order_id']; ?>">
                                <td colspan="7" style="padding: 0;">
                                    <div id="details-<?php echo $row['order_id']; ?>" class="order-details-expanded">
                                        <div class="detail-grid">
                                            <div class="detail-section">
                                                <h4>Order Details</h4>
                                                <p><strong>Order ID:</strong> <?php echo $row['order_id']; ?></p>
                                                <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($row['date'])); ?></p>
                                                <p><strong>Customer ID:</strong> <?php echo $row['customer_id']; ?></p>
                                                <p><strong>Employee ID:</strong> <?php echo $row['employee_id']; ?></p>
                                                <p><strong>Quantity:</strong> <?php echo $row['quantity']; ?></p>
                                                <p><strong>Price:</strong> ₱<?php echo number_format($row['price'], 2); ?></p>
                                            </div>
                                            
                                            <div class="detail-section">
                                                <h4>Delivery Details</h4>
                                                <?php if (!empty($row['delivery_id'])): ?>
                                                    <p><strong>Delivery ID:</strong> <?php echo $row['delivery_id']; ?></p>
                                                    <p><strong>Delivery Date:</strong> <?php echo date('Y-m-d', strtotime($row['delivery_date'])); ?></p>
                                                    <p><strong>Departure:</strong> <?php echo date('Y-m-d', strtotime($row['departure'])); ?></p>
                                                    <p><strong>Arrival:</strong> <?php echo date('Y-m-d', strtotime($row['arrival'])); ?></p>
                                                    <p><strong>Fee:</strong> ₱<?php echo number_format($row['fee'], 2); ?></p>
                                                <?php else: ?>
                                                    <p>No delivery information available</p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="detail-section">
                                                <h4>Packaging Details</h4>
                                                <?php if (!empty($row['packaging_id'])): ?>
                                                    <p><strong>Packaging ID:</strong> <?php echo $row['packaging_id']; ?></p>
                                                    <p><strong>Quantity:</strong> <?php echo $row['packaging_quantity']; ?></p>
                                                    <p><strong>Type:</strong> <?php echo $row['type']; ?></p>
                                                    <p><strong>Size:</strong> <?php echo $row['size']; ?></p>
                                                    <p><strong>Price:</strong> ₱<?php echo number_format($row['packaging_price'], 2); ?></p>
                                                <?php else: ?>
                                                    <p>No packaging information available</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    
    <script>
        function toggleDetails(orderId) {
            const details = document.getElementById(`details-${orderId}`);
            if (details.style.display === 'block') {
                details.style.display = 'none';
            } else {
                details.style.display = 'block';
            }
        }
    </script>
</body>
</html>
