<?php
include 'db.php';
session_start();
if(!isset($_SESSION['shop']))
{
    header("Location: login.html");
    exit();
}
else
{
    $id = $_SESSION['shop'];
    $sql = "SELECT * from admins where shop_code = $id";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $name = $row['username'];
    $shop = $row['shop_code'];
    $pass = $row['password'];
    $email = $row['email'];
    $mobile = $row['mobile'];
    $address = $row['address'];
}

$sql = "SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $id";
$result = $con->query($sql);
$pendingRequests = 0;

if ($result && $row = $result->fetch_assoc()) {
    $pendingRequests = $row['count'];
}



if(isset($_POST['submit']))
{
    $name = $_POST['username'];
    $pass = $_POST['password'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $id= $_SESSION['shop'];
    $sql1 = "UPDATE admins SET username = '$name' , password = '$pass' , mobile = '$mobile' , address = '$address' where shop_code = $id ";
    $result1 = mysqli_query($con,$sql1);
    if($result1)
    {
        $_SESSION['success_message']="Data Updated successfully";
        $_SESSION['username'] = $name;
        header('Location: home.php');
        exit;
    }
    else{
        die(mysqli_error($con));
    }
}



?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Shop Registration Form</title>
</head>
<body class = "update-body">

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



<div class="profile-container">
    <div class="profile-heading">
    <h2>Profile</h2>
    </div>
    <div class="line">
        <hr>
    </div>
    <form method = "POST">
        <div class="form-group">
            <label for="shop-id">Shop ID</label>
            <input type="text" id="shop-id" name="shop-id" value="<?php echo $shop;?>" readonly >
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"  value="<?php echo $name;?>" autocomplete="off" >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $email;?>" autocomplete="off" readonly >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="number" id="password" name="password" value="<?php echo $pass;?>" >
        </div>

        <div class="form-group">
            <label for="mobile">Mobile</label>
            <input type="text" id="mobile" name="mobile"   autocomplete="off"  value="<?php echo (string)$mobile;?>" >
        </div>

        <div class="form-group">
            <label for="address">Address</label>
           <input type="text"  id="address" name="address" value = "<?php echo $address;?>">
        </div>
        <div class="update-button">
        <button type="submit" name = "submit">Update</button>
        </div>
        
    </form>
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
