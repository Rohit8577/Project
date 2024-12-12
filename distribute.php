<?php
session_start();
include 'db.php'; 
$id = $_SESSION['shop'];
$count = 0;
$success = "";
$error = "";

$sql = "SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $id";
$result = $con->query($sql);
$pendingRequests = 0;

if ($result && $row = $result->fetch_assoc()) {
    $pendingRequests = $row['count'];
}

if (isset($_POST['submit'])) {
    if (!empty($_POST['selected_users'])) {
        $selected_card_nos = $_POST['selected_users'];
        $item_name = $_POST['item_name'];
        $quantity = (int)$_POST['quantity'];
        $admin_id = $id;

        foreach($selected_card_nos as $card_no)
        {
            $count++;
        }
        $newQuantity = $count * $quantity;
        $check = $con->query("SELECT stock from inventory1 where shop_code = $id AND commodity_name='$item_name'");
        $stock = $check->fetch_assoc();
        if($newQuantity > $stock['stock'])
        {
            $error = "Error:Available stock is " . $stock['stock'] .  "KG , Distribution Quantity is " . $newQuantity . "KG";

        }else{
            foreach ($selected_card_nos as $card_no) {
                        
                        $insert_query = $con->query("INSERT INTO ration_distribution (admin_id, card_no, item_name, quantity, distribution_date, status)
                                         VALUES ('$admin_id', '$card_no', '$item_name', '$quantity', NOW(), 'Completed')");
            }
            $oldStock = (int)$stock['stock'];
            $newstock = $oldStock - (int)$newQuantity;
            $updt = $con->query("UPDATE inventory1 SET stock = $newstock, last_updated = NOW() where shop_code = '$id' AND commodity_name = '$item_name'");
            $success = "Ration distribution completed!";
        }

    }else{
        $error = "No customer selected for ration distribution.";
    }
}


$commodity = $con->query("SELECT commodity_name FROM inventory1 where shop_code = $id");

$comm_history = $con->query("SELECT * from ration_distribution where admin_id = $id GROUP BY DATE(distribution_date)");
$row1 = $comm_history->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Ration Distribution</title>
</head>
<body class = "distribute-body">
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


    
    <form id="distributionForm" method="POST">
    <?php if (!empty($success)): ?>
<div class="success-message2">
    <span class="tick-symbol">✔</span>
    <?php echo htmlspecialchars($success); ?>
    <span class="close-btn" onclick="closeMessage()">&times;</span>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="error-msg2">
    <span class="tick-symbol">⚠</span>
    <?php echo htmlspecialchars($error); ?>
    <span class="close-btn" onclick="closeMessage()">&times;</span>
</div>
<?php endif; ?>
    <h2>Distribute Ration</h2>
    
    <div class="admin-distribution-history">
    <a href="admin_distribute_history.php">View History</a>
    </div>
    
        <table class="distribution-table" border="1">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>User Name</th>
                    <th>Ration Card Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM users_info where shop_code = $id AND status = 'Approved' AND kyc = '1'";
                $result = mysqli_query($con, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td><input type='checkbox' name='selected_users[]' value='" . $row['card_no'] . "'></td>
                            <td>" . $row['name'] . "</td>
                            <td>" . $row['card_no'] . "</td>
                          </tr>";
                        }
                ?>
            </tbody>
        </table>

        <h2>Commodity Details</h2>
        <div class="ad-dist-line">
        <hr>
    </div>
        <label for="item_name">Commodity:</label>
<select name="item_name" id="item_name"  required>
    <option value="">Select Commodity</option>
    <?php
    if (mysqli_num_rows($commodity) > 0) {
        while ($row = mysqli_fetch_assoc($commodity)) {
            echo "<option value='" . htmlspecialchars($row['commodity_name']) . "'>" . htmlspecialchars($row['commodity_name']) . "</option>";
        }
    } else {
        echo "<option value=''>No commodities available</option>";
    }
    ?>
</select>


        <label for="quantity">Quantity (per user):</label>
        <input type="number" name="quantity" id="quantity" min="1" required>
        <div class="distribute-submit-button">
        <button type="submit" name = "submit" >Distribute Rations</button>
        </div>
    </form>


    <div id="statusPopup" class="popup">
    <div class="popup-content">
    <span class="close-btn" onclick="closePopup()">&times;</span>
        <table class="stock-table">
            <thead>
                <tr>
                    <td>Date</td>
                    <td>Total Quantity</td>
                    <td>View</td>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

<td></td>

<script>
        document.getElementById('selectAll').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        document.addEventListener("DOMContentLoaded", function() {
  const hamburgerIcon = document.getElementById("hamburger-icon");
  const sidebar = document.getElementById("sidebar");
  const closeSidebar = document.getElementById("close-sidebar");

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

function closeMessage() {
    const messageBox = document.querySelector('.success-message2');
    const errorBox = document.querySelector('.error-msg2');
    if (messageBox) {
        messageBox.style.display = 'none';
    }
    if (errorBox) {
        errorBox.style.display = 'none';
    }
}

function openPopup() {
    document.getElementById("statusPopup").style.display = "flex";
}

function closePopup() {
    document.getElementById("statusPopup").style.display = "none";
}
    </script>
</body>
</html>
