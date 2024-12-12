<?php
include 'db.php';
session_start();
$shop =$_SESSION['shop'];
$sql = "SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $shop";
$result = $con->query($sql);
$pendingRequests = 0;

if ($result && $row = $result->fetch_assoc()) {
    $pendingRequests = $row['count'];
}

if (isset($_GET['updateid'])) {
    $id = $_GET['updateid'];
    $sql = "SELECT * FROM users_info WHERE card_no = '$id'";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['name']; 
        $units = $row['no_of_units'];
        $kyc = $row['kyc'];
        $mobile = $row['mobile'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_name = $_POST['card-holder-name'];
    $unit = $_POST['number-of-units'];
    $mob = $_POST['mobile'];
    $kyc_status = isset($_POST['KYC']) ? 1 : 0; 

    // Updated query with KYC status
    $sql = "UPDATE users_info SET name = '$card_name', no_of_units = $unit, kyc = $kyc_status, mobile = '$mob' WHERE card_no = $id";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Data Updated Successfully";
        header("Location: user_manage.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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

<div class="user-login-body">
    <div class="new-user-container">
        <div class="update-user-heading">
            <h1>Update Customer Data</h1>
        </div>
        <div class="common-line">
            <hr>
        </div>
        <form class="new-user-form" method="POST">
            <div class="form-group">
                <label for="card-holder-name"><b>Card Holder Name</b></label>
                <input type="text" id="card-holder-name" name="card-holder-name" autocomplete="off" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
                <label for="card-number"><b>Card Number</b></label>
                <input type="number" id="card-number" name="card-number" value="<?php echo $id; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="number-of-units"><b>Number of Units</b></label>
                <input type="number" id="number-of-units" name="number-of-units" value="<?php echo $units; ?>" required>
            </div>
            <div class="form-group">
            <label for="mobile">Mobile:</label>
            <input type="text" id="mobile" name="mobile" autocomplete="off"  value="<?php echo $mobile; ?>"  required>
        </div>
            <div class="form-group">
                <label for="kyc"><b>KYC</b></label>
                <input type="checkbox" id="kyc" name="KYC" value="1" <?php if ($kyc == 1) echo 'checked'; ?>>

                <!-- Show tick or cross symbol based on KYC -->
                <!-- <span class="kyc-status">
                    <?php if ($kyc == 1) { ?>
                        <span class="tick">✔</span>
                    <?php } else { ?>
                        <span class="cross">✖</span>
                    <?php } ?>
                </span> -->
            </div>

            <button type="submit" class="add-user-btn"><b>Update</b></button>
        </form>
    </div>
</div>
<script>
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

