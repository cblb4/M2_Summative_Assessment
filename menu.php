// Debugging
echo "<!-- Updated menu.php with category field - " . date('Y-m-d H:i:s') . " -->";

<?php
// Include database connection 
include 'db_connect.php';

// Fetch menu items from your database
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Dulcis Maison</title>

    <!-- Link to external CSS -->
    <link rel="stylesheet" href="menu.css">

    <!-- Ionicons -->
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
                <li><a href="/M2_Summative_Assessment/index.php"><ion-icon name="home-outline"></ion-icon> Overview</a></li>
                <li><a href="staff.php"><ion-icon name="people-outline"></ion-icon> Staff</a></li>
                <li class="active"><a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a></li>
                <li><a href="order.php"><ion-icon name="cart-outline"></ion-icon> Order</a></li>
                <li><a href="/M2_Summative_Assessment/inventory.php"><ion-icon name="cube-outline"></ion-icon> Inventory</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h2>Menu</h2>
            <p>Details of all menu items</p>
            
            <div class="menu-container">
                <!-- Menu Form -->
                <form action="process_menu.php" method="post" class="menu-form">
                    <label for="menu_id">Menu ID:</label>
                    <input type="text" id="menu_id" name="menu_id" placeholder="Enter Menu ID">

                    <label for="menu_name">Menu Name:</label>
                    <input type="text" id="menu_name" name="menu_name" placeholder="Enter Menu Name">
                    
                    <!-- New Category Field -->
                    <label for="category">Category:</label>
                    <select id="category" name="category">
                        <option value="">Select Category</option>
                        <option value="Breakfast">Breakfast</option>
                        <option value="Lunch">Lunch</option>
                        <option value="Dessert">Dessert</option>
                    </select>

                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date">

                    <label for="serving">Serving:</label>
                    <input type="number" id="serving" name="serving" placeholder="Enter Serving Size">

                    <label for="price">Price:</label>
                    <input type="text" id="price" name="price" placeholder="Enter Price">

                    <div class="buttons">
                        <button type="submit" name="action" value="add" class="btn">Add</button>
                        <button type="submit" name="action" value="delete" class="btn delete">Delete</button>
                        <button type="submit" name="action" value="update" class="btn update">Update</button>
                        <button type="reset" class="btn clear">Clear</button>
                    </div>
                </form>

                <!-- Menu Table -->
                <div class="menu-table">
                    <?php if (isset($result) && $result->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Menu ID</th>
                                    <th>Menu Name</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Serving</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["menu_id"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["menu_name"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["category"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["date"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["serving"]); ?></td>
                                        <td>₱ <?php echo htmlspecialchars($row["price"]); ?></td>
                                        <td class="actions">
                                            <a class="edit-btn" href="menu.php?edit=<?php echo $row["menu_id"]; ?>">Edit</a>
                                            <a class="delete-btn" href="process_menu.php?delete=<?php echo $row["menu_id"]; ?>">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="menu-table-empty">No menu items found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

<script>
    
</script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>