<?php

session_start();
// Include database connection
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order - Dulcis Maison</title>
    <link rel="stylesheet" href="order.css">
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
                <li><a href="index.html"><ion-icon name="home-outline"></ion-icon> Overview</a></li>
                <li><a href="staff.php"><ion-icon name="people-outline"></ion-icon> Staff</a></li>
                <li><a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a></li>
                <li class="active"><a href="order.php"><ion-icon name="cart-outline"></ion-icon> Order</a></li>
                <li><a href="/M2_Summative_Assessment/inventory.php"><ion-icon name="cube-outline"></ion-icon> Inventory</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="content">
            <h2>Order</h2>
            <p>Details of all orders</p>
            
            <form action="process_order.php" method="post" id="orderForm">
                <!-- Hidden input for action type (add/update) -->
                <input type="hidden" name="action" id="actionType" value="add">
                
                <!-- Customer Details -->
                <div class="customer-section">
                    <h3>Customer Details</h3>
                    <label>Customer ID:</label>
                    <input type="text" name="customer_id" placeholder="Enter Customer ID" value="<?php echo isset($_POST['customer_id']) ? $_POST['customer_id'] : ''; ?>">
                    <label>Name:</label>
                    <input type="text" name="name" placeholder="Enter Name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
                    <label>Address ID:</label>
                    <input type="text" name="address_id" placeholder="Enter Address ID" value="<?php echo isset($_POST['address_id']) ? $_POST['address_id'] : ''; ?>">
                    <label>Customer Number:</label>
                    <input type="text" name="customer_number" placeholder="Enter Customer Number" value="<?php echo isset($_POST['customer_number']) ? $_POST['customer_number'] : ''; ?>">
                </div>
                
                <!-- Order, Delivery, and Packaging Details -->
                <div class="order-container">
                    <div class="order-section">
                        <h3>Order Details</h3>
                        <label>Order ID:</label>
                        <input type="text" name="order_id" placeholder="Enter Order ID" value="<?php echo isset($_POST['order_id']) ? $_POST['order_id'] : ''; ?>">
                        <label>Date:</label>
                        <input type="date" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : ''; ?>">
                        <label>Customer ID:</label>
                        <input type="text" name="order_customer_id" placeholder="Enter Customer ID" value="<?php echo isset($_POST['order_customer_id']) ? $_POST['order_customer_id'] : ''; ?>">
                        <label>Employee ID:</label>
                        <input type="text" name="employee_id" placeholder="Enter Employee ID" value="<?php echo isset($_POST['employee_id']) ? $_POST['employee_id'] : ''; ?>">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" placeholder="Enter Quantity" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : ''; ?>">
                        <label>Price:</label>
                        <input type="text" name="price" placeholder="Enter Price" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>">
                    </div>
                    <div class="order-section">
                        <h3>Delivery Details</h3>
                        <label>Delivery ID:</label>
                        <input type="text" name="delivery_id" placeholder="Enter Delivery ID" value="<?php echo isset($_POST['delivery_id']) ? $_POST['delivery_id'] : ''; ?>">
                        <label>Employee ID:</label>
                        <input type="text" name="delivery_employee_id" placeholder="Enter Employee ID" value="<?php echo isset($_POST['delivery_employee_id']) ? $_POST['delivery_employee_id'] : ''; ?>">
                        <label>Order ID:</label>
                        <input type="text" name="delivery_order_id" placeholder="Enter Order ID" value="<?php echo isset($_POST['delivery_order_id']) ? $_POST['delivery_order_id'] : ''; ?>">
                        <label>Date:</label>
                        <input type="date" name="delivery_date" value="<?php echo isset($_POST['delivery_date']) ? $_POST['delivery_date'] : ''; ?>">
                        <label>Departure:</label>
                        <input type="date" name="departure" value="<?php echo isset($_POST['departure']) ? $_POST['departure'] : ''; ?>">
                        <label>Arrival:</label>
                        <input type="date" name="arrival" value="<?php echo isset($_POST['arrival']) ? $_POST['arrival'] : ''; ?>">
                        <label>Fee:</label>
                        <input type="text" name="fee" placeholder="Enter Fee" value="<?php echo isset($_POST['fee']) ? $_POST['fee'] : ''; ?>">
                    </div>
                    <div class="order-section">
                        <h3>Packaging Details</h3>
                        <label>Packaging ID:</label>
                        <input type="text" name="packaging_id" placeholder="Enter Packaging ID" value="<?php echo isset($_POST['packaging_id']) ? $_POST['packaging_id'] : ''; ?>">
                        <label>Order ID:</label>
                        <input type="text" name="packaging_order_id" placeholder="Enter Order ID" value="<?php echo isset($_POST['packaging_order_id']) ? $_POST['packaging_order_id'] : ''; ?>">
                        <label>Quantity:</label>
                        <input type="number" name="packaging_quantity" placeholder="Enter Quantity" value="<?php echo isset($_POST['packaging_quantity']) ? $_POST['packaging_quantity'] : ''; ?>">
                        <label>Type:</label>
                        <input type="text" name="type" placeholder="Enter Type" value="<?php echo isset($_POST['type']) ? $_POST['type'] : ''; ?>">
                        <label>Size:</label>
                        <input type="text" name="size" placeholder="Enter Size" value="<?php echo isset($_POST['size']) ? $_POST['size'] : ''; ?>">
                        <label>Price:</label>
                        <input type="text" name="packaging_price" placeholder="Enter Price" value="<?php echo isset($_POST['packaging_price']) ? $_POST['packaging_price'] : ''; ?>">
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="buttons">
                    <button type="submit" class="btn" onclick="setAction('add')">Add</button>
                    <button type="reset" class="btn delete">Delete</button>
                    <button type="submit" class="btn update" onclick="setAction('update')">Update</button>
                    <button type="button" class="btn clear" onclick="clearForm()">Clear</button>
                    <a href="order-details.php" class="btn view-orders">View Orders</a>
                </div>
            </form>
        </main>
    </div>
    
    <script>
    function setAction(action) {
        // Set the action type when a button is clicked
        document.getElementById('actionType').value = action;
    }
    
    function clearForm() {
        document.querySelector('form').reset();
    }
    </script>
</body>
</html>
