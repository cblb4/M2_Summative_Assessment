<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connect.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        // Add new employee
        if ($_POST['action'] == 'add') {
            $name = $_POST['name'] ?? '';
            $age = $_POST['age'] ?? 0;
            $civil_status = $_POST['civil_status'] ?? '';
            $address_id = $_POST['address_id'] ?? '';
            
            $stmt = $conn->prepare("INSERT INTO employee (employee_name, employee_age, civil_status, address_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sisi", $name, $age, $civil_status, $address_id);
            
            if ($stmt->execute()) {
                $message = "Employee added successfully";
            } else {
                $error = "Error adding employee: " . $stmt->error;
            }
            $stmt->close();
        }
        
        // Delete employee
        if ($_POST['action'] == 'delete' && isset($_POST['employee_id'])) {
            $employee_id = $_POST['employee_id'];
            
            $stmt = $conn->prepare("DELETE FROM employee WHERE employee_id = ?");
            $stmt->bind_param("i", $employee_id);
            
            if ($stmt->execute()) {
                $message = "Employee deleted successfully";
            } else {
                $error = "Error deleting employee: " . $stmt->error;
            }
            $stmt->close();
        }
        
        // Update employee
        if ($_POST['action'] == 'update' && isset($_POST['employee_id'])) {
            $employee_id = $_POST['employee_id'];
            $name = $_POST['name'] ?? '';
            $age = $_POST['age'] ?? 0;
            $civil_status = $_POST['civil_status'] ?? '';
            $address_id = $_POST['address_id'] ?? '';
            
            $stmt = $conn->prepare("UPDATE employee SET employee_name=?, employee_age=?, civil_status=?, address_id=? WHERE employee_id=?");
            $stmt->bind_param("sisii", $name, $age, $civil_status, $address_id, $employee_id);
            
            if ($stmt->execute()) {
                $message = "Employee updated successfully";
            } else {
                $error = "Error updating employee: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Get all employees
$employee_result = $conn->query("SELECT * FROM employee");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - Dulcis Maison</title>
    <link rel="stylesheet" href="staff.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@latest/dist/ionicons/ionicons.js"></script>
    <style>
        .staff-table-content {
            width: 100%;
            border-collapse: collapse;
        }
        
        .staff-table-content th, .staff-table-content td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        
        .staff-table-content th {
            background-color: #5b6d3d;
            color: white;
        }
        
        .staff-table-content tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
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
                <li class="active"><a href="staff.php"><ion-icon name="people-outline"></ion-icon> Staff</a></li>
                <li><a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a></li>
                <li><a href="order.php"><ion-icon name="cart-outline"></ion-icon> Order</a></li>
                <li><a href="inventory.php"><ion-icon name="cube-outline"></ion-icon> Inventory</a></li>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="content">
            <h2>Staff</h2>
            <p>Details of all staff</p>
            
            <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="staff-container">
                <!-- Staff Form -->
                <div class="staff-form">
                    <form method="POST" action="staff.php">
                        <input type="hidden" name="action" value="add" id="form-action">
                        <input type="hidden" name="employee_id" id="employee-id">
                        
                        <label>Name:</label>
                        <input type="text" name="name" id="staff-name" placeholder="Enter Name" required>
                        
                        <label>Age:</label>
                        <input type="number" name="age" id="staff-age" placeholder="Enter Age" required>
                        
                        <label>Civil Status:</label>
                        <input type="text" name="civil_status" id="staff-civil-status" placeholder="Enter Civil Status" required>
                        
                        <label>Address ID:</label>
                        <input type="text" name="address_id" id="staff-address-id" placeholder="Enter Address ID" required>
                        
                        <!-- Action Buttons -->
                        <div class="buttons">
                            <button type="submit" class="btn" id="add-btn">Add</button>
                            <button type="button" class="btn delete" id="delete-btn">Delete</button>
                            <button type="button" class="btn update" id="update-btn">Update</button>
                            <button type="reset" class="btn clear">Clear</button>
                        </div>
                    </form>
                </div>
                <!-- Staff Table / Data -->
                <div class="staff-table">
                    <?php if ($employee_result && $employee_result->num_rows > 0): ?>
                        <table class="staff-table-content">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Civil Status</th>
                                    <th>Address ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $employee_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['employee_id'] ?? ''; ?></td>
                                        <td><?php echo $row['employee_name'] ?? ''; ?></td>
                                        <td><?php echo $row['employee_age'] ?? ''; ?></td>
                                        <td><?php echo $row['civil_status'] ?? ''; ?></td>
                                        <td><?php echo $row['address_id'] ?? ''; ?></td>
                                        <td>
                                            <button class="edit-btn" 
                                                data-id="<?php echo $row['employee_id'] ?? ''; ?>"
                                                data-name="<?php echo $row['employee_name'] ?? ''; ?>"
                                                data-age="<?php echo $row['employee_age'] ?? ''; ?>"
                                                data-civil="<?php echo $row['civil_status'] ?? ''; ?>"
                                                data-address="<?php echo $row['address_id'] ?? ''; ?>">Edit</button>
                                            
                                            <form method="POST" action="staff.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="employee_id" value="<?php echo $row['employee_id'] ?? ''; ?>">
                                                <button type="submit" class="delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No staff records found</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // JavaScript for handling Edit button clicks
        document.addEventListener('DOMContentLoaded', function() {
            // Get form elements
            const formAction = document.getElementById('form-action');
            const employeeId = document.getElementById('employee-id');
            const staffName = document.getElementById('staff-name');
            const staffAge = document.getElementById('staff-age');
            const staffCivilStatus = document.getElementById('staff-civil-status');
            const staffAddressId = document.getElementById('staff-address-id');
            const addBtn = document.getElementById('add-btn');
            const updateBtn = document.getElementById('update-btn');
            const deleteBtn = document.getElementById('delete-btn');
            
            // Add event listeners to all Edit buttons
            const editButtons = document.querySelectorAll('.edit-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const age = this.getAttribute('data-age');
                    const civil = this.getAttribute('data-civil');
                    const address = this.getAttribute('data-address');
                    
                    // Populate form with employee data
                    employeeId.value = id;
                    staffName.value = name;
                    staffAge.value = age;
                    staffCivilStatus.value = civil;
                    staffAddressId.value = address;
                    
                    // Change form action to update
                    formAction.value = 'update';
                    addBtn.textContent = 'Update';
                });
            });
            
            // Update button functionality
            updateBtn.addEventListener('click', function() {
                if (employeeId.value) {
                    formAction.value = 'update';
                    document.querySelector('form').submit();
                } else {
                    alert('Please select a staff member to update');
                }
            });
            
            // Delete button functionality
            deleteBtn.addEventListener('click', function() {
                if (employeeId.value) {
                    if (confirm('Are you sure you want to delete this staff member?')) {
                        formAction.value = 'delete';
                        document.querySelector('form').submit();
                    }
                } else {
                    alert('Please select a staff member to delete');
                }
            });
        });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>