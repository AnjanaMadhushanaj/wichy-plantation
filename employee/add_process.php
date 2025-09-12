<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Process - Wichy Coconut</title>
    <link rel="stylesheet" href="../global.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php
        include "db_connect.php";
        include "tables.php";

        $table = $_POST['table'] ?? '';
        if (!is_allowed_table($table, $ALLOWED_TABLES)) {
            echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>❌ Invalid Table</h2>
                    <p>The specified table is not allowed.</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
            exit;
        }

        $data = $_POST['data'] ?? [];
        if (empty($data)) {
            echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>❌ No Data Provided</h2>
                    <p>Please provide data to add.</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
            exit;
        }

        $cols = [];
        $vals = [];
        foreach ($data as $col => $val) {
            // column list from SHOW COLUMNS, safe to backtick
            $cols[] = "`" . $conn->real_escape_string($col) . "`";
            $vals[] = "'" . $conn->real_escape_string($val) . "'";
        }

        $sql = "INSERT INTO `$table` (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";

        if ($conn->query($sql)) {
            echo "<div style='background: #d1fae5; color: #065f46; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>✅ Success!</h2>
                    <p>Row added successfully to $table table.</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
        } else {
            echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; text-align: center;'>
                    <h2>❌ Database Error</h2>
                    <p>Error: " . $conn->error . "</p>
                    <a href='display_wichy_coconut.php' class='btn btn-primary'>← Back to Dashboard</a>
                  </div>";
        }
        ?>
    </div>
</body>
</html>
