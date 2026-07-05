<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
$error = '';
$success = '';

// ---- handle user actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username)) {
        $error = "Username is required.";
    } else {
        if (!empty($_POST['id'])) {
            // Edit User
            $id = (int)$_POST['id'];
            
            // Check unique username
            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username=? AND id!=?");
            $stmt->execute([$username, $id]);
            if ($stmt->fetch()) {
                $error = "Username already taken.";
            } else {
                if (!empty($password)) {
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE admin_users SET username=?, password=? WHERE id=?");
                    $stmt->execute([$username, $hashed, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE admin_users SET username=? WHERE id=?");
                    $stmt->execute([$username, $id]);
                }
                $success = "User updated successfully.";
            }
        } else {
            // Add User
            if (empty($password)) {
                $error = "Password is required for new users.";
            } else {
                // Check unique username
                $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username=?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $error = "Username already exists.";
                } else {
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
                    $stmt->execute([$username, $hashed]);
                    $success = "New user added successfully.";
                }
            }
        }
    }
}

// ---- handle user delete ----
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if ($del_id === (int)$_SESSION['admin_id']) {
        $error = "You cannot delete your own logged-in account!";
    } else {
        $pdo->prepare("DELETE FROM admin_users WHERE id=?")->execute([$del_id]);
        $success = "User deleted successfully.";
    }
}

$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT id, username FROM admin_users WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}

$users = $pdo->query("SELECT id, username, created_at FROM admin_users ORDER BY username ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin User Manager - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h1>Admin Users Manager</h1></div>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;"><?= $editUser ? 'Edit Admin User' : 'Add New Admin User' ?></h3>
      
      <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
      
      <form method="POST">
        <?php if ($editUser): ?><input type="hidden" name="id" value="<?= (int)$editUser['id'] ?>"><?php endif; ?>
        
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" required value="<?= e($editUser['username'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Password <?= $editUser ? '(Leave blank to keep current password)' : '' ?></label>
          <input type="password" name="password" <?= $editUser ? '' : 'required' ?>>
        </div>

        <button type="submit" class="btn"><?= $editUser ? 'Update User' : 'Add User' ?></button>
        <?php if ($editUser): ?><a href="users.php" class="btn" style="background:#9ca3af;">Cancel</a><?php endif; ?>
      </form>
    </div>

    <div class="admin-card">
      <h3 style="margin-bottom:16px;">Existing Admin Users</h3>
      <table class="admin-table">
        <tr><th>ID</th><th>Username</th><th>Created At</th><th>Actions</th></tr>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><strong><?= e($u['username']) ?></strong> <?php if ($u['id'] == $_SESSION['admin_id']): ?><span style="font-size:0.8rem; color:var(--color-primary); font-weight:normal;">(You)</span><?php endif; ?></td>
            <td><?= e($u['created_at']) ?></td>
            <td class="action-links">
              <a href="?edit=<?= (int)$u['id'] ?>">Edit / Change PW</a>
              <?php if ($u['id'] != $_SESSION['admin_id']): ?>
                <a href="?delete=<?= (int)$u['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this admin user?')">Delete</a>
              <?php else: ?>
                <span style="color:#d1d5db; cursor:not-allowed;">Delete</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

  </div>
</div>
</body>
</html>
