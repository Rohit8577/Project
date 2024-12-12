<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$db = 'project';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['shop-id'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $shop_id = $_POST['shop-id'];

    // Prepare SQL query to avoid SQL injection
    $query = $conn->prepare("SELECT username, password, shop_code FROM admins WHERE email = ? AND shop_code = ?");
    $query->bind_param("ss", $email, $shop_id); 
    $query->execute();
    $result = $query->get_result();

    // Check if any results were returned
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        
        if ($password === $row['password'])  {  
            
            if (isset($row['username'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['shop'] = $row['shop_code'];
                header("Location: home.php"); 
                exit();
            } else {
                $error_message =  "Username not found.";
            }
        } else {
              $error_message =  "Incorrect password.";
        }
    } else {
        $error_message =  "No user found with the provided email and shop ID.";
    }

    $query->close();
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <div class="admin-body">
    <body>
        <div class="goto-main-page">
          <a href="index.html">Go To Main Page</a>
        </div>
      <div class="admin-login-container">
      <?php
if (!empty($error_message)) {
    echo '<div class="error-message" id="errorBox">';
    echo '<span> ⚠ ' . htmlspecialchars($error_message) . '</span>';
    echo '<button class="add-user-close-btn" onclick="closeError()">✖</button>';
    echo '</div>';
}
?>
        <div class="admin-login-heading">
        <h1>Admin Login</h1>
        </div>
        <div class="admin-line">
          <hr />
        </div>
        <form class="admin-login-form" method="POST">
          <div class="form-group">
            <label for="admin-email"><b>Email</b></label>
            <input
              type="email"
              id="admin-email"
              name="email"
              autocomplete="off"
              placeholder="Enter Admin Email"
              required
            />
          </div>
          <div class="form-group">
            <label for="admin-password"><b>Password</b></label>
            <input
              type="password"
              id="admin-password"
              name="password"
              placeholder="Enter Password"
              required
            />
          </div>
          <div class="form-group">
            <label for="shop-id"><b>Shop-Id</b></label>
            <input
              type="number"
              id="shop-id"
              name="shop-id"
              placeholder="Enter shop-id"
              required
            />
          </div>
          <button type="submit" class="login-btn"><b>Login</b></button>
        </form>

        <div class="new-registration-button">
          <a href="register1.php" 
            ><b>New Registration</b></a>
          
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
  </div>
</html>
