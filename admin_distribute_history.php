<?php
include 'db.php';
session_start();
$id = $_SESSION['shop'];

$sql = "SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $id";
$result = $con->query($sql);
$pendingRequests = 0;

if ($result && $row = $result->fetch_assoc()) {
    $pendingRequests = $row['count'];
}

$date = $con->query("SELECT DATE(distribution_date) AS date from ration_distribution where admin_id = $id GROUP BY DATE(distribution_date)");
$comm_name = $con -> query("SELECT item_name from ration_distribution where admin_id = $id GROUP BY item_name");
if($comm_name->num_rows)
{
  
  while($item_name = $comm_name -> fetch_assoc())
  {
    $name[] = $item_name['item_name'];
  }

}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution Table</title>
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


        <div class="history-container" id = "main-content">
        <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Stock</th>
                <!-- <th>View Details</th> -->
            </tr>
        </thead>
        <tbody>
            <?php if($date->num_rows)
            {$i=0;
              while($fetch = $date->fetch_assoc())
              {
                echo "<tr>";
                echo  "<td rowspan = '2'>" .$fetch['date'] ."</td>";
                echo "<td>" ."rice". "</td>";
                // echo "<td>". "<button>view</button>". "</td>";
                echo "</tr>";
                $i++;
              }
            } ?>
        </tbody>
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
