<?php
// admin/contact_messages.php - Admin page to view contact form submissions
require_once '../config.php';

$sql = "SELECT id, name, email, subject, message, created_at FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contact Messages</title>
    <link rel="stylesheet" href="../global.css">
    <style>
        body { background: #f4f6f8; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { max-width: 1100px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 12px #0001; padding: 32px; }
        h1 { color: #2d3a4b; margin-bottom: 24px; }
    .back-btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:8px; border:1px solid #e0e6ed; background:#f7fafc; color:#2d3a4b; text-decoration:none; font-weight:600; box-shadow:0 1px 4px rgba(0,0,0,0.05); }
    .back-btn:hover { background:#eef4fb; }
    .header-row { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; }
    .dash-link { color:#1a73e8; text-decoration:none; font-weight:600; }
    .dash-link:hover { text-decoration:underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 10px; border-bottom: 1px solid #e0e6ed; }
        th { background: #f0f4f8; color: #2d3a4b; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        .name { font-weight: 500; color: #1a73e8; }
        .email { color: #388e3c; }
        .subject { color: #333; font-style: italic; }
        .message { color: #333; }
        .date { color: #888; font-size: 0.97em; }
        .no-messages { text-align: center; color: #888; padding: 40px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-row">
            <a href="#" class="back-btn" onclick="if (history.length > 1) { history.back(); } else { window.location.href='admin.php'; } return false;" aria-label="Go back">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Back
            </a>
            <a class="dash-link" href="admin.php">Go to Dashboard</a>
        </div>
        <h1>Contact Form Submissions</h1>
        <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="name"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="email"><?= htmlspecialchars($row['email']) ?></td>
                    <td class="subject"><?= htmlspecialchars($row['subject']) ?></td>
                    <td class="message"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                    <td class="date"><?= $row['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="no-messages">No contact messages found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if ($result) $result->free();
$conn->close();
?>
