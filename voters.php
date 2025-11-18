<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add-voter'])) {
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $password = $_POST['password'];

        $sql = "INSERT INTO voters (voterPass, voterFName, voterMName, voterLName) VALUES ('$password', '$firstname', '$middlename', '$lastname')";
        mysqli_query($conn, $sql);
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
                <form method="POST">
                    <input type="hidden" name="voter-id" value="<?= htmlspecialchars($row['voterID']) ?>">
                    <button type="submit" name="edit-voter">Edit</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="voter-id" value="<?= htmlspecialchars($row['voterID']) ?>">
                    <button type="submit" name="toggle-voter">Deactivate</button>
                </form>


            </td>
        </tr>
    <?php }?>

</table>



<form method="POST">
    <input type="text" name="firstname" placeholder="firstname">
    <input type="text" name="middlename" placeholder="middlename">
    <input type="text" name="lastname" placeholder="lastname">
    <input type="password" name="password" placeholder="*******">

    <button type="submit" name="add-voter">Add Voter</button>
</form>