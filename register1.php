<?php
include 'db.php';
$error_message = ""; // Initialize the error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // Directly using plain-text password
    $email = trim($_POST['email']);
    $shop = trim($_POST['shop-id']);

    // Check if the email already exists
    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $error_message = "Email already exists!";
    }
     else {
        // Check if the email and shop code combination already exists
        $sql = "SELECT * FROM admins WHERE shop_code = '$shop'";
        $result = $con->query($sql);

        if ($result->num_rows > 0) {
            $error_message = "shop code already exists!";
        } else {
            // Insert new user with plain-text password
            $sql = "INSERT INTO admins (username, password, email, shop_code) 
                    VALUES ('$username', '$password', '$email', '$shop')";

            if ($con->query($sql)) {
                header("Location: login.php"); // Redirect to login page
                exit();
            } else {
                $error_message = "Error: " . $con->error;
            }
        }
    }
}

$con->close();
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Registration</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <div class="register-body">
    <body>
      
        <div class="goto-main-page">
          <a href="index.html">Go To Main Page</a>
        </div>
      
      <div class="register-container">
        <?php
        if (!empty($error_message)) {
          echo '<div class="error-message" id="errorBox">';
          echo '<span> ⚠ ' . htmlspecialchars($error_message) . '</span>';
          echo '<button class="add-user-close-btn" onclick="closeError()">✖</button>';
          echo '</div>';
      }
      ?>

        <h1>New Admin Registration</h1>
        <div class="register-line">
          <hr />
        </div>
        <form action="register1.php" method="POST">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" autocomplete="off" name="username" required />
          </div>
          <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" autocomplete="off" name="email" required />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <div class="form-group">
            <label for="shop-id"><b>Shop-Id</b></label>
            <input type="number" id="shop-id" name="shop-id" autocomplete="off" required>
        </div>
          <button type="submit" class="new-register-btn">Register</button>
        </form>
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
  </div>
</html>
