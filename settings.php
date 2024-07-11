<?php
// Initialize the session
session_start();

// If the session variable is not set, initialize it as an empty array
if (!isset($_SESSION['electorates'])) {
    $_SESSION['electorates'] = [];
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $new_electorate = trim($_POST['new_electorate']);
        if (!empty($new_electorate) && !in_array($new_electorate, $_SESSION['electorates'])) {
            $_SESSION['electorates'][] = $new_electorate;
        }
    } elseif (isset($_POST['remove'])) {
        $remove_electorate = $_POST['remove_electorate'];
        if (($key = array_search($remove_electorate, $_SESSION['electorates'])) !== false) {
            unset($_SESSION['electorates'][$key]);
        }
    } elseif (isset($_POST['upload_csv'])) {
        if (is_uploaded_file($_FILES['electorates_csv']['tmp_name'])) {
            $file = fopen($_FILES['electorates_csv']['tmp_name'], 'r');
            while (($line = fgetcsv($file)) !== FALSE) {
                foreach ($line as $electorate) {
                    $electorate = trim($electorate);
                    if (!empty($electorate) && !in_array($electorate, $_SESSION['electorates'])) {
                        $_SESSION['electorates'][] = $electorate;
                    }
                }
            }
            fclose($file);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - 6 News Election Calculator</title>
</head>
<body>
    <h1>Settings - 6 News Election Calculator</h1>
    <p><a href="index.php">Back to Homepage</a></p>
    <h2>Electorates</h2>
    
    <form method="post">
        <label for="new_electorate">Add Electorate:</label>
        <input type="text" id="new_electorate" name="new_electorate">
        <button type="submit" name="add">Add</button>
        <p>Note: Electorates are deleted when the browser is closed</p>
    </form>

    <form method="post" enctype="multipart/form-data">
        <label for="electorates_csv">Upload Electorates (CSV):</label>
        <input type="file" id="electorates_csv" name="electorates_csv" accept=".csv">
        <button type="submit" name="upload_csv">Upload</button>
    </form>

    <h3>Current Electorates:</h3>
    <ul>
        <?php foreach ($_SESSION['electorates'] as $electorate): ?>
            <li>
                <?php echo htmlspecialchars($electorate); ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="remove_electorate" value="<?php echo htmlspecialchars($electorate); ?>">
                    <button type="submit" name="remove">Remove</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
