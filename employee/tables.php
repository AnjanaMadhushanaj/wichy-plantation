<?php
$ALLOWED_TABLES = ["FatRate6", "FatRate9", "FatRate10", "FatRate12", "FatRate18", "FatRate22", "FatRate30"];

function is_allowed_table(string $table, array $list): bool {
    return in_array($table, $list, true);
}
?>
