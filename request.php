<?php
include 'db.php';
session_start();
$id = $_SESSION['shop'];

$fetch = $con->query("SELECT * from users_info where shop_code = $id AND status = 'Pending'");
$pendingRequests = 0;
$pendingRequests1 = 0;
$result = $con->query("SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $id");

if ($result && $row = $result->fetch_assoc()) {
    $pendingRequests = $row['count'];
}

$result = $con->query("SELECT * FROM users_info WHERE status='pending' AND shop_code = $id");
$result1 = $con->query("SELECT * FROM request WHERE status1='pending' AND shop_code = $id ORDER BY DATE(date_time) DESC");

if(isset($_POST['submit']))
{
  if($_POST['new-password'])
  {
    $action = $_POST['submit'];
    $cardNo = $_POST['card-no'];
    $newpass = $_POST['new-password'];

    if($action === "Agree")
  {
    $result2 = $con->query("UPDATE users_info set password = '$newpass' where card_no = $cardNo");
    $result3 = $con->query("UPDATE request set status1 ='approved' where card_no1 = $cardNo");
    $success = "Password Changed";
  }
} 
}

if (isset($_GET['approve'])) {
  $card = $_GET['approve'];
  $insert = $con->query("UPDATE users_info set status = 'Approved' where email = '$card'");
  $success = "Customer Approved";
} elseif (isset($_GET['reject'])) {
  $card = $_GET['reject'];
  $delete = $con->query("UPDATE users_info set status = 'Rejected' where email = '$card'");
  $success = "Customer Rejected";

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Dashboard</title>
</head>
<body class="admin-request-body">
<nav class="ho">
    <div class="welcome-message">
      <b>Hello,</b> <span class="homepage-username"><b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
    </div>
    <h2><I>PUBLIC DISTRIBUTION SYSTEM</I></h2>
    <div id="hamburger-icon">☰</div>
    <?php if ($pendingRequests > 0 || $pendingRequests1 > 0) { ?>
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

    <?php if (!empty($success) ): ?>
    <div class="request-success-message">
        <?php echo htmlspecialchars($success); ?>
        <span class="close-btn" onclick="closeMessage()">&times;</span>
    </div>
<?php endif; ?>

        <div class="admin-request-table" id = "request-table">
        <table id ="newcard">
              <label class="toggle-switch">
                  <input type="checkbox" id="actionToggle">
                  <span class="slider"></span>
              </label>
              <span id="toggleLabel"></span>
            <h2>Pending Request</h2>
    <tr>
        <th>Name</th>
        <th>Mobile No:</th>
        <th>Unit</th>
        <th>Email</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $fetch->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['mobile']; ?></td>
        <td><?php echo $row['no_of_units']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td class="admin-request-action-container">
            <a class="approve" href="request.php?approve=<?php echo $row['email']; ?>">Approve</a> |
            <a class="reject" href="request.php?reject=<?php echo $row['email']; ?>">Reject</a>
        </td>
    </tr>
    <?php } ?>
</table>

<table id ="change-req">
<thead>
    <tr>
      <th>Card Number</th>
      <th>Purpose</th>
      <th>Date-Time</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while($req = $result1->fetch_assoc()) { ?>
        <tr>
        <td><?php echo $req['card_no1']; ?></td>
        <td><?php echo $req['purpose']; ?></td>
        <td><?php echo $req['date_time']; ?></td>
        <td class="viewpass">
        <button class="view-btn" data-card="<?php echo $req['card_no1']; ?>" onclick="openPopup(this)">View</button>
</td>
    </tr>
    <?php }  ?>
    </tbody>
</table>

<div id="popup" class="popup">
    <div class="popup-content">
        <div class="popup-heading">
        <h2>Change Password</h2>
        </div>
        <form action="request.php" method = "POST" id = "myform">
        <label for="new-password">New Password:</label>
        <input type="password" id="new-password" name ="new-password" placeholder="Enter new password">
        <input type="hidden" id="card-no" name="card-no">
        
        <br>
        <div class="popup-buttons">
    <button class="agree-btn" type="submit" name="submit" value="Agree">Update</button>
    <span class="popup-close">&times;</span>
</div>
        </form>
    </div>
        </div>

    <script>
    function closeMessage() {
        const messageBox = document.querySelector('.request-success-message');
        if (messageBox) {
            messageBox.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
  const hamburgerIcon = document.getElementById("hamburger-icon");
  const sidebar = document.getElementById("sidebar");
  const closeSidebar = document.getElementById("close-sidebar");


  hamburgerIcon.addEventListener("click", () => {
    console.log("Hamburger icon clicked!");
    sidebar.style.right = "0"; 
    
  });

  closeSidebar.addEventListener("click", () => {
    sidebar.style.right = "-300px";  
  });
});


const actionToggle = document.getElementById('actionToggle');
  const newcard = document.getElementById('newcard');
  const changereq = document.getElementById('change-req');
  const toggleLabel = document.getElementById('toggleLabel');

  // Default state (Pending Request table is visible)
  document.addEventListener('DOMContentLoaded', () => {
    newcard.style.display = 'table'; // Show Pending Request table
    changereq.style.display = 'none'; // Hide Change Request table
  });

  // Toggle between tables
  actionToggle.addEventListener('change', () => {
    if (actionToggle.checked) {
      // Show Change Request table
      newcard.style.display = 'none';
      changereq.style.display = 'table';
    } else {
      // Show Pending Request table
      newcard.style.display = 'table';
      changereq.style.display = 'none';
    }
  });
// Function to open the popup
function openPopup(button) {
    const cardNumber = button.getAttribute('data-card');
    document.getElementById("card-no").value = cardNumber;
    document.getElementById('popup').style.display = 'flex';
}

const closeButton = document.querySelector('.popup-close');
    const popup = document.getElementById('popup');

    // Add a click event listener to the close button
    closeButton.addEventListener('click', () => {
        // Hide the entire popup
        popup.style.display = 'none';
    });

    function denyRequest(button) {
    const value = button.getAttribute('data-card'); 
    document.getElementById("deny").value = value;
    document.getElementById("Deny").submit();
    alert("Form Submitted but value set to deny");
}


</script>
</body>
</html>

<?php $con->close(); ?>
