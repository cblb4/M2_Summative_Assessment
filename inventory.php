<?php
// Include database connection
include 'db_connect.php';

// Initialize variables
$supply_id = $date = $employee_id = $ingredients_id = $quantity = $price = $expiration_date = "";
$supplier_id = $supplier_name = $contact = $address_id = "";
$ingredient_id = $ingredient_name = $unit = $current = $critical_level = "";
$message = $error = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determine which action was performed
    if (isset($_POST['add'])) {
        // Handle Add operation
        if (isset($_POST['action_type']) && $_POST['action_type'] == 'supply') {
            // Add supply record
            $supply_id = $_POST['supply_id'];
            $date = $_POST['date'];
            $employee_id = $_POST['employee_id'];
            $ingredients_id = $_POST['ingredients_id'];
            $quantity = $_POST['quantity'];
            $price = $_POST['price'];
            $expiration_date = $_POST['expiration_date'];
            
            // STEP 1: First check if the supply record already exists in the supply table
            $stmt = $conn->prepare("SELECT * FROM supply WHERE supply_id = ?");
            $stmt->bind_param("s", $supply_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // If supply record doesn't exist, first create it in the supply table
            if ($result->num_rows == 0) {
                // Get supplier_id if not provided
                if (empty($_POST['supplier_id'])) {
                    $supplier_id = 45; // Using a default supplier_id if not provided
                } else {
                    $supplier_id = $_POST['supplier_id'];
                }
                
                // Insert into supply table first
                $stmt = $conn->prepare("INSERT INTO supply (supply_id, date, employee_id, supplier_id) 
                        VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $supply_id, $date, $employee_id, $supplier_id);
                
                if (!$stmt->execute()) {
                    $error = "Error adding supply record: " . $stmt->error;
                    $stmt->close();
                    // Don't proceed to the next step if this fails
                } else {
                    $stmt->close();
                    
                    // STEP 2: Now insert into supply_details
                    $stmt = $conn->prepare("INSERT INTO supply_details (supply_id, ingredients_id, quantity, price, expiration_date) 
                            VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssdss", $supply_id, $ingredients_id, $quantity, $price, $expiration_date);
                    
                    if ($stmt->execute()) {
                        $message = "Supply record added successfully";
                    } else {
                        $error = "Error adding supply details: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                // If supply already exists, just add the details
                $stmt->close();
                
                $stmt = $conn->prepare("INSERT INTO supply_details (supply_id, ingredients_id, quantity, price, expiration_date) 
                        VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdss", $supply_id, $ingredients_id, $quantity, $price, $expiration_date);
                
                if ($stmt->execute()) {
                    $message = "Supply details added successfully";
                } else {
                    $error = "Error adding supply details: " . $stmt->error;
                }
                $stmt->close();
            }
        } 
        else if (isset($_POST['action_type']) && $_POST['action_type'] == 'supplier') {
            // Add supplier record
            $supplier_id = $_POST['supplier_id'];
            $supplier_name = $_POST['name'];
            $contact = $_POST['contact'];
            $address_id = $_POST['address_id'];
            
            // Using prepared statement
            $stmt = $conn->prepare("INSERT INTO supplier (supplier_id, name, contact, address_id) 
                    VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $supplier_id, $supplier_name, $contact, $address_id);
            
            if ($stmt->execute()) {
                $message = "Supplier record added successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        else if (isset($_POST['action_type']) && $_POST['action_type'] == 'ingredients') {
            // Add ingredients record
            $ingredient_id = $_POST['ingredients_id'];
            $ingredient_name = $_POST['name'];
            $unit = $_POST['unit'];
            $current = $_POST['current'];
            $critical_level = $_POST['critical_level'];
            
            // Using prepared statement
            $stmt = $conn->prepare("INSERT INTO ingredients (ingredients_id, name, unit, current, critical_level) 
                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $ingredient_id, $ingredient_name, $unit, $current, $critical_level);
            
            if ($stmt->execute()) {
                $message = "Ingredient record added successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } 
    else if (isset($_POST['delete'])) {
        // Handle Delete operation
        if (isset($_POST['action_type']) && $_POST['action_type'] == 'supply' && !empty($_POST['supply_id'])) {
            $supply_id = $_POST['supply_id'];
            
            // First delete from supply_details (child table)
            $stmt = $conn->prepare("DELETE FROM supply_details WHERE supply_id = ?");
            $stmt->bind_param("s", $supply_id);
            $stmt->execute();
            $stmt->close();
            
            // Then delete from supply (parent table)
            $stmt = $conn->prepare("DELETE FROM supply WHERE supply_id = ?");
            $stmt->bind_param("s", $supply_id);
            
            if ($stmt->execute()) {
                $message = "Supply record deleted successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        else if (isset($_POST['action_type']) && $_POST['action_type'] == 'supplier' && !empty($_POST['supplier_id'])) {
            $supplier_id = $_POST['supplier_id'];
            
            // Using prepared statement
            $stmt = $conn->prepare("DELETE FROM supplier WHERE supplier_id = ?");
            $stmt->bind_param("s", $supplier_id);
            
            if ($stmt->execute()) {
                $message = "Supplier record deleted successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        else if (isset($_POST['action_type']) && $_POST['action_type'] == 'ingredients' && !empty($_POST['ingredients_id'])) {
            $ingredient_id = $_POST['ingredients_id'];
            
            // Using prepared statement
            $stmt = $conn->prepare("DELETE FROM ingredients WHERE ingredients_id = ?");
            $stmt->bind_param("s", $ingredient_id);
            
            if ($stmt->execute()) {
                $message = "Ingredient record deleted successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    else if (isset($_POST['update'])) {
        // Handle Update operation
        if (isset($_POST['action_type']) && $_POST['action_type'] == 'supply' && !empty($_POST['supply_id'])) {
            $supply_id = $_POST['supply_id'];
            $ingredients_id = $_POST['ingredients_id'];
            $quantity = $_POST['quantity'];
            $price = $_POST['price'];
            $expiration_date = $_POST['expiration_date'];
            
            // Using prepared statement
            $stmt = $conn->prepare("UPDATE supply_details SET ingredients_id=?, quantity=?, price=?, expiration_date=? 
                    WHERE supply_id=?");
            $stmt->bind_param("sdsss", $ingredients_id, $quantity, $price, $expiration_date, $supply_id);
            
            if ($stmt->execute()) {
                $message = "Supply record updated successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        else if (isset($_POST['action_type']) && $_POST['action_type'] == 'supplier' && !empty($_POST['supplier_id'])) {
            $supplier_id = $_POST['supplier_id'];
            $supplier_name = $_POST['name'];
            $contact = $_POST['contact'];
            $address_id = $_POST['address_id'];
            
            // Using prepared statement
            $stmt = $conn->prepare("UPDATE supplier SET name=?, contact=?, address_id=? 
                    WHERE supplier_id=?");
            $stmt->bind_param("ssss", $supplier_name, $contact, $address_id, $supplier_id);
            
            if ($stmt->execute()) {
                $message = "Supplier record updated successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        else if (isset($_POST['action_type']) && $_POST['action_type'] == 'ingredients' && !empty($_POST['ingredients_id'])) {
            $ingredient_id = $_POST['ingredients_id'];
            $ingredient_name = $_POST['name'];
            $unit = $_POST['unit'];
            $current = $_POST['current'];
            $critical_level = $_POST['critical_level'];
            
            // Using prepared statement
            $stmt = $conn->prepare("UPDATE ingredients SET name=?, unit=?, current=?, critical_level=? 
                    WHERE ingredients_id=?");
            $stmt->bind_param("sssss", $ingredient_name, $unit, $current, $critical_level, $ingredient_id);
            
            if ($stmt->execute()) {
                $message = "Ingredient record updated successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch data for search
$search_results = array();
if (isset($_POST['search_term']) && !empty($_POST['search_term'])) {
    $search_term = $_POST['search_term'];
    $search_term = "%$search_term%"; // Add wildcards for LIKE query
    
    // Search in supplies using prepared statement
    $stmt = $conn->prepare("SELECT * FROM supply_details WHERE 
            supply_id LIKE ? OR ingredients_id LIKE ?");
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['type'] = 'supply';
            $search_results[] = $row;
        }
    }
    $stmt->close();
    
    // Search in suppliers using prepared statement
    $stmt = $conn->prepare("SELECT * FROM supplier WHERE 
            supplier_id LIKE ? OR name LIKE ? OR contact LIKE ?");
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['type'] = 'supplier';
            $search_results[] = $row;
        }
    }
    $stmt->close();
    
    // Search in ingredients using prepared statement
    $stmt = $conn->prepare("SELECT * FROM ingredients WHERE 
            ingredients_id LIKE ? OR name LIKE ? OR unit LIKE ?");
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['type'] = 'ingredient';
            $search_results[] = $row;
        }
    }
    $stmt->close();
}

// Fetch all inventory records for the table
$inventory_items = array();
try {
    $stmt = $conn->prepare("SELECT sd.*, s.date, s.employee_id, s.supplier_id, i.name AS ingredient_name 
                            FROM supply_details sd 
                            LEFT JOIN supply s ON sd.supply_id = s.supply_id
                            LEFT JOIN ingredients i ON sd.ingredients_id = i.ingredients_id
                            ORDER BY s.date DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $inventory_items[] = $row;
        }
    }
    $stmt->close();
} catch (Exception $e) {
    $error = "Error fetching inventory data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Dulcis Maison</title>
    <link rel="stylesheet" href="inventory.css">
    <script type="module" src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo-container">
                <img src="images/Logo.png" alt="Dulcis Maison Logo" class="logo-img">
                <div class="logo-text">Dulcis Maison</div>
            </div>
            <ul class="nav-menu">
                <li><a href="/M2_Summative_Assessment/index.php"><ion-icon name="home-outline"></ion-icon> Overview</a></li>
                <li><a href="staff.php"><ion-icon name="people-outline"></ion-icon> Staff</a></li>
                <li><a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a></li>
                <li><a href="order.php"><ion-icon name="cart-outline"></ion-icon> Order</a></li>
                <li class="active"><a href="inventory.php"><ion-icon name="cube-outline"></ion-icon> Inventory</a></li>
            </ul>
        </aside>
        <main class="content">
            <h2>Inventory</h2>
            <p>Details of all the supply</p>

            <?php
            // Display message if any
            if (isset($message)) {
                echo "<div class='message success'>$message</div>";
            }
            if (isset($error)) {
                echo "<div class='message error'>$error</div>";
            }
            ?>

            <div class="details-row">
                <!-- Supply Details -->
                <div class="details-section">
                    <h3>Supply Details:</h3>
                    <form id="supply-form" method="POST" action="">
                        <input type="hidden" name="action_type" value="supply">
                        <input type="text" name="supply_id" placeholder="Supply_ID">
                        <input type="text" name="date" placeholder="Date">
                        <input type="text" name="employee_id" placeholder="Employee_ID">
                        <input type="text" name="supplier_id" placeholder="Supplier_ID">
                        <input type="text" name="ingredients_id" placeholder="Ingredients_ID">
                        <input type="text" name="quantity" placeholder="Quantity">
                        <input type="text" name="price" placeholder="Price">
                        <input type="text" name="expiration_date" placeholder="Expiration Date">
                    </form>
                </div>

                <!-- Supplier Details -->
                <div class="details-section">
                    <h3>Supplier Details:</h3>
                    <form id="supplier-form" method="POST" action="">
                        <input type="hidden" name="action_type" value="supplier">
                        <input type="text" name="supplier_id" placeholder="Supplier_ID">
                        <input type="text" name="name" placeholder="Name">
                        <input type="text" name="contact" placeholder="Contact">
                        <input type="text" name="address_id" placeholder="Address_ID">
                    </form>
                </div>

                <!-- Ingredients Details -->
                <div class="details-section">
                    <h3>Ingredients Details:</h3>
                    <form id="ingredients-form" method="POST" action="">
                        <input type="hidden" name="action_type" value="ingredients">
                        <input type="text" name="ingredients_id" placeholder="Ingredients_ID">
                        <input type="text" name="name" placeholder="Name">
                        <input type="text" name="unit" placeholder="Unit">
                        <input type="text" name="current" placeholder="Current">
                        <input type="text" name="critical_level" placeholder="Critical Level">
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn" onclick="submitForm('add')">Add</button>
                <button class="btn" onclick="submitForm('delete')">Delete</button>
                <button class="btn" onclick="submitForm('update')">Update</button>
                <button class="btn" onclick="clearForms()">Clear</button>
            </div>

            <!-- Search -->
            <div class="search-container">
                <form id="search-form" method="POST" action="">
                    <input type="text" name="search_term" placeholder="Search" class="search-input">
                    <button type="submit" name="search" class="search-btn" style="display:none;">Search</button>
                </form>
                
                <!-- Inventory Table (Grey Area) -->
                <div class="search-results">
                    <?php if (!empty($inventory_items)): ?>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Supply ID</th>
                                <th>Date</th>
                                <th>Ingredient</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Expiration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory_items as $item): ?>
                                <tr>
                                    <td><?php echo $item['supply_id']; ?></td>
                                    <td><?php echo $item['date']; ?></td>
                                    <td><?php echo $item['ingredient_name']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo $item['price']; ?></td>
                                    <td><?php echo $item['expiration_date']; ?></td>
                                    <td>
                                        <button onclick="loadInventoryData('<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>')">Edit</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p style="text-align: center; padding: 20px;">No inventory items found.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Search Results -->
                <?php if (!empty($search_results)): ?>
                <div class="search-results" style="margin-top: 15px;">
                    <h3>Search Results</h3>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>ID</th>
                                <th>Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $result): ?>
                                <tr>
                                    <td><?php echo ucfirst($result['type']); ?></td>
                                    <td>
                                        <?php 
                                        if ($result['type'] == 'supply') echo $result['supply_id'];
                                        elseif ($result['type'] == 'supplier') echo $result['supplier_id'];
                                        elseif ($result['type'] == 'ingredient') echo $result['ingredients_id'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($result['type'] == 'supply') {
                                            echo "Quantity: " . $result['quantity'] . ", Price: " . $result['price'];
                                        } elseif ($result['type'] == 'supplier') {
                                            echo $result['name'] . ", Contact: " . $result['contact'];
                                        } elseif ($result['type'] == 'ingredient') {
                                            echo $result['name'] . ", Unit: " . $result['unit'];
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button onclick="loadData('<?php echo $result['type']; ?>', <?php echo json_encode($result); ?>)">Load</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    // JavaScript for form handling
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener for search input to submit on enter
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.search-btn').click();
            }
        });
    });

    // Submit the appropriate form based on which section is active
    function submitForm(action) {
        // Determine which section has focus/data
        const supplyId = document.querySelector('#supply-form input[name="supply_id"]').value;
        const supplierId = document.querySelector('#supplier-form input[name="supplier_id"]').value;
        const ingredientId = document.querySelector('#ingredients-form input[name="ingredients_id"]').value;
        
        let form;
        
        // Priority: If any ID field has a value, use that form
        if (supplyId) {
            form = document.getElementById('supply-form');
        } else if (supplierId) {
            form = document.getElementById('supplier-form');
        } else if (ingredientId) {
            form = document.getElementById('ingredients-form');
        } else {
            // If no ID fields have values, use the form that has focus
            const activeElement = document.activeElement;
            if (activeElement && activeElement.form) {
                form = activeElement.form;
            } else {
                // Default to supply form if no other indicators
                form = document.getElementById('supply-form');
            }
        }
        
        // Create and append the action button
        const actionButton = document.createElement('input');
        actionButton.type = 'hidden';
        actionButton.name = action;
        actionButton.value = '1';
        form.appendChild(actionButton);
        
        // Submit the form
        form.submit();
    }

    // Clear all forms
    function clearForms() {
        document.getElementById('supply-form').reset();
        document.getElementById('supplier-form').reset();
        document.getElementById('ingredients-form').reset();
    }

    // Load data from search results into form
    function loadData(type, data) {
        clearForms();
        
        if (type === 'supply') {
            const form = document.getElementById('supply-form');
            form.elements['supply_id'].value = data.supply_id;
            form.elements['ingredients_id'].value = data.ingredients_id;
            form.elements['quantity'].value = data.quantity;
            form.elements['price'].value = data.price;
            form.elements['expiration_date'].value = data.expiration_date;
        } 
        else if (type === 'supplier') {
            const form = document.getElementById('supplier-form');
            form.elements['supplier_id'].value = data.supplier_id;
            form.elements['name'].value = data.name;
            form.elements['contact'].value = data.contact;
            form.elements['address_id'].value = data.address_id;
        } 
        else if (type === 'ingredient') {
            const form = document.getElementById('ingredients-form');
            form.elements['ingredients_id'].value = data.ingredients_id;
            form.elements['name'].value = data.name;
            form.elements['unit'].value = data.unit;
            form.elements['current'].value = data.current;
            form.elements['critical_level'].value = data.critical_level;
        }
    }
    
    // Load data from inventory table into supply form
    function loadInventoryData(jsonString) {
        try {
            clearForms();
            const data = JSON.parse(jsonString);
            
            const form = document.getElementById('supply-form');
            form.elements['supply_id'].value = data.supply_id;
            form.elements['date'].value = data.date;
            form.elements['employee_id'].value = data.employee_id;
            form.elements['supplier_id'].value = data.supplier_id;
            form.elements['ingredients_id'].value = data.ingredients_id;
            form.elements['quantity'].value = data.quantity;
            form.elements['price'].value = data.price;
            form.elements['expiration_date'].value = data.expiration_date;
        } catch (e) {
            console.error("Error parsing JSON data:", e);
        }
    }
    </script>
</body>
</html>