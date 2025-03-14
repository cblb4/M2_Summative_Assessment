<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// process_menu.php - Handle form submission for menu
include 'db_connect.php';

// Process delete requests from GET (links in the table)
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $menu_id = $_GET['delete'];
    
    // Check if there are any related menu_details records
    $check_sql = "SELECT COUNT(*) as count FROM menu_details WHERE menu_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt) {
        $check_stmt->bind_param("s", $menu_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            // Delete related menu_details records first
            $del_details_sql = "DELETE FROM menu_details WHERE menu_id = ?";
            $del_details_stmt = $conn->prepare($del_details_sql);
            if ($del_details_stmt) {
                $del_details_stmt->bind_param("s", $menu_id);
                $del_details_stmt->execute();
                $del_details_stmt->close();
            }
        }
    }
    
    // Now delete the menu item
    $sql = "DELETE FROM menu WHERE menu_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed for menu delete: " . $conn->error);
    }
    
    $stmt->bind_param("s", $menu_id);
    if ($stmt->execute()) {
        header("Location: menu.php?success=Menu item deleted successfully");
        exit();
    } else {
        header("Location: menu.php?error=Failed to delete menu item: " . $stmt->error);
        exit();
    }
}

// Process POST requests from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get menu data
    $menu_id = $_POST['menu_id'] ?? '';
    $menu_name = $_POST['menu_name'] ?? '';
    $category = $_POST['category'] ?? ''; // Added category field
    $date = $_POST['date'] ?? date('Y-m-d');
    $serving = $_POST['serving'] ?? 0;
    $price = $_POST['price'] ?? 0;
    $action = $_POST['action'] ?? '';
    
    // Debug output - remove in production
    echo "Action: $action<br>";
    echo "Menu ID: $menu_id<br>";
    echo "Menu Name: $menu_name<br>";
    echo "Category: $category<br>"; // Added category debug output
    echo "Date: $date<br>";
    echo "Serving: $serving<br>";
    echo "Price: $price<br>";
    
    // Check for the menu table structure
    $tableResult = $conn->query("SHOW TABLES LIKE 'menu'");
    if ($tableResult->num_rows == 0) {
        die("The 'menu' table does not exist in the database.");
    }
    
    // Get menu table structure
    $columnsResult = $conn->query("DESCRIBE `menu`");
    echo "<h3>Menu Table Structure:</h3>";
    echo "<pre>";
    $columns = [];
    while ($row = $columnsResult->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
        $columns[] = $row['Field'];
    }
    echo "</pre>";
    
    // Check which action was requested
    if ($action == 'add') {
        // Insert new menu item
        $sql = "INSERT INTO menu (menu_id, menu_name, category, date, serving, price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed for menu insert: " . $conn->error);
        }
        
        $stmt->bind_param("ssssid", $menu_id, $menu_name, $category, $date, $serving, $price);
        if ($stmt->execute()) {
            echo "Menu item added successfully. <a href='menu.php'>Return to Menu</a>";
            // header("Location: menu.php?success=Menu item added successfully");
            // exit();
        } else {
            echo "Failed to add menu item: " . $stmt->error . "<br>";
            echo "<a href='menu.php'>Return to Menu</a>";
            // header("Location: menu.php?error=Failed to add menu item: " . $stmt->error);
            // exit();
        }
    } 
    else if ($action == 'update') {
        // Update existing menu item
        $sql = "UPDATE menu SET menu_name = ?, category = ?, date = ?, serving = ?, price = ? WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed for menu update: " . $conn->error);
        }
        
        $stmt->bind_param("sssids", $menu_name, $category, $date, $serving, $price, $menu_id);
        if ($stmt->execute()) {
            echo "Menu item updated successfully. <a href='menu.php'>Return to Menu</a>";
            // header("Location: menu.php?success=Menu item updated successfully");
            // exit();
        } else {
            echo "Failed to update menu item: " . $stmt->error . "<br>";
            echo "<a href='menu.php'>Return to Menu</a>";
            // header("Location: menu.php?error=Failed to update menu item: " . $stmt->error);
            // exit();
        }
    } 
    else if ($action == 'delete') {
        // Delete menu item
        // First check if there are related menu_details records
        $check_sql = "SELECT COUNT(*) as count FROM menu_details WHERE menu_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        if ($check_stmt) {
            $check_stmt->bind_param("s", $menu_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                // Delete related menu_details records first
                $del_details_sql = "DELETE FROM menu_details WHERE menu_id = ?";
                $del_details_stmt = $conn->prepare($del_details_sql);
                if ($del_details_stmt) {
                    $del_details_stmt->bind_param("s", $menu_id);
                    $del_details_stmt->execute();
                    $del_details_stmt->close();
                }
            }
            $check_stmt->close();
        }
        
        // Now delete the menu item
        $sql = "DELETE FROM menu WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed for menu delete: " . $conn->error);
        }
        
        $stmt->bind_param("s", $menu_id);
        if ($stmt->execute()) {
            echo "Menu item deleted successfully. <a href='menu.php'>Return to Menu</a>";
            // header("Location: menu.php?success=Menu item deleted successfully");
            // exit();
        } else {
            echo "Failed to delete menu item: " . $stmt->error . "<br>";
            echo "<a href='menu.php'>Return to Menu</a>";
            // header("Location: menu.php?error=Failed to delete menu item: " . $stmt->error);
            // exit();
        }
    }
    else {
        echo "Invalid action: $action <br>";
        echo "<a href='menu.php'>Return to Menu</a>";
    }
}
else {
    echo "No POST data received. <a href='menu.php'>Return to Menu</a>";
}

// Close the connection
$conn->close();
?>