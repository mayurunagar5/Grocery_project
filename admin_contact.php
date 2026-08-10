<?php
require_once 'config/database.php';

// Check session: allow admins; fallback allow logged-in users; otherwise send to login
if (!isAdminLoggedIn()) {
    if (!isUserLoggedIn()) {
        redirect('login.php');
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $submission_id = (int)$_POST['submission_id'];
        $new_status = $_POST['status'];
        
        try {
            $sql = "UPDATE contact_submissions SET status = ? WHERE id = ?";
            executeQuery($sql, [$new_status, $submission_id]);
            $success_message = "Status updated successfully!";
        } catch (Exception $e) {
            $error_message = "Error updating status: " . $e->getMessage();
        }
    }
    }
}

// Get contact submissions
try {
    $sql = "SELECT * FROM contact_submissions ORDER BY created_at DESC";
    $submissions = fetchAll($sql);
} catch (Exception $e) {
    $error_message = "Error fetching submissions: " . $e->getMessage();
    $submissions = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submissions - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2fb36d;
            --primary-dark: #249458;
            --text: #1f2937;
            --muted: #6b7280;
            --bg: #f7f7f7;
            --card: #ffffff;
            --border: #e5e7eb;
            --shadow-color: 24 39 75;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: var(--text); background: var(--bg); }
        
        .container { width: min(1200px, 100% - 2rem); margin: 0 auto; padding: 2rem 0; }
        
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
        .header h1 { margin: 0; color: var(--primary); }
        .back-btn { background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
        
        .submission { border-bottom: 1px solid var(--border); padding: 1.5rem; }
        .submission:last-child { border-bottom: none; }
        
        .submission-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .submission-info h3 { margin: 0 0 0.5rem 0; color: var(--text); }
        .submission-meta { color: var(--muted); font-size: 0.9rem; }
        
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .status-new { background: #dbeafe; color: #1e40af; }
        .status-read { background: #fef3c7; color: #92400e; }
        .status-replied { background: #d1fae5; color: #065f46; }
        
        .submission-content { margin-bottom: 1rem; }
        .submission-content p { margin: 0.5rem 0; }
        .submission-content strong { color: var(--text); }
        
        .submission-actions { display: flex; gap: 0.5rem; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.9rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: var(--border); color: var(--text); }
        
        .message-box { background: #f8f9fa; border: 1px solid var(--border); border-radius: 8px; padding: 1rem; margin-top: 1rem; }
        
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .empty-state { text-align: center; padding: 3rem; color: var(--muted); }
        
        @media (max-width: 768px) {
            .submission-header { flex-direction: column; gap: 1rem; }
            .submission-actions { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Contact Form Submissions</h1>
            <a href="admin.php" class="back-btn">← Back to Admin</a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="card">
            <?php if (empty($submissions)): ?>
                <div class="empty-state">
                    <h3>No submissions yet</h3>
                    <p>Contact form submissions will appear here once customers start using the contact form.</p>
                </div>
            <?php else: ?>
                <?php foreach ($submissions as $submission): ?>
                    <div class="submission">
                        <div class="submission-header">
                            <div class="submission-info">
                                <h3><?php echo htmlspecialchars($submission['name']); ?></h3>
                                <div class="submission-meta">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($submission['email']); ?>
                                    <?php if (!empty($submission['phone'])): ?>
                                        | <strong>Phone:</strong> <?php echo htmlspecialchars($submission['phone']); ?>
                                    <?php endif; ?>
                                    | <strong>Topic:</strong> <?php echo htmlspecialchars($submission['topic']); ?>
                                    | <strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($submission['created_at'])); ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo $submission['status']; ?>">
                                <?php echo ucfirst($submission['status']); ?>
                            </span>
                        </div>

                        <div class="submission-content">
                            <div class="message-box">
                                <strong>Message:</strong><br>
                                <?php echo nl2br(htmlspecialchars($submission['message'])); ?>
                            </div>
                        </div>

                        <!-- <div class="submission-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                <select name="status" onchange="this.form.submit()" class="btn btn-secondary">
                                    <option value="new" <?php echo $submission['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="read" <?php echo $submission['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                    <option value="replied" <?php echo $submission['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                </select>
                            </form>
                            <a href="mailto:<?php echo htmlspecialchars($submission['email']); ?>?subject=Re: <?php echo htmlspecialchars($submission['topic']); ?>" class="btn btn-primary">Reply via Email</a>
                        </div> -->
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<script>
  function toggleReply(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
  }
</script>
