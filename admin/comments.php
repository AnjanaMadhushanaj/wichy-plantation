<?php
// admin/comments.php - Admin page to view customer comments
require_once '../config.php';

// Fetch all comments
$sql = "SELECT id, user_name, comment, created_at FROM comments ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Customer Comments</title>
    <link rel="stylesheet" href="../global.css">
    <style>
        body { background: #f4f6f8; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 12px #0001; padding: 32px; }
        h1 { color: #2d3a4b; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 10px; border-bottom: 1px solid #e0e6ed; }
        th { background: #f0f4f8; color: #2d3a4b; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        .user { font-weight: 500; color: #1a73e8; }
        .comment { color: #333; }
        .date { color: #888; font-size: 0.97em; }
        .no-comments { text-align: center; color: #888; padding: 40px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Customer Comments</h1>
        <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="user"><?= htmlspecialchars($row['user_name']) ?></td>
                    <td class="comment"><?= nl2br(htmlspecialchars($row['comment'])) ?></td>
                    <td class="date"><?= $row['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="no-comments">No comments found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if ($result) $result->free();
$conn->close();
?>
