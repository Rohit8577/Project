<?php
include 'db.php';
session_start();

$sql = "SELECT shop_code, username FROM admins";
$result = mysqli_query($con, $sql);

if(isset($_POST['submit']))
{
  $card = $_POST['rationCardNumber'];
  $code = $_POST['shopOwner'];

  $check = "SELECT shop_code, card_no from users_info where card_no = $card";
  $result1 = mysqli_query($con,$check);

  if(mysqli_num_rows($result1))
  {
    $req_insert = "INSERT into request(shop_code,card_no1,date_time,purpose,status1) Values ( '$code','$card', NOW(), 'Change Password' , 'pending')";
    $ins = mysqli_query($con,$req_insert);
    $_SESSION['req_card']= $card;
    $success = "Request Submitted (ShopID = " .$code. ")";
  }else{
    $forget_msg = "Invalid Credential";
  }
}

if(isset($_POST['sub']))
{
  $cardNO = $_POST['cardNO'];
  $req = $con->query("SELECT users_info.password,request.card_no1,request.status1,request.date_time
                     from users_info
                      INNER JOIN request
                      ON users_info.card_no = request.card_no1
                       where users_info.card_no = $cardNO
                       ORDER BY request.date_time DESC LIMIT 1");
  
  if ($req->num_rows > 0) {
    $row1 = $req->fetch_assoc();
    if($row1['status1'] === 'Viewed')
    {
      $password = "Not Found";
    $cardNo = "Not Found";
    $date = "Not Found";
    $status = "Not Found";
    }else{
      if($row1['status1'] === 'approved')
    {
      $cardNo = $row1['card_no1'];
    $status = $row1['status1'];
    $password = $row1['password'];
    $date = $row1['date_time'];
    }else{
      $password = "______";
    $cardNo = $_POST['cardNO'];
    $date = $row1['date_time'];
    $status = "Pending";
    }
    }
  }else {
    $password = "Not Found";
    $cardNo = "Not Found";
    $date = "Not Found";
    $status = "Not Found";
}
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles.css">
  <title>Forget Password</title>
</head>
<body class = "forget-body">
<div class="goto-main-page">
          <a href="index.html">Go To Main Page</a>
        </div>
<?php
if (!empty($forget_msg)) {
    echo '<div class="forget-msg" id="forget-msg">';
    echo '<span> ⚠ ' . htmlspecialchars($forget_msg) . '</span>';
    echo '<button class="add-user-close-btn" onclick="closeError()">✖</button>';
    echo '</div>';
}
?>
<?php if (!empty($success)): ?>
    <div class="success-message1">
        <span class="tick-symbol">✔</span>
        <?php echo htmlspecialchars($success); ?>
        <span class="close-btn" onclick="closeMessage()">&times;</span>
    </div>
    <?php endif; ?>
  <div class="forget-pass-container">
    <h2>Forget Password</h2>
    <div class="forget-pass-line">
        <hr>
    </div>
    <form method="POST">
      <div class="form-group">
        <label for="rationCardNumber">Ration Card Number</label>
        <input type="text" id="rationCardNumber" name="rationCardNumber" placeholder="Enter your ration card number" required>
      </div>
      <div class="request-btn">
      <button type="submit" name = "submit">Submit</button>
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
        <div class="popup-heading">
          <h2>Check New Password</h2>
        </div>
        <form action="forget_password.php" method="POST">
        <div class="form-group">
            <label for="admin-email"><b>Card Number</b></label>
            <input type="number" id="admin-email" name="cardNO" autocomplete="off" placeholder="Enter Card Number" required/>
          </div>
          <button type="submit" class="login-btn" name="sub"><b>Submit</b></button>
        </form>

        <?php if (isset($_POST['sub']) && isset($cardNo)): ?>
            <div class="status-table" id="forget-table">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Card No.</th>
                            <th>Status</th>
                            <th>Date Of Request</th>
                            <th>New Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($cardNo); ?></td>
                            <td><?php echo htmlspecialchars($status); ?></td>
                            <td><?php echo htmlspecialchars($date); ?></td>
                            <td><?php echo htmlspecialchars($password); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>


  <script>
function closeError() {
    const errorBox = document.getElementById('forget-msg');
    if (errorBox) {
        errorBox.style.opacity = '0'; 
        setTimeout(() => {
            errorBox.style.display = 'none'; 
        }, 300); 
    }
}

function closeMessage() {
    const messageBox = document.querySelector('.success-message1');
    if (messageBox) {
        messageBox.style.display = 'none';
    }
}

// Open the popup window
function openPopup() {
    document.getElementById("statusPopup").style.display = "flex";
}

// Close the popup window
function closePopup() {
    document.getElementById("statusPopup").style.display = "none";
    document.getElementById("forget-table").style.display = "none";
}


<?php if (isset($_POST['sub']) && isset($cardNo)): ?>
    document.getElementById("statusPopup").style.display = "flex";
<?php endif; ?>


   </script>
</body>
</html>
