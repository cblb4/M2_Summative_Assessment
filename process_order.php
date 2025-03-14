<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// process_order.php - Handle form submission for orders
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the action (add or update)
    $action = $_POST['action'] ?? 'add';
    
    // Get customer data
    $customer_id = $_POST['customer_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $address_id = $_POST['address_id'] ?? '';
    $customer_number = $_POST['customer_number'] ?? '';
    
    // If no customer ID provided but name is, create a new customer
    if (empty($customer_id) && !empty($name)) {
        try {
            $stmt = $conn->prepare("INSERT INTO customer (name, address_id, customer_number) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed for customer insert: " . $conn->error);
            }
            
            $stmt->bind_param("sis", $name, $address_id, $customer_number);
            if ($stmt->execute()) {
                $customer_id = $conn->insert_id;
                echo "Customer created with ID: $customer_id<br>";
            } else {
                echo "Failed to create customer: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "Error in customer creation: " . $e->getMessage() . "<br>";
        }
    }
    
    // Get order data
    $order_id = $_POST['order_id'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $customer_id = !empty($_POST['order_customer_id']) ? $_POST['order_customer_id'] : $customer_id;
    $employee_id = $_POST['employee_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $price = $_POST['price'] ?? 0;
    
    // Get delivery data
    $delivery_id = $_POST['delivery_id'] ?? '';
    $delivery_employee_id = $_POST['delivery_employee_id'] ?? $employee_id;
    $delivery_order_id = $_POST['delivery_order_id'] ?? $order_id;
    $delivery_date = $_POST['delivery_date'] ?? $date;
    $departure = $_POST['departure'] ?? '';
    $arrival = $_POST['arrival'] ?? '';
    $fee = $_POST['fee'] ?? 0;
    
    // Get packaging data
    $packaging_id = $_POST['packaging_id'] ?? '';
    $packaging_order_id = $_POST['packaging_order_id'] ?? $order_id;
    $packaging_quantity = $_POST['packaging_quantity'] ?? $quantity;
    $type = $_POST['type'] ?? '';
    $size = $_POST['size'] ?? '';
    $packaging_price = $_POST['packaging_price'] ?? 0;
    
    if ($action == 'add') {
        echo "Attempting to insert order with: date=$date, customer_id=$customer_id, employee_id=$employee_id, quantity=$quantity, price=$price<br>";
        
        // Check if the order table exists and get its structure
        $tableResult = $conn->query("SHOW TABLES LIKE 'order'");
        if ($tableResult->num_rows == 0) {
            die("The 'order' table does not exist in the database.");
        }
        
        // Get the actual columns from the order table
        $columnsResult = $conn->query("DESCRIBE `order`");
        echo "<h3>Order Table Structure:</h3>";
        echo "<pre>";
        $columns = [];
        while ($row = $columnsResult->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
            $columns[] = $row['Field'];
        }
        echo "</pre>";
        
        // Check if the required columns exist
        $requiredColumns = ['date', 'customer_id', 'employee_id', 'quantity', 'price'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (!empty($missingColumns)) {
            echo "<h3>Missing columns in the order table:</h3>";
            foreach ($missingColumns as $column) {
                echo "- $column<br>";
            }
            die("Please fix your database structure or adjust the code to match your database structure.");
        }
        
        // Insert order
        try {
            $stmt = $conn->prepare("INSERT INTO `order` (date, customer_id, employee_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed for order insert: " . $conn->error);
            }
            
            $stmt->bind_param("siids", $date, $customer_id, $employee_id, $quantity, $price);
            
            if ($stmt->execute()) {
                $new_order_id = $conn->insert_id;
                echo "Order created successfully with ID: $new_order_id<br>";
                
                // Check if there's delivery information
                if (!empty($delivery_id)) {
                    $stmt = $conn->prepare("INSERT INTO delivery (delivery_id, employee_id, order_id, date, departure, arrival, fee) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        echo "Prepare failed for delivery insert: " . $conn->error . "<br>";
                    } else {
                        $stmt->bind_param("iiisssd", $delivery_id, $delivery_employee_id, $new_order_id, $delivery_date, $departure, $arrival, $fee);
                        if (!$stmt->execute()) {
                            echo "Failed to create delivery: " . $stmt->error . "<br>";
                        }
                    }
                }
                
                // Check if there's packaging information
                if (!empty($packaging_id)) {
                    $stmt = $conn->prepare("INSERT INTO packaging (packaging_id, order_id, quantity, type, size, price) VALUES (?, ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        echo "Prepare failed for packaging insert: " . $conn->error . "<br>";
                    } else {
                        $stmt->bind_param("iiissd", $packaging_id, $new_order_id, $packaging_quantity, $type, $size, $packaging_price);
                        if (!$stmt->execute()) {
                            echo "Failed to create packaging: " . $stmt->error . "<br>";
                        }
                    }
                }
                
                // Comment out the redirect temporarily to see debug info
                echo "<p>Processing complete. <a href='order-details.php'>View order details</a></p>";
                // header("Location: order-details.php");
                // exit();
            } else {
                echo "Failed to create order: " . $stmt->error . "<br>";
            }
            
            $stmt->close();
        } catch (Exception $e) {
            echo "Error in order creation: " . $e->getMessage() . "<br>";
        }
    } else if ($action == 'update') {
        // UPDATE operation
        echo "Attempting to update order with ID: $order_id<br>";
        
        try {
            // First, check if the order exists
            $check = $conn->prepare("SELECT * FROM `order` WHERE order_id = ?");
            if (!$check) {
                throw new Exception("Prepare failed for order check: " . $conn->error);
            }
            
            $check->bind_param("i", $order_id);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows == 0) {
                echo "Error: Order with ID $order_id does not exist.<br>";
            } else {
                // Update order
                $stmt = $conn->prepare("UPDATE `order` SET date = ?, customer_id = ?, employee_id = ?, quantity = ?, price = ? WHERE order_id = ?");
                if (!$stmt) {
                    throw new Exception("Prepare failed for order update: " . $conn->error);
                }
                
                $stmt->bind_param("siidsi", $date, $customer_id, $employee_id, $quantity, $price, $order_id);
                
                if ($stmt->execute()) {
                    echo "Order updated successfully.<br>";
                    
                    // Update or insert delivery information
                    if (!empty($delivery_id)) {
                        // Check if delivery exists
                        $check = $conn->prepare("SELECT * FROM delivery WHERE delivery_id = ?");
                        $check->bind_param("i", $delivery_id);
                        $check->execute();
                        $delivery_exists = $check->get_result()->num_rows > 0;
                        
                        if ($delivery_exists) {
                            // Update delivery
                            $stmt = $conn->prepare("UPDATE delivery SET employee_id = ?, order_id = ?, date = ?, departure = ?, arrival = ?, fee = ? WHERE delivery_id = ?");
                            $stmt->bind_param("iisssdi", $delivery_employee_id, $delivery_order_id, $delivery_date, $departure, $arrival, $fee, $delivery_id);
                        } else {
                            // Insert delivery
                            $stmt = $conn->prepare("INSERT INTO delivery (delivery_id, employee_id, order_id, date, departure, arrival, fee) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("iiisssd", $delivery_id, $delivery_employee_id, $delivery_order_id, $delivery_date, $departure, $arrival, $fee);
                        }
                        
                        if (!$stmt->execute()) {
                            echo "Failed to update/create delivery: " . $stmt->error . "<br>";
                        }
                    }
                    
                    // Update or insert packaging information
                    if (!empty($packaging_id)) {
                        // Check if packaging exists
                        $check = $conn->prepare("SELECT * FROM packaging WHERE packaging_id = ?");
                        $check->bind_param("i", $packaging_id);
                        $check->execute();
                        $packaging_exists = $check->get_result()->num_rows > 0;
                        
                        if ($packaging_exists) {
                            // Update packaging
                            $stmt = $conn->prepare("UPDATE packaging SET order_id = ?, quantity = ?, type = ?, size = ?, price = ? WHERE packaging_id = ?");
                            $stmt->bind_param("iissdi", $packaging_order_id, $packaging_quantity, $type, $size, $packaging_price, $packaging_id);
                        } else {
                            // Insert packaging
                            $stmt = $conn->prepare("INSERT INTO packaging (packaging_id, order_id, quantity, type, size, price) VALUES (?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("iiissd", $packaging_id, $packaging_order_id, $packaging_quantity, $type, $size, $packaging_price);
                        }
                        
                        if (!$stmt->execute()) {
                            echo "Failed to update/create packaging: " . $stmt->error . "<br>";
                        }
                    }
                    
                    // Comment out the redirect temporarily to see debug info
                    echo "<p>Update complete. <a href='order-details.php'>View order details</a></p>";
                    // header("Location: order-details.php");
                    // exit();
                } else {
                    echo "Failed to update order: " . $stmt->error . "<br>";
                }
                
                $stmt->close();
            }
        } catch (Exception $e) {
            echo "Error in order update: " . $e->getMessage() . "<br>";
        }
    }
}

$conn->close();
?>