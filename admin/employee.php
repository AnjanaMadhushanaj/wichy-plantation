<?php
require_once __DIR__ . '/../config.php';

$success = $error = '';

// Handle Delete (via GET)
if (isset($_GET['delete'])) {
    $eid = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM employees WHERE Emp_ID = ?");
    $stmt->bind_param("i", $eid);
    if ($stmt->execute()) {
        // redirect with a flag so we can show a message
        header('Location: employee.php?deleted=1');
        exit;
    } else {
        $error = 'Delete failed: ' . $conn->error;
    }
}

if (isset($_GET['deleted'])) {
    $success = 'Employee deleted successfully.';
}

// Handle Add (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phones = $_POST['phones'] ?? []; // may be an array of strings

    // Server-side validation for required fields
    if (!$name || !$role || !$address) {
        $error = 'Please fill name, role and address.';
    } else {
        // Validate and normalize phone numbers
        $validPhones = [];
        foreach ($phones as $rawPhone) {
            $rawPhone = trim($rawPhone);
            if ($rawPhone === '') continue; // ignore empty inputs

            // remove spaces, dashes and other characters except leading plus
            $normalized = preg_replace('/[^\d+]/', '', $rawPhone);

            // Accept Sri Lanka formats:
            //  - Local: 0 + 9 digits  (e.g. 0771234567)
            //  - International: +94 + 9 digits (e.g. +94771234567)
            if (preg_match('/^(?:0\d{9}|\+94\d{9})$/', $normalized)) {
                $validPhones[] = $normalized;
            } else {
                $error = "Invalid phone number format: {$rawPhone}. Use 0771234567 or +94771234567.";
                break;
            }
        }

        if (empty($validPhones) && !$error) {
            $error = "Please provide at least one valid phone number.";
        }
    }

    // If no validation errors, insert employee and phones inside a transaction
    if (!$error) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO employees (Role, Name, Address) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $role, $name, $address);
            if (!$stmt->execute()) {
                throw new Exception("Insert employee failed: " . $stmt->error);
            }
            $emp_id = $stmt->insert_id;

            $phone_stmt = $conn->prepare("INSERT INTO employee_phones (Emp_ID, Phone) VALUES (?, ?)");
            foreach ($validPhones as $ph) {
                $phone_stmt->bind_param("is", $emp_id, $ph);
                if (!$phone_stmt->execute()) {
                    throw new Exception("Insert phone failed: " . $phone_stmt->error);
                }
            }

            $conn->commit();
            $success = "Employee added successfully!";
        } catch (Exception $ex) {
            $conn->rollback();
            $error = $ex->getMessage();
        }
    }
}

// Fetch employees with phones (GROUP_CONCAT)
$res = $conn->query("
    SELECT e.Emp_ID, e.Name, e.Role, e.Address,
           GROUP_CONCAT(p.Phone SEPARATOR ', ') AS Phones
    FROM employees e
    LEFT JOIN employee_phones p ON e.Emp_ID = p.Emp_ID
    GROUP BY e.Emp_ID
    ORDER BY e.Emp_ID DESC
");
$employee = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Manage Employees</title>
<link rel="stylesheet" href="../public/style.css">
<style>
.container { max-width:1000px; margin:auto; padding:20px; }
table { width:100%; border-collapse:collapse; margin-top:16px; }
th, td { border:1px solid #ccc; padding:8px; text-align:left; }
th { background:#f5f5f5; }
input[type=text] { width:100%; padding:8px; margin-bottom:8px; box-sizing:border-box; }
.btn { padding:6px 10px; text-decoration:none; border-radius:4px; border:1px solid #aaa; display:inline-block; }
.btn-primary { background:#2d7; border-color:#28a745; color:#000; }
.btn-danger { background:#f88; border-color:#e63946; color:#000; }
.notice { padding:10px; margin-bottom:12px; border-radius:4px; }
.notice.success { background:#e6ffed; border:1px solid #b7f0c6; }
.notice.error { background:#ffe6e6; border:1px solid #f0b7b7; }
</style>

<script>
// Add new phone input
function addPhoneField() {
    const div = document.createElement('div');
    div.innerHTML = '<input type="text" name="phones[]" placeholder="Phone Number (0771234567 or +94771234567)" required>';
    document.getElementById('phone-fields').appendChild(div);
}

// Validate phone numbers before submit
function validatePhones(e) {
    const inputs = document.querySelectorAll('input[name="phones[]"]');
    const re = /^(?:0\d{9}|\+94\d{9})$/;
    for (let i = 0; i < inputs.length; i++) {
        const raw = inputs[i].value.trim();
        if (!raw) {
            e.preventDefault();
            alert('Please fill all phone inputs or remove empty ones.');
            return false;
        }
        const normalized = raw.replace(/[^\d+]/g, '');
        if (!re.test(normalized)) {
            e.preventDefault();
            alert('Invalid phone: ' + raw + '\\nUse Sri Lanka format: 0771234567 or +94771234567');
            return false;
        }
        // normalize value before sending
        inputs[i].value = normalized;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('add-employee-form');
    form.addEventListener('submit', validatePhones);
});
</script>
</head>
<body>
<div class="container">
    <h1>Manage Employees</h1>

    <?php if ($success): ?>
        <div class="notice success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="notice error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Add Employee Form -->
    <h3>Add Employee</h3>
    <form method="post" id="add-employee-form" novalidate>
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Role</label>
        <input type="text" name="role" required>

        <label>Address</label>
        <input type="text" name="address" required>

        <label>Phone Numbers</label>
        <div id="phone-fields">
            <input type="text" name="phones[]" placeholder="Phone Number (0771234567 or +94771234567)" required>
        </div>
        <button type="button" onclick="addPhoneField()" class="btn">+ Add More Phones</button>
        <br><br>
        <button type="submit" name="add" class="btn btn-primary">Add Employee</button>
    </form>

    <!-- Employee Table -->
    <h3 style="margin-top:24px;">Employee List</h3>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Role</th><th>Address</th><th>Phone Numbers</th><th>Actions</th>
        </tr>
        <?php foreach ($employee as $e): ?>
        <tr>
            <td><?= (int)$e['Emp_ID'] ?></td>
            <td><?= htmlspecialchars($e['Name']) ?></td>
            <td><?= htmlspecialchars($e['Role']) ?></td>
            <td><?= htmlspecialchars($e['Address']) ?></td>
            <td><?= htmlspecialchars($e['Phones'] ?? 'N/A') ?></td>
            <td>
                <a href="employee.php?delete=<?= (int)$e['Emp_ID'] ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Delete this employee?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
