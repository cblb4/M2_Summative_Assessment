<?php
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
                    <input type="text" name="customer_id" placeholder="Enter Customer ID">
                    <label>Name:</label>
                    <input type="text" name="name" placeholder="Enter Name">
                    <label>Address ID:</label>
                    <input type="text" name="address_id" placeholder="Enter Address ID">
                    <label>Customer Number:</label>
                    <input type="text" name="customer_number" placeholder="Enter Customer Number">
                </div>
                
                <!-- Order, Delivery, and Packaging Details -->
                <div class="order-container">
                    <div class="order-section">
                        <h3>Order Details</h3>
                        <label>Order ID:</label>
                        <input type="text" name="order_id" placeholder="Enter Order ID">
                        <label>Date:</label>
                        <input type="date" name="date" placeholder="Enter Date">
                        <label>Customer ID:</label>
                        <input type="text" name="order_customer_id" placeholder="Enter Customer ID">
                        <label>Employee ID:</label>
                        <input type="text" name="employee_id" placeholder="Enter Employee ID">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" placeholder="Enter Quantity">
                        <label>Price:</label>
                        <input type="text" name="price" placeholder="Enter Price">
                    </div>
                    <div class="order-section">
                        <h3>Delivery Details</h3>
                        <label>Delivery ID:</label>
                        <input type="text" name="delivery_id" placeholder="Enter Delivery ID">
                        <label>Employee ID:</label>
                        <input type="text" name="delivery_employee_id" placeholder="Enter Employee ID">
                        <label>Order ID:</label>
                        <input type="text" name="delivery_order_id" placeholder="Enter Order ID">
                        <label>Date:</label>
                        <input type="date" name="delivery_date" placeholder="Enter Date">
                        <label>Departure:</label>
                        <input type="text" name="departure" placeholder="Enter Departure">
                        <label>Arrival:</label>
                        <input type="text" name="arrival" placeholder="Enter Arrival">
                        <label>Fee:</label>
                        <input type="text" name="fee" placeholder="Enter Fee">
                    </div>
                    <div class="order-section">
                        <h3>Packaging Details</h3>
                        <label>Packaging ID:</label>
                        <input type="text" name="packaging_id" placeholder="Enter Packaging ID">
                        <label>Order ID:</label>
                        <input type="text" name="packaging_order_id" placeholder="Enter Order ID">
                        <label>Quantity:</label>
                        <input type="number" name="packaging_quantity" placeholder="Enter Quantity">
                        <label>Type:</label>
                        <input type="text" name="type" placeholder="Enter Type">
                        <label>Size:</label>
                        <input type="text" name="size" placeholder="Enter Size">
                        <label>Price:</label>
                        <input type="text" name="packaging_price" placeholder="Enter Price">
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