<?php
include "db_connect.php";
include "tables.php";

$table = $_GET['table'] ?? '';
$ingredient = $_GET['ingredient'] ?? '';

if (!is_allowed_table($table, $ALLOWED_TABLES)) die("Invalid table.");

// Fetch existing row if editing
$row = [];
if ($ingredient) {
    $ingredientEsc = $conn->real_escape_string($ingredient);
    $res = $conn->query("SELECT * FROM `$table` WHERE Ingredient='$ingredientEsc'");
    if ($res && $res->num_rows > 0) $row = $res->fetch_assoc();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'] ?? [];
    if ($ingredient) {
        // Update
        $updates = [];
        foreach ($data as $col => $val) {
            $updates[] = "`" . $conn->real_escape_string($col) . "` = '" . $conn->real_escape_string($val) . "'";
        }
        $ingredientEsc = $conn->real_escape_string($ingredient);
        $sql = "UPDATE `$table` SET " . implode(", ", $updates) . " WHERE Ingredient='$ingredientEsc'";
    } else {
        // Insert
        $cols = array_keys($data);
        $vals = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $data);
        $sql = "INSERT INTO `$table` (`" . implode("`,`",$cols) . "`) VALUES (" . implode(",",$vals) . ")";
    }

    if ($conn->query($sql)) {
        $msg = $ingredient ? "Row updated successfully!" : "New row added successfully!";
        header("Location: display_wichy_coconut.php?table=$table&msg=" . urlencode($msg));
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
<title><?php echo $ingredient ? "Edit" : "Add"; ?> Record - Wichy Coconut</title>
<link rel="stylesheet" href="../global.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ---- FORM SPECIFIC STYLES ---- */
.form-container {
    max-width: 600px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    max-width: 500px;
    margin: 0 auto;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px;
}

h2 {
    text-align: center;
    color: var(--color-dark-green-text);
    margin-bottom: 20px;
}

label {
    display: block;
    margin-top: 16px;
    font-weight: 600;
    color: var(--color-dark-green-text);
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

input {
    width: 100%;
    padding: 12px 16px;
    margin-top: 8px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

input:focus {
    outline: none;
    border-color: var(--color-medium-green-text);
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.btn-save {
    background: linear-gradient(135deg, var(--color-medium-green-text) 0%, var(--color-dark-green-text) 100%);
    color: white;
    margin-right: 10px;
}

.btn-cancel {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.btn-save:hover, .btn-cancel:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
</style>
</head>
<body>
<div class="card">
    <h2><?php echo $ingredient ? "✏️ Edit" : "➕ Add New"; ?> Record (<?php echo strtoupper($table); ?>)</h2>
    <form method="POST">
        <?php
        $columns = $conn->query("SHOW COLUMNS FROM `$table`");
        while ($col = $columns->fetch_assoc()) {
            $field = $col['Field'];
            if ($field == "id") continue;
            $value = $row[$field] ?? '';
            echo "<label>" . str_replace("_"," ",$field) . "</label>";
            echo "<input type='text' name='data[$field]' value='" . htmlspecialchars($value) . "' required>";
        }
        ?>
        <button type="submit" class="btn btn-save">💾 Save</button>
        <a href="display_wichy_coconut.php?table=<?php echo $table; ?>" class="btn btn-cancel">✖ Cancel</a>
    </form>
</div>
</body>
</html>
