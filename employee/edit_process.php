<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Process - Wichy Coconut</title>
    <link rel="stylesheet" href="../global.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php
        include "db_connect.php";
        include "tables.php";

        $table = $_POST['table'] ?? '';
        $ingredientOriginal = $_POST['ingredient'] ?? '';
        $data = $_POST['data'] ?? [];

        if (!is_allowed_table($table, $ALLOWED_TABLES)) {
            echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>❌ Invalid Table</h2>
                    <p>The specified table is not allowed.</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
            exit;
        }

        if (empty($ingredientOriginal) || empty($data)) {
            echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>❌ Missing Data</h2>
                    <p>Please provide all required information.</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
            exit;
        }

        $updates = [];
        foreach ($data as $col => $val) {
            $updates[] = "`" . $conn->real_escape_string($col) . "` = '" . $conn->real_escape_string($val) . "'";
        }
        $ingredientEsc = $conn->real_escape_string($ingredientOriginal);

        $sql = "UPDATE `$table` SET " . implode(", ", $updates) . " WHERE `Ingredient` = '$ingredientEsc'";

        if ($conn->query($sql)) {
            echo "<div style='background: #d1fae5; color: #065f46; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>✅ Success!</h2>
                    <p>Row updated successfully in $table table.</p>
                    <p><strong>Updated ingredient:</strong> " . htmlspecialchars($ingredientOriginal) . "</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
        } else {
            echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>❌ Database Error</h2>
                    <p>Error: " . htmlspecialchars($conn->error) . "</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
        }
        ?>
    </div>
</body>
</html>
