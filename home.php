<?php
include 'db.php';
session_start();
$id = $_SESSION['shop'];

if (!isset($_SESSION['username']) || !isset($_SESSION['shop'])) {
    header("Location: login.html");
    exit();
}

$sql = "SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $id";
$result = $con->query($sql);
$pendingRequests = 0;

if ($result && $row = $result->fetch_assoc()) {
    $pendingRequests = $row['count'];
}

$sql = "SELECT SUM(no_of_units) AS total_sum FROM users_info where shop_code = $id AND status = 'approved'";
$result = mysqli_query($con , $sql);
if ($result) {
    $row = $result->fetch_assoc();
    $_SESSION['totalunit'] = $row['total_sum'];
} else {
    echo "0 results";
}

$sql = "SELECT COUNT(*) AS total_cus FROM users_info where shop_code = $id AND status = 'approved'";
$result = mysqli_query($con , $sql);
if ($result) {
    $row1 = $result->fetch_assoc();
} else {
    echo "0 results";
}

$sql = "SELECT * FROM admins where shop_code = $id ";
$result = mysqli_query($con , $sql);
if($result){
    $row2 = $result -> fetch_assoc();
} else {
    echo "0 result";
}

$success = '';
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$inventory = $con->query("SELECT commodity_name from inventory1 where shop_code = $id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Home</title>
</head>
<body class="home-body">
<nav class="ho">
    <div class="welcome-message">
      <b>Hello,</b> <span class="homepage-username"><b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
    </div>
    <h2><I>PUBLIC DISTRIBUTION SYSTEM</I></h2>
    <div id="hamburger-icon">☰</div>
    <?php if ($pendingRequests > 0) { ?>
        <span class="dot"></span>
      <?php } ?>
</nav>

<div id="sidebar">
    <button id="close-sidebar">&times;</button>
    <a href="home.php"><b>Home</b></a>
    <a href="profile.php"><b>Profile</b></a>
    <a href="update_profile.php"><b>Edit Profile</b></a>
    <a href="request.php"><b>Requests</b> 
      <?php if ($pendingRequests > 0) { ?>
        <span class="badge"><?php echo $pendingRequests; ?></span>
      <?php } ?>
    </a>
    <a href="inventory.php"><b>Manage Inventory</b></a>
    <a href="user_manage.php"><b>Customers</b></a>
    <a href="distribute.php"><b>Distibute Ration</b></a>
    <a href="add_user.php"><b>Add New Customer</b></a>
    <a href="logout.php"><b>Logout</b></a>
</div>


<?php if (!empty($success)): ?>
    <div class="success-message1">
        <span class="tick-symbol">✔</span>
        <?php echo htmlspecialchars($success); ?>
        <span class="close-btn" onclick="closeMessage()">&times;</span>
    </div>
    <?php endif; ?>

<div id="main-content">
    <div class="home-table">
        <div class="table-heading">
            <h2 style="text-align: center;">Shop Details</h2>
        </div>
        <table class="bottom-border-table">
            <tr>
                <td>Shop Owner Name : </td>
                <td><?php echo $row2['username'];  ?></td>
            </tr>
            <tr>
                <td>Total Card Holders : </td>
                <td><?php echo $row1['total_cus']; ?>

                </td>
            </tr>
            <tr>
                <td>Total Units : </td>
                <td><?php echo $row['total_sum']; ?></td>
            </tr>
            <tr>
                <td>Shop ID : </td>
                <td><?php echo $id ?></td>
            </tr>
            <tr>
                <td>Contact : </td>
                <td><?php echo $row2['mobile']; ?></td>
            </tr>
            <tr>
                <td>Shop Location : </td>
                <td><?php echo $row2['address']; ?></td>
            </tr>
            <tr>
                <td>Type of Grain in Inventory : </td>
                <td><?php while($fetch = $inventory->fetch_assoc())
                {
                    echo $fetch['commodity_name']  .  ", ";
                } ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
function closeMessage() {
    const messageBox = document.querySelector('.success-message1');
    if (messageBox) {
        messageBox.style.display = 'none';
    }
}

// Get elements
document.addEventListener("DOMContentLoaded", function() {
  const hamburgerIcon = document.getElementById("hamburger-icon");
  const sidebar = document.getElementById("sidebar");
  const closeSidebar = document.getElementById("close-sidebar");
  const mainContent = document.getElementById("main-content");

  hamburgerIcon.addEventListener("click", () => {
    console.log("Hamburger icon clicked!");
    sidebar.style.right = "0"; 
    mainContent.style.marginRight = "300px"; 
  });

  closeSidebar.addEventListener("click", () => {
    sidebar.style.right = "-300px"; 
    mainContent.style.marginRight = "0"; 
  });
});
</script>

</body>
</html>
