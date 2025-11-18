<?php 
require_once 'config.php';

$edit_post = false;
$edit_id = '';
$edit_fname = '';
$edit_mname = '';
$edit_lname = '';
$edit_pass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add-voter'])) {
        $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
        $middlename = isset($_POST['middlename']) ? $_POST['middlename'] : '';
        $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO voters (voterPass, voterFName, voterMName, voterLName) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $password, $firstname, $middlename, $lastname);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['edit-voter'])) {
        $id = intval($_POST['voter-id']);
        $stmt = $conn->prepare("SELECT * FROM voters WHERE voterID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if ($row) {
            $edit_post = true;
            $edit_id = $row['voterID'];
            $edit_fname = $row['voterFName'];
            $edit_mname = $row['voterMName'];
            $edit_lname = $row['voterLName'];
            $edit_pass = $row['voterPass'];
        }
        $stmt->close();
    }

    if (isset($_POST['save-edit'])) {
        $id = intval($_POST['voter-id']);
        $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
        $middlename = isset($_POST['middlename']) ? $_POST['middlename'] : '';
        $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $stmt = $conn->prepare("UPDATE voters SET voterFName = ?, voterMName = ?, voterLName = ?, voterPass = ? WHERE voterID = ?");
        $stmt->bind_param("ssssi", $firstname, $middlename, $lastname, $password, $id);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['toggle-voter'])) {
        $id = intval($_POST['voter-id']);
        $stmt = $conn->prepare("SELECT voterStat FROM voters WHERE voterID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if($row) {
            $new_status = ($row['voterStat'] == 'active') ? 'inactive' : 'active';
            $update_stmt = $conn->prepare("UPDATE voters SET voterStat = ? WHERE voterID = ?");
            $update_stmt->bind_param("si", $new_status, $id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        $stmt->close();
    }
}

$voters = mysqli_query($conn, "SELECT * FROM voters");
?>

<table border="1">
    <thead>
        <tr>
            <th>Firstname</th>
            <th>Middlename</th>
            <th>Lastname</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <?php while($row = mysqli_fetch_assoc($voters)) {?>
        <tr>
            <td><?= htmlspecialchars($row['voterFName']) ?></td>
            <td><?= htmlspecialchars($row['voterMName']) ?></td>
            <td><?= htmlspecialchars($row['voterLName']) ?></td>
            <td><?= htmlspecialchars($row['voterStat']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="voter-id" value="<?= htmlspecialchars($row['voterID']) ?>">
                    <button type="submit" name="edit-voter">Edit</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="voter-id" value="<?= htmlspecialchars($row['voterID']) ?>">
                    <button type="submit" name="toggle-voter">
                        <?= $row['voterStat'] == 'active' ? 'Deactivate' : 'Activate' ?>
                    </button>
                </form>
            </td>
        </tr>
    <?php }?>
</table>

<form method="POST">
    <input type="text" name="firstname" placeholder="firstname" required>
    <input type="text" name="middlename" placeholder="middlename">
    <input type="text" name="lastname" placeholder="lastname" required>
    <input type="password" name="password" placeholder="*******" required>
    <button type="submit" name="add-voter">Add Voter</button>
</form>

<?php if ($edit_post) { ?>
    <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center;">
        <div style="background:#fff; padding:20px; border-radius:5px; width:300px;">
            <form method="POST">
                <input type="hidden" name="voter-id" value="<?= htmlspecialchars($edit_id) ?>">
                <input type="text" name="firstname" value="<?= htmlspecialchars($edit_fname) ?>" required>
                <input type="text" name="middlename" value="<?= htmlspecialchars($edit_mname) ?>"> 
                <input type="text" name="lastname" value="<?= htmlspecialchars($edit_lname) ?>" required>
                <input type="password" name="password" value="<?= htmlspecialchars($edit_pass) ?>" required>
                <button type="submit" name="save-edit">Save</button>
                <a href="<?= $_SERVER['PHP_SELF'] ?>" style="margin-left:10px;">Cancel</a>
            </form>
        </div>
    </div>
<?php } ?>