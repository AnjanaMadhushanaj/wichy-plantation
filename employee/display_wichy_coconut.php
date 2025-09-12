<?php

include "db_connect.php";
include "tables.php";

echo "<link rel='stylesheet' href='../global.css'>";
echo "<link rel='stylesheet' href='style.css'>";
echo "<link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>";
echo "<style>
/* ---- ADDITIONAL EMPLOYEE SPECIFIC STYLES ----*/
/* ---- EMPLOYEE SPECIFIC STYLES ---- */
.company-header {
    text-align: center;
    padding: 15px;
    background: linear-gradient(135deg, var(--color-medium-green-text) 0%, var(--color-dark-green-text) 100%);
    color: white;
    font-size: 28px;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    margin-bottom: 10px;
    border-radius: 8px;
}

.table-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
}

th, td {
    border: 1px solid var(--color-main-green);
    padding: 12px 8px;
    text-align: center;
}

th {
    background: linear-gradient(135deg, var(--color-medium-green-text) 0%, var(--color-dark-green-text) 100%);
    color: white;
    font-weight: 600;
}

tr:nth-child(even) {
    background-color: var(--color-bg-light-card);
}

tr:hover {
    background-color: var(--color-main-green);
}
</style>";

// ===================== Company Header =====================
echo "<div class='company-header'>
    WICHY COCONUT
</div>";

// ===================== Get selected table =====================
$table = $_GET['table'] ?? 'FatRate6';
if (!is_allowed_table($table, $ALLOWED_TABLES)) $table = 'FatRate6';

// ===================== Navbar =====================
echo "<div class='navbar'>";
foreach ($ALLOWED_TABLES as $t) {
    echo "<a href='?table=$t' class='nav-btn'>$t</a>";
}
echo "<a href='../logout.php' class='logout-btn'>Logout</a>";
echo "</div>";

// ===================== Display Table =====================
$sql = "SELECT * FROM `$table`";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    $fields = $result->fetch_fields();
    echo "<tr>";
    foreach ($fields as $f) {
        $colName = $f->name;
        if ($colName == "CoconutCompany") $colName = "Coconut Company";
        elseif ($colName == "Or_Food_and_Cafe") $colName = "Or Food and Cafe";
        echo "<th>$colName</th>";
    }
    echo "<th>Actions</th>";
    echo "</tr>";

    $leftAlignCols = ['Ingredient', 'Sugar_g', 'Polysorbate_g'];
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $val) {
            $class = in_array($key, $leftAlignCols) ? 'left-align' : '';
            echo "<td class='$class'>".htmlspecialchars($val)."</td>";
        }
        $ing = urlencode($row['Ingredient']);
        echo "<td>
            <a href='row_form.php?table=$table&ingredient=$ing' class='btn-edit'>Edit</a>
            <a href='delete.php?table=$table&ingredient=$ing' class='btn-delete' onclick=\"return confirm('Delete this row?');\">Delete</a>
        </td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><a href='row_form.php?table=$table' class='add-btn'>+ Add New Row</a>";
} else {
    echo "<p>No data in $table</p>";
}

// ==================== QC Panel ====================
echo "<hr><h2>Quality Controller Panel</h2>";
echo "<form method='POST' action='qc_process.php'>";
echo "<table>
<tr><th>Check</th><th>Inspector</th><th>Status</th><th>Remarks</th><th>Date</th></tr>";

// Fat % row
echo "<tr class='fat-row'>
  <td>Fat %</td>
  <td>Mr. <input type='text' name='inspector[Fat %]' placeholder='Inspector name'></td>
  <td>
    <select name='status[Fat %]'>
      <option>Pending</option>
      <option>Pass</option>
      <option>Fail</option>
    </select>
  </td>
  <td><input type='text' name='remarks[Fat %]' placeholder='Remarks'></td>
  <td><input type='date' name='qc_date[Fat %]' value='".date("Y-m-d")."'></td>
</tr>";

// Ingredients row
echo "<tr class='ingredients-row'>
  <td>Ingredients</td>
  <td>Mr. <input type='text' name='inspector[Ingredients]' placeholder='Inspector name'></td>
  <td>
    <select name='status[Ingredients]'>
      <option>Pending</option>
      <option>Pass</option>
      <option>Fail</option>
    </select>
  </td>
  <td><input type='text' name='remarks[Ingredients]' placeholder='Remarks'></td>
  <td><input type='date' name='qc_date[Ingredients]' value='".date("Y-m-d")."'></td>
</tr>";

echo "</table><br>";
echo "<input type='hidden' name='table_name' value='$table'>";
echo "<button type='submit'>Submit QC</button> ";

// ===================== Delete All QC Button =====================
echo "<button 
    type='button'
    onclick=\"confirmDeleteAll('$table')\" 
    style='background-color:#ff4444; color:white; font-weight:bold; padding:10px 15px; border:none; border-radius:8px; cursor:pointer; box-shadow:0 0 10px red; margin-left:15px;'>
    🚨 Delete All QC (Panel)
</button>";

echo "</form>";

// ===================== JavaScript for Delete Confirmation =====================
echo "<script>
function confirmDeleteAll(tableName) {
    let confirmation = confirm(
        '⚠️ WARNING!\\n\\n' +
        'You are about to DELETE ALL QC RECORDS for ' + tableName + '.\\n\\n' +
        'This action CANNOT be undone.\\n\\nDo you want to proceed?'
    );
    if (confirmation) {
        window.location.href = 'qc_delete.php?table_name=' + encodeURIComponent(tableName) + '&all=1';
    }
}
</script>";

// ==================== Display Previous QC Records ====================
echo "<hr><h3>Previous Quality Control Records for $table</h3>";

$sql_qc = "SELECT * FROM `quality_control` WHERE `table_name` = '$table' ORDER BY qc_date DESC, id DESC";
$res_qc = $conn->query($sql_qc);

if ($res_qc && $res_qc->num_rows > 0) {
    echo "<table>
    <tr>
        <th>Check</th>
        <th>Inspector</th>
        <th>Status</th>
        <th>Remarks</th>
        <th>Date</th>
        <th>Submitted At</th>
        <th>Actions</th>
    </tr>";

    while ($row = $res_qc->fetch_assoc()) {
        $id = $row['id'];
        $class = $row['check_type'] === 'Fat %' ? 'fat-row' : 'ingredients-row';
        echo "<tr class='$class'>
            <td>".htmlspecialchars($row['check_type'])."</td>
            <td>Mr. ".htmlspecialchars($row['inspector'])."</td>
            <td>".htmlspecialchars($row['status'])."</td>
            <td>".htmlspecialchars($row['remarks'])."</td>
            <td>".htmlspecialchars($row['qc_date'])."</td>
            <td>".htmlspecialchars($row['created_at'])."</td>
            <td>
                <a href='qc_delete.php?id=$id' class='btn-delete' onclick=\"return confirm('Delete this QC record?');\">Delete</a>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No Quality Control records found for $table.</p>";
}

$conn->close();
?>