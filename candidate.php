<?php 
require_once 'config.php';

$editMode = false;
$editID = 0;
$editFName = '';
$editLName = '';
$editMName = '';
$editPosID = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add-candidate'])) {
        $firstname = $_POST['first-name']; 
        $middlename = $_POST['middle-name'];
        $lastname = $_POST['last-name']; 
        $id = intval($_POST['position-id']);

         $sql = "INSERT INTO candidates (candFName, candMName, candLName, posID) 
                VALUES ('$firstname', '$middlename', '$lastname', $id)";

        if (mysqli_query($conn, $sql)) {
            echo "Candidate added successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }  
    }


    if (isset($_POST['edit-candidate'])) {
        $id = intval($_POST['candidate-id']);
        $res = mysqli_query($conn, "SELECT * FROM candidates WHERE candID = $id");

        $row = mysqli_fetch_assoc($res);
        if ($row ){
            $editMode = true;
            $editID = $id; // ADD THIS LINE - you were missing it!
            $editFName = $row['candFName'];
            $editMName = $row['candMName'];
            $editLName = $row['candLName'];
            $editPosID = $row['posID'];
        }
    }

    // Add this handler for saving edits
        if (isset($_POST['save-edit'])) {
            $id = intval($_POST['candidate-id']);
            $firstname = $_POST['first-name']; 
            $middlename = $_POST['middle-name'];
            $lastname = $_POST['last-name']; 
            $posID = intval($_POST['position-id']);

            $sql = "UPDATE candidates 
                    SET candFName = '$firstname', candMName = '$middlename', 
                        candLName = '$lastname', posID = $posID 
                    WHERE candID = $id";

            if (mysqli_query($conn, $sql)) {
                echo "Candidate updated successfully!";
                $editMode = false; // Reset edit mode
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }

    if (isset($_POST['toggle-candidate'])) {
        $id = intval($_POST['candidate-id']);
        $res = mysqli_query($conn, "SELECT candStat FROM candidates WHERE candID = $id");
        
        $row = mysqli_fetch_assoc($res);

        if ($row) {
            $newStatus = ($row['candStat'] == 'active') ? 'inactive' : 'active';
            mysqli_query($conn, "UPDATE candidates SET candStat = '$newStatus' WHERE candID = $id");
        }

    }
}

$positions = mysqli_query($conn, "SELECT * FROM positions") ;
$candidates = mysqli_query($conn, "SELECT * FROM candidates c
                            LEFT JOIN positions p ON c.posID = p.posID"); 
?>

<table border="1">
    <thead>
        <tr>
            <th>Firstname</th>
            <th>Middlename</th>
            <th>Lastname</th>
            <th>Position</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <?php while($row = mysqli_fetch_assoc($candidates)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['candFName']) ?></td>
            <td><?= htmlspecialchars($row['candMName']) ?></td>
            <td><?= htmlspecialchars($row['candLName']) ?></td>
            <td><?= htmlspecialchars($row['posName']) ?></td>
            <td><?= htmlspecialchars($row['candStat']) ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="candidate-id" value="<?= htmlspecialchars($row['candID'])?>">
                    <button type="submit" name="edit-candidate">Edit</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="candidate-id" value="<?= htmlspecialchars($row['candID'])?>">
                    <button type="submit" name="toggle-candidate">Deactivate</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>


<form method="POST">
    <input type="text" name="first-name" placeholder="firstname">
    <input type="text" name="middle-name" placeholder="middlename">
    <input type="text" name="last-name" placeholder="lastname">
    <select name="position-id" >
        <option value="">Select Position</option>
        <?php while ($row = mysqli_fetch_assoc($positions)) : ?>
            <option value="<?= $row['posID']?>"><?= htmlspecialchars($row['posName']) ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit" name="add-candidate">Add Canidate</button>
</form>


<?php if($editMode) { ?>
    <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center;">
        <div style="background:#fff; padding:20px; border-radius:5px; width:300px;">
         <form method="POST">
                <input type="hidden" name="candidate-id" value="<?= $editID ?>"> <!-- Add this -->
                <input type="text" name="first-name" placeholder="firstname" value="<?= htmlspecialchars($editFName) ?>">
                <input type="text" name="middle-name" placeholder="middlename" value="<?= htmlspecialchars($editMName) ?>">
                <input type="text" name="last-name" placeholder="lastname" value="<?= htmlspecialchars($editLName) ?>">
                <select name="position-id">
                    <option value="">Select Position</option>
                    <?php 
                    // Re-query positions for the edit form
                    $positions2 = mysqli_query($conn, "SELECT * FROM positions");
                    while ($pos = mysqli_fetch_assoc($positions2)) : 
                    ?>
                        <option value="<?= $pos['posID']?>" <?= ($pos['posID'] == $editPosID) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pos['posName']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="save-edit">Save</button>
            </form>
        </div>
    </div>
    
    
<?php } ?>