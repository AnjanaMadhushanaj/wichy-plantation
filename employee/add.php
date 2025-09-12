<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "WICHY_COCONUT";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = $_GET['table'] ?? '';
if (empty($table)) {
    die("No table specified.");
}

// If form submitted → Insert
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cols = array_keys($_POST);
    $values = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $_POST);

    $sql = "INSERT INTO $table (" . implode(",", $cols) . ") VALUES (" . implode(",", $values) . ")";
    if ($conn->query($sql) === TRUE) {
        header("Location: display_wichy_coconut.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Record</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ---- GLOBAL STYLES & VARIABLES ----*/
:root {
    --color-bg: #c7efcb;
    --color-bg-light-card: #F1F8E9;
    --color-main-green: #A5D6A7;
    --color-dark-green-text: #2E7D32;
    --color-medium-green-text: #4CAF50;
    --color-dark-btn: #333;
    --color-gray-800: #1f2937;
    --color-gray-700: #374151;
    --color-gray-600: #4b5563;
    --primary-green: #4CAF50;
} 

.container-global {
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.hidden {
    display: none;
}

.btn {
    padding: 0.375rem 1.25rem;
    border-radius: 9999px;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s;
    display: inline-block;
    text-align: center;
    border: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
}

.btn-primary {
    background-color: var(--color-dark-green-text);
    color: white;
}

.btn-primary:hover {
    background-color: var(--color-medium-green-text);
}

.btn-light {
    background-color: var(--color-bg-light-card);
    color: var(--color-dark-green-text);
    border: 1px solid var(--color-main-green);
}

.btn-light:hover {
    background-color: var(--color-main-green);
    color: var(--color-dark-green-text);
}
</style>
<style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); width: 450px; }
    h2 { text-align: center; color: #00695c; margin-bottom: 20px; }
    label { display: block; margin-top: 12px; font-weight: bold; }
    input { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; }
    .btn { display: inline-block; padding: 10px 18px; margin-top: 20px; border-radius: 6px; font-weight: bold; text-decoration: none; }
    .btn-save { background: #28a745; color: white; }
    .btn-cancel { background: #dc3545; color: white; margin-left: 10px; }
</style>
</head>
<body>
<div class="card">
    <h2>➕ Add New Record (<?php echo strtoupper($table); ?>)</h2>
    <form method="POST">
        <?php
        // Show input fields (skip ID column if auto-increment)
        $columns = $conn->query("SHOW COLUMNS FROM $table");
        while ($col = $columns->fetch_assoc()) {
            if ($col['Field'] == "id") continue;
            echo "<label>" . str_replace("_", " ", $col['Field']) . "</label>";
            echo "<input type='text' name='" . $col['Field'] . "' required>";
        }
        ?>
        <button type="submit" class="btn btn-save">💾 Save</button>
        <a href="display_wichy_coconut.php" class="btn btn-cancel">✖ Cancel</a>
    </form>
</div>
</body>
</html>
