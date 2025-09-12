<?php
include "db_connect.php";

$table = $_POST['table_name'] ?? '';
$inspectors = $_POST['inspector'] ?? [];
$statuses = $_POST['status'] ?? [];
$remarksArr = $_POST['remarks'] ?? [];
$qc_dates = $_POST['qc_date'] ?? [];

if (!$table || empty($inspectors)) {
    die("Missing data.");
}

foreach ($inspectors as $check_type => $inspector) {
    $status = $statuses[$check_type] ?? '';
    $remarks = $remarksArr[$check_type] ?? '';
    $qc_date = $qc_dates[$check_type] ?? date("Y-m-d");

    $inspectorEsc = $conn->real_escape_string($inspector);
    $statusEsc = $conn->real_escape_string($status);
    $remarksEsc = $conn->real_escape_string($remarks);
    $qc_dateEsc = $conn->real_escape_string($qc_date);
    $tableEsc = $conn->real_escape_string($table);
    $check_typeEsc = $conn->real_escape_string($check_type);

    // Check if a record already exists for this table, check type, and date
    $check_sql = "SELECT id FROM `quality_control` 
                  WHERE table_name='$tableEsc' AND check_type='$check_typeEsc' AND qc_date='$qc_dateEsc' LIMIT 1";
    $res_check = $conn->query($check_sql);

    if ($res_check && $res_check->num_rows > 0) {
        // Update existing record
        $row = $res_check->fetch_assoc();
        $id = $row['id'];
        $sql = "UPDATE `quality_control` SET 
                    inspector='$inspectorEsc', 
                    status='$statusEsc', 
                    remarks='$remarksEsc',
                    created_at=CURRENT_TIMESTAMP
                WHERE id=$id";
    } else {
        // Insert new record
        $sql = "INSERT INTO `quality_control` 
                (`table_name`, `check_type`, `inspector`, `status`, `remarks`, `qc_date`) 
                VALUES ('$tableEsc', '$check_typeEsc', '$inspectorEsc', '$statusEsc', '$remarksEsc', '$qc_dateEsc')";
    }

    $conn->query($sql);
}

echo "Quality Control data saved successfully! <a href='display_wichy_coconut.php'>Back</a>";
?>