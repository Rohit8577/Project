<?php
include 'db.php';
session_start();
$shop = $_SESSION['shop'];

// Fetch pending requests count
$sql = "SELECT COUNT(*) as count FROM users_info WHERE status = 'pending' AND shop_code = $shop";
$result = $con->query($sql);
$pendingRequests = 0;

if ($result && $row = $result->fetch_assoc()) {
    $pendingRequests = $row['count'];
}

// Display success message
$success = '';
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// KYC Filter logic
$kycFilter = isset($_GET['kyc']) ? $_GET['kyc'] : 'all';
$filterQuery = "";
if ($kycFilter == 'done') {
    $filterQuery = "AND kyc = 1";
} elseif ($kycFilter == 'pending') {
    $filterQuery = "AND kyc = 0";
}

// SQL query for users
$sql = "SELECT * FROM users_info WHERE shop_code = $shop AND status = 'approved' $filterQuery";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>User Manage</title>
</head>

<body>
<div class="user-manage-body">
    <nav class="ho">
        <div class="welcome-message">
            <b>Hello,</b> <span class="homepage-username"><b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
        </div>
        <h2><i>PUBLIC DISTRIBUTION SYSTEM</i></h2>
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
        <a href="distribute.php"><b>Distribute Ration</b></a>
        <a href="add_user.php"><b>Add New Customer</b></a>
        <a href="logout.php"><b>Logout</b></a>
    </div>

    
    <?php if (!empty($success)): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success); ?>
            <span class="close-btn" onclick="closeMessage()">&times;</span>
        </div>
    <?php endif; ?>
    
        
        <div class="user-manage-table" id="manage-table">
        <h2>All Customer</h2>
    <div class="filter-section">
        <form method="GET" action="user_manage.php">
            <select name="kyc" id="kycFilter" onchange="this.form.submit()">
                <option value="all" <?php echo ($kycFilter == 'all') ? 'selected' : ''; ?>>All</option>
                <option value="done" <?php echo ($kycFilter == 'done') ? 'selected' : ''; ?>>KYC Done</option>
                <option value="pending" <?php echo ($kycFilter == 'pending') ? 'selected' : ''; ?>>KYC Pending</option>
            </select>
        </form>
    </div>
        <table>
            <thead>
                <tr>
                    <th scope="col">Shop - Id</th>
                    <th scope="col">Card - No.</th>
                    <th scope="col">Cardholder - Name</th>
                    <th scope="col">No. of Units</th>
                    <th>Mobile Number</th>
                    <th>KYC</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id = $row['shop_code'];
                        $card = $row['card_no'];
                        $name = $row['name'];
                        $units = $row['no_of_units'];
                        $kyc = $row['kyc'];
                        $mobile = $row['mobile'];
                        $kyc_status = ($kyc == 1) ? '<span style="color:green;">&#10004;</span>' : '<span style="color:red;">&#10006;</span>';

                        echo '<tr>
                                <th scope="row">' . $id . '</th>
                                <td>' . $card . '</td>
                                <td>' . $name . '</td>
                                <td>' . $units . '</td>
                                <td>' . $mobile . '</td>
                                <td>' . $kyc_status . '</td>
                                <td class="action-container">
                                    <a href="update_cus.php?updateid=' . $card . '" class="btn btn-primary">Update</a>
                                    <h2>|</h2>
                                    <a href="delete.php?deleteid=' . $card . '" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" style="text-align:center;">No records found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function closeMessage() {
            const messageBox = document.querySelector('.success-message');
            if (messageBox) {
                messageBox.style.display = 'none';
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const hamburgerIcon = document.getElementById("hamburger-icon");
            const sidebar = document.getElementById("sidebar");
            const closeSidebar = document.getElementById("close-sidebar");
            const mainContent = document.getElementById("manage-table");

            hamburgerIcon.addEventListener("click", () => {
                sidebar.style.right = "0";
                mainContent.style.marginRight = "300px"; 
            });

            closeSidebar.addEventListener("click", () => {
                sidebar.style.right = "-300px";
                mainContent.style.marginRight = "0"; 
            });
        });
    </script>
</div>
</body>
</html>
