<?php
// Initialize the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calculator - 6 News Elections</title>
</head>
<body>
    <h1>6 News Election Calculator</h1>
    
    <form method="post">
        <label for="electorate">Select Electorate:</label>
        <select id="electorate" name="electorate">
            <?php if (isset($_SESSION['electorates'])): ?>
                <?php foreach ($_SESSION['electorates'] as $electorate): ?>
                    <option value="<?php echo htmlspecialchars($electorate); ?>">
                        <?php echo htmlspecialchars($electorate); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </form>

    <p><a href="index.php">Back to Homepage</a></p>
</body>
</html>
