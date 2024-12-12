<?php  
include 'db.php';
$comm = $con->query("SELECT item_name from ration_distribution where admin_id = 110 AND DATE(distribution_date) = '2024-12-07' GROUP BY item_name");
if($comm->num_rows)
{
    while($row = $comm->fetch_assoc())
    {
        $name[] = $row['item_name'];
    }

    foreach($name as $comm_name)
    {
        $sql = $con->query("SELECT SUM(quantity) as total_sum from ration_distribution where admin_id = 110 AND item_name = '$comm_name' AND DATE(distribution_date) = '2024-12-11'");
        $row = $sql->fetch_assoc();
        $total[] = $row['total_sum']; 
    }

    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Item Name</th><th>Total Quantity</th></tr>";

    for ($i = 0; $i < count($name); $i++) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($name[$i]) . "</td>"; 
        echo "<td>" . htmlspecialchars($total[$i]) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No commodities found for the given shop code.";
}

?>