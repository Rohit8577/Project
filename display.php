<?php
include 'db.php';
session_start();
$id=$_SESSION['card'];
$sql = "SELECT * from users_info where card_no = $id ";
$result = mysqli_query($con ,$sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $card = $row['card_no'];
    $mobile = $row['mobile'];
    $shop_code = $row['shop_code'];
    $unit = $row['no_of_units'];
    $kyc = $row['kyc'];
    $status = $row['status'];
    $kyc_status = ($kyc == 1)? 'Done' : 'Not Done';
}

$sql1 = "SELECT username from admins where shop_code = $shop_code";
$ans=mysqli_query($con,$sql1);
$row1=mysqli_fetch_assoc($ans);
$owner=$row1['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>User Data Display</title>
</head>
<body class="customer-details">

<nav class="ho">
    <div class="welcome-message">
      <b>Hello,</b> <span class="homepage-username"><b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
    </div>
    <h2><I>PUBLIC DISTRIBUTION SYSTEM</I></h2>
    <div id="hamburger-icon">☰</div>
</nav>

<div id="sidebar">
    <button id="close-sidebar">&times;</button>
    <a href="display.php"><b>Home</b></a>
    <a href="profile.php"><b>Profile</b></a>
    <a href="update_profile.php"><b>Edit Profile</b></a>
    <a href="history.php"><b>Ration History</b></a>
    <a href="logout1.php"><b>Logout</b></a>
</div>

    <div class="customer-detail-table-container">
        <h2>Customer Details</h2>
        <table>
            <tr>
                <td>Card Holder Name:</td>
                <td><?php echo $name; ?></td>
            </tr>
            <tr>
                <td>Ration Card Number:</td>
                <td><?php echo $card; ?></td>
            </tr>
            <tr>
                <td>Distributor Name:</td>
                <td><?php echo $shop_code, ' - ', $owner; ?></td>
            </tr>
            <tr>
                <td>Total Units:</td>
                <td><?php echo $unit; ?></td>
            </tr>
            <tr>
                <td>KYC:</td>
                <td><?php echo $kyc_status; ?></td>
            </tr>
            <tr>
                <td>Mobile:</td>
                <td><?php echo $mobile; ?></td>
            </tr>
            <tr>
                <td>Status of Request:</td>
                <td><?php echo $status; ?></td>
            </tr>
        </table>
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
