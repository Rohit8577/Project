<?php
include 'db.php';
session_start();
$id = $_SESSION['shop'];
$unit = $_SESSION['totalunit'];
$success = "";
$error = "";

if(isset($_POST['Add']))
{
    $name = $_POST['commodity_name'];
    $quntity = (int)$_POST['quantity_new'];
    $action = $_POST['addelete'];
    $check = $con->query("SELECT * from inventory1 where shop_code = '$id' AND commodity_name = '$name'");

    if($check->num_rows)
    {
        if($action === "delete")
        {
            $delete = $con->query("DELETE FROM inventory1 WHERE shop_code = '$id' AND commodity_name = '$name'");
            if($delete)
            {
                $success =  "Commodity Deleted Successfully";
            }
        }elseif($action === "ADD"){
            $error =  "Commodity Already Exist"; 
        }
    }else{
        if($action === "delete"){
            $error =  "Commodity Not Exist";
        }elseif($action === "ADD")
        {
            $insert = $con->query("INSERT INTO inventory1 (shop_code,commodity_name,unit,stock,last_updated) VALUES('$id','$name','100','$quntity',NOW())");
            $success =  "Added Successfully";
        }
    }
}
     



if(isset($_POST['update']))
{
    $comm = $_POST['commodity_select'];
    (int)$update = (int)$_POST['quantity_update'];
    $value = $_POST['update_action'];

    if($update <= 0)
    {
        $error =  "Quantity must be a positive number";
        echo $update;
    }else{
        $check1 = $con->query("SELECT * from inventory1 where shop_code = '$id' AND commodity_name ='$comm'");
    if($check1->num_rows)
    {
        $row1 = $check1->fetch_assoc();
        $currentStock = (int)$row1['stock'];
        $newstock =0;
        if($value){
            if($value === "add")
        {
            $newstock = $currentStock + $update;
        }elseif($value === "remove")
        {
            if($update > $currentStock)
            {
                $newstock = $currentStock; 
                $error =  "Error: Cannot remove more stock than available stock";
            }else{
                $newstock = $currentStock - $update;
            }
        }

        $updt = $con->query("UPDATE inventory1 SET stock = $newstock, last_updated = NOW() where shop_code = '$id' AND commodity_name = '$comm'");
        if($updt && $newstock!== $currentStock)
        {
            $success = "Inventory Updated";
        }else{
            $error = "Updation Failed";
        }
        
        }else{
            $error = "Please Select An Option";
        }

        
    }else{
        $error = "Commodity Not Found";
    }
    }

    
}

$sql = "SELECT * FROM inventory1 where shop_code = $id";
$result = $con->query($sql);


$sql1 = "SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $id";
$result2 = $con->query($sql1);
$pendingRequests = 0;

if ($result2 && $row = $result2->fetch_assoc()) {
    $pendingRequests = $row['count'];
}

$commodity = $con->query("SELECT commodity_name from inventory1 where shop_code = $id");

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">\
    <link rel="stylesheet" href="styles.css">
    <title>Manage Inventory</title>
</head>
<body class="inventory-body">
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


    <div class="container">
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

        <h1 class="text-center">Manage Inventory</h1>
        <div class="inventory-line">
            <hr>
        </div>
        <h3>Inventory Overview</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Commodity Name</th>
                    <th>Unit</th>
                    <th>Max. Stock</th>
                    <th>Current Stock</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody id="inventoryTable">
                <?php
                        
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc())
                        {
                            echo "<tr>";
                            echo "<td>{$row['commodity_name']}</td>";
                            echo "<td>".$unit."</td>";
                            echo "<td>".($unit*5)."</td>";
                            echo "<td>{$row['stock']}</td>";
                            echo "<td>{$row['last_updated']}</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </tbody>
        </table>
        

<form method="POST">
              <label class="toggle-switch1">
                  <input type="checkbox" id="actionToggle">
                  <span class="slider"></span>
              </label>
              <span id="toggleLabel"></span>
  


    <!-- Fields for Add New Commodity -->
    <div id="addNewSection">
        <h2>Add/Delete Commodity</h2>
        <div class="add-update-line">
            <hr>
        </div>
        <div class="form-group">
        <label for="commoditySelect" class="form-label">Commodity Name</label>
            <input type="text" name="commodity_name" autocomplete="off"  id="commoditySelect" class="form-control">
        </div>
        <div class="form-group">
            <label for="quantityNew" class="form-label">Initial Quantity (kg/liters)</label>
            <input type="number" name="quantity_new" id="quantityNew" autocomplete="off"  class="form-control" required>
        </div>
        <div class="form-group">
            <label for="updateAction" class="form-label">Action</label>
            <select name="addelete" id="updateAction" class="form-control">
                <option value="ADD">Add Commodity</option>
                <option value="delete">Remove Commodity</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="Add">Submit</button>
    </div>

</form> 



<form method ="POST">
    <div id="updateStockSection" style="display: none;">
        <h2>Update Stock</h2>
        <div class="add-update-line">
            <hr>
        </div>
        <div class="form-group">
            
<select name="commodity_select"  required>
    <option value="">Select Com modity</option>
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
        </div>
        <div class="form-group">
            <label for="quantityUpdate" class="form-label">Quantity (kg/liters)</label>
            <input type="number" name="quantity_update" autocomplete="off"  id="quantityUpdate" class="form-control">
        </div>
        <div class="form-group">
            <label for="updateAction" class="form-label">Action</label>
            <select name="update_action" id="updateAction" class="form-control">
                <option value="">Select an option</option>
                <option value="add">Add Stock</option>
                <option value="remove">Remove Stock</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Submit</button>
    </div>
</form>

<script >
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


const actionToggle = document.getElementById('actionToggle');
  const addNewSection = document.getElementById('addNewSection');
  const updateStockSection = document.getElementById('updateStockSection');
  
  
  updateStockSection.style.display = 'none';
  
  
  actionToggle.addEventListener('change', () => {
      if (actionToggle.checked) {
          addNewSection.style.display = 'none';
          updateStockSection.style.display = 'block';
      } else {
          addNewSection.style.display = 'block';
          updateStockSection.style.display = 'none';
      }
  });
    </script>
</body>
</html>
