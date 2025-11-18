<?php 
require_once 'config.php';

$editMode = false;
$editID = 0;
$editName = '';
$editNum = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add-position'])) {
        $posName = $_POST['position-name'];
        $numOfPositions = intval($_POST['no-of-position']);
        
        mysqli_query($conn, "INSERT INTO positions (posName, numOfPositions) VALUES ('$posName', $numOfPositions)");
    }

    if (isset($_POST['edit-position'])) {
        $editID = intval($_POST['position-id']);
        $result = mysqli_query($conn, "SELECT * FROM Positions WHERE posID = $editID");
        $row = mysqli_fetch_assoc($result);
        
        if ($row)
            $editMode = true;
            $editName = $row['posName'];
            $editNum = $row['numOfPositions'];
    }

    if (isset($_POST['save-edit'])) {
        $editID = intval($_POST['position-id']);
        $editName = $_POST['position-name'];
        $editNum = intval($_POST['no-of-position']);
        
        $sql = "UPDATE positions SET 
        posName = COALESCE(NULLIF('$editName',''), posName), 
        numOfPositions = COALESCE($editNum, numOfPositions)
        WHERE posID = $editID";

        mysqli_query($conn, $sql);
    } 

    if (isset($_POST['toggle-position'])) {
        $id = intval($_POST['position-id']);
        $result = mysqli_query($conn, "SELECT posStat from positions WHERE posID = $id");

        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $newStatus = ($row['posStat'] == 'open') ? 'closed' : 'open';
            mysqli_query($conn, "UPDATE positions SET posStat = '$newStatus' WHERE posID = $id"); 
        }
    }
}

$positions = mysqli_query($conn, "SELECT * FROM positions");
?>



<table border="1">
    <thead>
        <tr>
            <th>Position Name</th>
            <th>No. of Position</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <?php while($row = mysqli_fetch_assoc($positions)) {  ?>
        <tr>
            <td><?php echo $row['posName']?></td>
            <td><?php echo $row['numOfPositions']?></td>
            <td><?php echo $row['posStat']?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="position-id" value="<?php echo $row['posID']?>">
                    <button type="submit" name="edit-position">Edit</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="position-id" value="<?php echo $row['posID']?>">
                    <button type="submit" name="toggle-position">Deactivate</button>
                </form>
            </td>
        </tr>
    <?php } ?>

</table>


<form action="" method="POST">
    <input type="text" name="position-name" placeholder="position name">
    <input type="number" name="no-of-position" placeholder="no. of position">
    <button type="submit" name="add-position">Add Position</button>
</form>


<?php if($editMode) { ?>
<div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:20px; border-radius:5px; width:300px;">
        <h3>Edit Position</h3>
        <form method="POST">
            <input type="hidden" name="position-id" value="<?php echo $editID ?>">
            <input type="text" name="position-name" value="<?php echo $editName ?>" required>
            <input type="number" name="no-of-position" value="<?php echo $editNum ?>" required>
            <button type="submit" name="save-edit">Save</button>
        </form>
    </div>
</div>

<?php } ?>