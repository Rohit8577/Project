<?php
include 'db.php';
session_start();


if(isset($_GET['deleteid'])){
    $id = $_GET['deleteid'];
    $sql = "DELETE from users_info WHERE card_no = $id";
    $result = mysqli_query($con , $sql);
    if($result)
    {
        $_SESSION['success_message'] = "Customer Delete Successfully";
        header("Location: user_manage.php");
    }
    else
    {
        die(mysqli_error($con));
    }
}
?>
