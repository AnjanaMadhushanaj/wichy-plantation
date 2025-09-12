<?php
include "db_connect.php";
include "tables.php";

$table = $_GET['table'] ?? '';
$ingredient = $_GET['ingredient'] ?? '';

if (!is_allowed_table($table, $ALLOWED_TABLES)) die("Invalid table.");
if ($ingredient === '') die("Missing ingredient.");

$ingredientEsc = $conn->real_escape_string($ingredient);
$sql = "DELETE FROM `$table` WHERE `Ingredient` = '$ingredientEsc'";

if ($conn->query($sql)) {
    echo "Row deleted successfully! <a href='display_wichy_coconut.php'>Back</a>";
} else {
    echo "Error: " . $conn->error . " | <a href='display_wichy_coconut.php'>Back</a>";
}
?>
