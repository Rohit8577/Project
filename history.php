<?php
include 'db.php';
session_start();

$id = $_SESSION['shop'];
$card = $_SESSION['card'];
$username = htmlspecialchars($_SESSION['username']);

// Fetch distribution days
$result = $con->query("SELECT DATE(distribution_date) AS distribution_day, card_no 
                       FROM ration_distribution 
                       WHERE admin_id = $id AND card_no = $card 
                       GROUP BY DATE(distribution_date) 
                       ORDER BY distribution_day");

// Fetch inventory
$result1 = $con->query("SELECT * FROM inventory1 WHERE shop_code = $id");


// Check for selected date
$selected_date = null;
$stock_data = [];
$total = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_date'])) {
    $selected_date = $_POST['selected_date'];

    $comm = $con->query("SELECT item_name from ration_distribution where admin_id = $id AND DATE(distribution_date) = '$selected_date' GROUP BY item_name");
    while ($row = $comm->fetch_assoc()) {
        $stock_data[] = $row['item_name'];
    }

    foreach($stock_data as $comm_name)
    {
        $sql = $con->query("SELECT SUM(quantity) as total_sum from ration_distribution where admin_id = $id AND item_name = '$comm_name' AND DATE(distribution_date) = '$selected_date'");
        $row1 = $sql->fetch_assoc();
        $total[] = $row1['total_sum'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="user-history-body">

<!-- Navigation Bar -->
<nav class="ho">
    <div class="welcome-message">
      <b>Hello,</b> <span class="homepage-username"><b><?php echo $username; ?></b></span>
    </div>
    <h2><i>PUBLIC DISTRIBUTION SYSTEM</i></h2>
    <div id="hamburger-icon">☰</div>
</nav>

<!-- Sidebar -->
<div id="sidebar">
    <button id="close-sidebar">&times;</button>
    <a href="display.php"><b>Home</b></a>
    <a href="profile.php"><b>Profile</b></a>
    <a href="update_profile.php"><b>Edit Profile</b></a>
    <a href="history.php"><b>Ration History</b></a>
    <a href="logout1.php"><b>Logout</b></a>
</div>

<!-- Distribution History Table -->
<div class="history-container">
    <h3>Distribution History</h3>
    <table class="user-table">
        <thead>
            <tr>
                <th>Card Number</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['card_no']; ?></td>
                <td><?php echo $row['distribution_day']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="selected_date" value="<?php echo $row['distribution_day']; ?>">
                        <div class="viewpass">
                        <button type="submit" onclick="openPopup()">View Stock</button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Popup -->
<div id="statusPopup" class="popup">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <?php if ($selected_date): ?>
        <div class="stock-container">
            <h3>Commodity Stock for <?php echo $selected_date; ?></h3>
            <?php if (!empty($stock_data)): ?>
                <table class="stock-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity (in KG)</th>
                        </tr>
                    </thead>
                    <tbody>
        <?php for ($i = 0; $i < count($stock_data); $i++): ?>
            <tr>
                <td><?php echo htmlspecialchars($stock_data[$i]); ?></td>
                <td><?php echo htmlspecialchars($total[$i] ?? 0); ?></td>
            </tr>
        <?php endfor; ?>
    <?php endif; ?>
</tbody>
                </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Sidebar Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const hamburgerIcon = document.getElementById("hamburger-icon");
    const sidebar = document.getElementById("sidebar");
    const closeSidebar = document.getElementById("close-sidebar");

    hamburgerIcon.addEventListener("click", () => {
        sidebar.style.right = "0";
    });

    closeSidebar.addEventListener("click", () => {
        sidebar.style.right = "-300px";
    });

    const selectedDate = "<?php echo $selected_date ? $selected_date : ''; ?>";
    if (selectedDate) {
        document.getElementById("statusPopup").style.display = "flex";
    }
});

function openPopup() {
    document.getElementById("statusPopup").style.display = "flex";
}

function closePopup() {
    document.getElementById("statusPopup").style.display = "none";
}
</script>

</body>
</html>
