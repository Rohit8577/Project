<?php
include 'db.php';
session_start();
if(isset($_POST['submit']))
{
    $pass = $_POST['password'];
    $card = $_POST['ration_card_number'];
    $shop = $_POST['shop_id'];

    $sql = "SELECT * from users_info WHERE card_no = $card AND shop_code = $shop AND password = $pass";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if($row['status'] === 'Approved'){
            $_SESSION['pass'] = $row['password'];
        $_SESSION['shop'] = $shop;
        $_SESSION['card'] = $row['card_no'];
        $_SESSION['username'] = $row['name'];
        $updt = $con->query("UPDATE request set status1 ='Viewed' where card_no1 = $card");
        header("Location: display.php");
        exit();
        }else{
            $error_message = "Invalid Card Number";
        }
    } else {
       $error_message = "Invalid Card Number or Password";
    }

    
} 


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="user-body">

    <div class="goto-main-page">
        <a href="index.html">Go To Main Page</a>
    </div>
    <div class="user-login-container">
    <?php
if (!empty($error_message)) {
    echo '<div class="error-message" id="errorBox">';
    echo '<span> ⚠ ' . htmlspecialchars($error_message) . '</span>';
    echo '<button class="add-user-close-btn" onclick="closeError()">✖</button>';
    echo '</div>';
}
?>
        <h1>User Login</h1>
        <div class="user-login-hr">
            <hr>
        </div>
        <form class="user-login-form" action="userlogin.php" method="POST">
        <div class="form-group">
                <label for="ration-card-number"><b>Ration Card Number</b></label>
                <input type="number" id="ration-card-number" name="ration_card_number" placeholder="Enter Ration Card Number" required>
            </div>
            <div class="form-group">
                <label for="password"><b>Password</b></label>
                <input type="number" id="user-login-password" name="password" autocomplete="off" placeholder="Enter Password" required>
            </div>
            <div class="form-group">
                <label for="shop_id"><b>Shop Id</b></label>
                <input type="number" id="shop_id" name="shop_id" placeholder="Enter Shop ID" required>
            </div>
            <button type="submit" name="submit" class="login-btn">Login</button>
        </form>
        <div class="forget">
            <a href="forget_password.php">Forget Password !!</a>
        </div>
        <div class="new-registration-button">
            <a href="request1.php"><b>Apply Ration Card</b></a>
        </div>
    </div>
   <script>
function closeError() {
    const errorBox = document.getElementById('errorBox');
    if (errorBox) {
        errorBox.style.opacity = '0'; // Fade-out effect
        setTimeout(() => {
            errorBox.style.display = 'none'; // Hide the error box after fade-out
        }, 300); // Match the duration of the fade-out effect
    }
}
   </script>

</body>
</html>
