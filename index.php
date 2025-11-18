<?php 
require_once 'config.php';
session_start();

// Already logged in
if (isset($_SESSION['voterID'])) {
    header('Location: voting.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['voterID']);
    $voterPass = $_POST['voterPass'];

    // Simple query
    $sql = "SELECT * FROM voters WHERE voterID = $id AND voterPass = '$voterPass' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $voter = mysqli_fetch_assoc($res);

        if ($voter['voterStat'] === 'active' && $voter['voted'] === 'n') {
            $_SESSION['voterID'] = $voter['voterID'];
            $_SESSION['voterStat'] = $voter['voterStat'];
            $_SESSION['voted'] = $voter['voted'];

            header('Location: voting.php');
            exit;
        } else {
            $error = 'You are not eligible to vote.';
        }
    } else {
        $error = 'Invalid ID or password.';
    }
}
?>

<h2>Voter Login</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <div>
        <label>Voter ID:</label>
        <input type="text" name="voterID" required>
    </div>
    <div>
        <label>Password:</label>
        <input type="password" name="voterPass" required>
    </div>
    <div>
        <button type="submit">Login</button>
    </div>
</form>
