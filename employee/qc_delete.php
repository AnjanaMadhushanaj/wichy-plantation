<?php
include "db_connect.php";

// Delete all QC entries for a table
if (isset($_GET['all']) && $_GET['all'] == 1 && isset($_GET['table_name'])) {
    $table = $conn->real_escape_string($_GET['table_name']);
    $sql = "DELETE FROM quality_control WHERE table_name='$table'";
    if ($conn->query($sql)) {
        echo "All QC records for $table deleted successfully! <a href='display_wichy_coconut.php?table=$table'>Back</a>";
    } else {
        echo "Error deleting QC records: " . $conn->error . " | <a href='display_wichy_coconut.php?table=$table'>Back</a>";
    }
}

// Delete a single QC entry
elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM quality_control WHERE id=$id";
    if ($conn->query($sql)) {
        echo "QC record deleted successfully! <a href='javascript:history.back()'>Back</a>";
    } else {
        echo "Error deleting QC record: " . $conn->error . " | <a href='javascript:history.back()'>Back</a>";
    }
}

// No valid parameters passed
else {
    echo "No action specified. <a href='javascript:history.back()'>Back</a>";
}

$conn->close();
?>
