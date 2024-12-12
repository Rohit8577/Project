<?php
include 'db.php';
session_start();

$sql = "SELECT shop_code, username FROM admins";
$result = mysqli_query($con, $sql);
$shop_owner = $con->query("SELECT * from admins");

$success = ""; 

if (isset($_POST['newUser'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $units = $_POST['units'];
    $id = $_POST['shopOwner'];  
    $check = $con->query("SELECT email from users_info where email = '$email'");
    if($check->num_rows)
    {
        $success = "User Already Exist";
    }else{
        $insert = $con->query("INSERT INTO users_info (shop_code,email,name,no_of_units,kyc,status,mobile,password) VALUE ($id,'$email','$name','$units','0','Pending','$mobile','$password')");
        $success = "Request Submitted Successfully"; 
    }
  }

  if(isset($_POST['check']))
  {
    $email1 = $_POST['email1'];
    $fetch = $con->query("SELECT * from users_info where email = '$email1'");
    if($fetch->num_rows){
        $row = $fetch->fetch_assoc();
        if($row['status'] === 'Approved')
        {
            $cemail = $row['email'];
            $card = $row['card_no'];
            $status = $row['status'];
            $shop = $row['shop_code'];
        }else{
            $cemail = $row['email'];
            $card = '_______';
            $status = $row['status'];
            $shop = $row['shop_code'];
        }
    }else{
        $cemail = "Not Found";
        $card = "Not Found";
        $status = "Not Found";
        $shop = "Not Found";
    }
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Registration Form</title>
</head>
<body class="user-request-body">

<?php if (!empty($success)): ?>
    <div class="request-success-message">
        <?php echo htmlspecialchars($success); ?>
        <span class="close-btn" onclick="closeMessage()">&times;</span>
    </div>
<?php endif; ?>

<div class="goto-main-page">
    <a href="index.html">Go To Main Page</a>
</div>

<div class="user-request-form-container">
    <h2>Registration Form</h2>
    <div class="user-request-form-line">
        <hr>
    </div>
    <form method="POST">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" autocomplete="off" placeholder="Enter your name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" autocomplete="off" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label for="mobile">Mobile:</label>
            <input type="tel" id="mobile" name="mobile" autocomplete="off" placeholder="Enter your mobile number" pattern="[0-9]{10}" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="number" id="password" name="password" autocomplete="off" placeholder="Enter your password" min="6" required>
        </div>
        <div class="form-group">
            <label for="units">Number of Units:</label>
            <input type="number" id="units" name="units" autocomplete="off" placeholder="Enter number of units" min="1" required>
        </div>
        <div class="form-group">
            <label for="shopOwner">Shop Owners:</label>
            <select id="shopOwner" name="shopOwner" required>
                <option value="">Select a shop owner</option>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['shop_code'] . '">' . $row['shop_code'] . ' - ' . $row['username'] . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="request-btn">
            <button type="submit" name="newUser">Submit Request</button>
        </div>
    </form>
    <div class="forget1">
            <button onclick="openPopup()">Check Status !!</button>
        </div>
</div>

  <!-- Popup Window -->
  <div id="statusPopup" class="popup">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <form method="POST">
        <div class="form-group">
            <label for="email"><b>Email</b></label>
            <input type="email" id="email" name="email1" autocomplete="off" placeholder="Enter Email" required/>
          </div>
          <button type="submit" class="login-btn" name="check"><b>Submit</b></button>
        </form>
        <?php if (isset($_POST['check'])) :?>
        <div class="status-table" id="request">
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Card Number</th>
                            <th>Shop Code</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <td><?php echo $cemail; ?></td>
                        <td><?php echo $card; ?></td>
                        <td><?php echo $shop ;?></td>
                        <td><?php echo $status; ?></td>
                    </tbody>
                </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function closeMessage() {
        const messageBox = document.querySelector('.request-success-message');
        if (messageBox) {
            messageBox.style.display = 'none';
        }
    }

    function openPopup() {
        document.getElementById("statusPopup").style.display = "flex";
    }

// Close the popup window
function closePopup() {
    document.getElementById("statusPopup").style.display = "none";
    document.getElementById("request").style.display = "none";
}

<?php if (isset($_POST['check'])): ?>
    document.getElementById("statusPopup").style.display = "flex";
<?php endif; ?>
</script>
</body>
</html>
