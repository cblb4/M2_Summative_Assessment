<?php
// Include database connection
include 'db_connect.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get action type (add or update)
    $action = $_POST['action'] ?? 'add';
    
    // Begin transaction to ensure data integrity
    $conn->begin_transaction();
    
    // Check if customer exists
    if (!empty($_POST['customer_id']) && !empty($_POST['name'])) {
        $customer_id = $_POST['customer_id'];
        $customer_name = $_POST['name'];
        $address_id = $_POST['address_id'] ?? null;
        
        // If an address_id is provided, check if it exists and create it if needed
        if (!empty($address_id)) {
            $check_address = "SELECT address_id FROM address WHERE address_id = ?";
            $stmt = $conn->prepare($check_address);
            $stmt->bind_param("i", $address_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                // Address doesn't exist - create a placeholder address record
                $create_address = "INSERT INTO address (address_id, street) VALUES (?, 'Address pending')";
                $stmt = $conn->prepare($create_address);
                $stmt->bind_param("i", $address_id);
                $stmt->execute();
            }
        }
        
        // Check if customer exists
        $check_customer = "SELECT customer_id FROM customer WHERE customer_id = ?";
        $stmt = $conn->prepare($check_customer);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            // Get the structure of the customer table to find out column names
            $table_structure = "DESCRIBE customer";
            $structure_result = $conn->query($table_structure);
            $columns = [];
            
            while ($column = $structure_result->fetch_assoc()) {
                $columns[] = $column['Field'];
            }
            
            // Determine which column might be used for customer name
            $name_column = "";
            $possible_name_columns = ["name", "customer_name", "cust_name", "fullname"];
            
            foreach ($possible_name_columns as $col) {
                if (in_array($col, $columns)) {
                    $name_column = $col;
                    break;
                }
            }
            
            // If we found a name column, create the customer
            if (!empty($name_column)) {
                $create_customer = "INSERT INTO customer (customer_id, $name_column) VALUES (?, ?)";
                $stmt = $conn->prepare($create_customer);
                $stmt->bind_param("is", $customer_id, $customer_name);
                $stmt->execute();
            } else {
                // If no name column was found, just insert the ID
                $create_customer = "INSERT INTO customer (customer_id) VALUES (?)";
                $stmt = $conn->prepare($create_customer);
                $stmt->bind_param("i", $customer_id);
                $stmt->execute();
            }
            
            // Check which fields exist in the customer table for potential updates
            $table_structure = "DESCRIBE customer";
            $structure_result = $conn->query($table_structure);
            $customer_columns = [];
            
            while ($column = $structure_result->fetch_assoc()) {
                $customer_columns[] = $column['Field'];
            }
            
            // Map form fields to potential database columns
            $field_mappings = [
                'address_id' => ['address_id', 'addr_id'],
                'customer_number' => ['customer_number', 'cust_number', 'phone', 'contact_number']
            ];
            
            $update_needed = false;
            $update_customer = "UPDATE customer SET ";
            $params = [];
            $types = "";
            
            // For each form field, check if corresponding column exists in the database
            foreach ($field_mappings as $form_field => $possible_columns) {
                if (!empty($_POST[$form_field])) {
                    foreach ($possible_columns as $col) {
                        if (in_array($col, $customer_columns)) {
                            $update_customer .= "$col = ?, ";
                            $params[] = $_POST[$form_field];
                            $types .= (is_numeric($_POST[$form_field]) && !strpos($_POST[$form_field], '.')) ? "i" : "s";
                            $update_needed = true;
                            break;
                        }
                    }
                }
            }
            
            // Only proceed if updates are needed
            if ($update_needed) {
                // Remove trailing comma and space
                $update_customer = rtrim($update_customer, ", ");
                
                // Add WHERE clause
                $update_customer .= " WHERE customer_id = ?";
                $params[] = $customer_id;
                $types .= "i";
                
                // Execute update
                $stmt = $conn->prepare($update_customer);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
            }
        }
    }
    
    try {
        if ($action == 'add') {
            // Handle adding a new order
            
            // Insert main order record
            $order_sql = "INSERT INTO `order` (order_id, date, customer_id, employee_id, quantity, price) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($order_sql);
            
            // Use provided order_id or let database auto-increment
            $order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : null;
            $date = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');
            $customer_id = $_POST['order_customer_id'];
            $employee_id = $_POST['employee_id'];
            $quantity = $_POST['quantity'];
            $price = $_POST['price'];
            
            // Bind parameters 
            $stmt->bind_param("issidi", $order_id, $date, $customer_id, $employee_id, $quantity, $price);
            $stmt->execute();
            
            // If order_id was not provided, get the auto-generated ID
            if (empty($order_id)) {
                $order_id = $conn->insert_id;
            }
            
            // Handle delivery details if provided
            if (!empty($_POST['delivery_id'])) {
                $delivery_sql = "INSERT INTO delivery (delivery_id, employee_id, order_id, date, departure, arrival, fee) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($delivery_sql);
                
                $delivery_id = $_POST['delivery_id'];
                $delivery_employee_id = $_POST['delivery_employee_id'];
                $delivery_date = !empty($_POST['delivery_date']) ? $_POST['delivery_date'] : date('Y-m-d');
                $departure = $_POST['departure'];
                $arrival = $_POST['arrival'];
                $fee = $_POST['fee'];
                
                $stmt->bind_param("iiisssd", $delivery_id, $delivery_employee_id, $order_id, $delivery_date, $departure, $arrival, $fee);
                $stmt->execute();
            }
            
            // Handle packaging details if provided
            if (!empty($_POST['packaging_id'])) {
                $packaging_sql = "INSERT INTO packaging (packaging_id, order_id, quantity, type, size, price) 
                                 VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($packaging_sql);
                
                $packaging_id = $_POST['packaging_id'];
                $packaging_quantity = $_POST['packaging_quantity'];
                $type = $_POST['type'];
                $size = $_POST['size'];
                $packaging_price = $_POST['packaging_price'];
                
                $stmt->bind_param("iiissd", $packaging_id, $order_id, $packaging_quantity, $type, $size, $packaging_price);
                $stmt->execute();
            }
            
            // Commit the transaction
            $conn->commit();
            
            // Redirect to order details page
            header("Location: order-details.php");
            exit;
            
        } else if ($action == 'update') {
            // Handle updating an existing order
            
            // Update main order record
            $order_sql = "UPDATE `order` 
                          SET date = ?, customer_id = ?, employee_id = ?, quantity = ?, price = ? 
                          WHERE order_id = ?";
            $stmt = $conn->prepare($order_sql);
            
            $order_id = $_POST['order_id'];
            $date = $_POST['date'];
            $customer_id = $_POST['customer_id'];
            $employee_id = $_POST['employee_id'];
            $quantity = $_POST['quantity'];
            $price = $_POST['price'];
            
            $stmt->bind_param("ssiidi", $date, $customer_id, $employee_id, $quantity, $price, $order_id);
            $stmt->execute();
            
            // Update delivery details if provided
            if (!empty($_POST['delivery_id'])) {
                // Check if delivery record exists
                $check_sql = "SELECT delivery_id FROM delivery WHERE delivery_id = ?";
                $stmt = $conn->prepare($check_sql);
                $stmt->bind_param("i", $_POST['delivery_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update existing delivery record
                    $delivery_sql = "UPDATE delivery 
                                   SET employee_id = ?, order_id = ?, date = ?, departure = ?, arrival = ?, fee = ? 
                                   WHERE delivery_id = ?";
                    $stmt = $conn->prepare($delivery_sql);
                    
                    $delivery_id = $_POST['delivery_id'];
                    $delivery_employee_id = $_POST['delivery_employee_id'];
                    $delivery_date = $_POST['delivery_date'];
                    $departure = $_POST['departure'];
                    $arrival = $_POST['arrival'];
                    $fee = $_POST['fee'];
                    
                    $stmt->bind_param("iisssdi", $delivery_employee_id, $order_id, $delivery_date, $departure, $arrival, $fee, $delivery_id);
                    $stmt->execute();
                }
            }
            
            // Update packaging details if provided
            if (!empty($_POST['packaging_id'])) {
                // Check if packaging record exists
                $check_sql = "SELECT packaging_id FROM packaging WHERE packaging_id = ?";
                $stmt = $conn->prepare($check_sql);
                $stmt->bind_param("i", $_POST['packaging_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update existing packaging record
                    $packaging_sql = "UPDATE packaging 
                                    SET order_id = ?, quantity = ?, type = ?, size = ?, price = ? 
                                    WHERE packaging_id = ?";
                    $stmt = $conn->prepare($packaging_sql);
                    
                    $packaging_id = $_POST['packaging_id'];
                    $packaging_quantity = $_POST['packaging_quantity'];
                    $type = $_POST['type'];
                    $size = $_POST['size'];
                    $packaging_price = $_POST['packaging_price'];
                    
                    $stmt->bind_param("iissdi", $order_id, $packaging_quantity, $type, $size, $packaging_price, $packaging_id);
                    $stmt->execute();
                }
            }
            
            // Commit the transaction
            $conn->commit();
            
            // Redirect to order details page
            header("Location: order-details.php");
            exit;
        }
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $conn->rollback();
        
        // Display error message
        echo "Error: " . $e->getMessage();
    }
}

// If we get here, redirect back to the order form
header("Location: order.php");
exit;
