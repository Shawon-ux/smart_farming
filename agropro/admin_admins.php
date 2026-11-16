<?php
require_once __DIR__ . '/admin_layout.php';
$me = $_SESSION['admin_username'];
$msg = ''; $err = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_admin'])) {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $pw = $_POST['password'] ?? ''; $pw2 = $_POST['password2'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $err = true; $msg = 'Enter a valid email address.'; }
        elseif (strlen($pw) < 8) { $err = true; $msg = 'Password must be at least 8 characters.'; }
        elseif ($pw !== $pw2) { $err = true; $msg = 'Passwords do not match.'; }
        else {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $s = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            $s->bind_param("ss", $email, $hash);
            if ($s->execute()) $msg = "New admin $email added.";
            else { $err = true; $msg = $conn->errno == 1062 ? 'An admin with this email already exists.' : 'Could not add admin.'; }
        }
    } elseif (isset($_POST['change_pw'])) {
        $u = $_POST['username'] ?? ''; $pw = $_POST['password'] ?? '';
        if (strlen($pw) < 8) { $err = true; $msg = 'Password must be at least 8 characters.'; }
        else {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $s = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
            $s->bind_param("ss", $hash, $u);
            $s->execute();
            $msg = "Password updated for $u.";
        }
    } elseif (isset($_POST['delete_admin'])) {
        $u = $_POST['username'] ?? '';
        if ($u === $me) { $err = true; $msg = "You can't delete the account you're logged in with."; }
        elseif (admin_count("SELECT COUNT(*) FROM admin") <= 1) { $err = true; $msg = 'At least one admin must remain.'; }
        else {
            $s = $conn->prepare("DELETE FROM admin WHERE username = ?");
            $s->bind_param("s", $u);
            $s->execute();
            $msg = "Admin $u removed.";
        }
    }
}
$admins = $conn->query("SELECT username, password FROM admin ORDER BY username")->fetch_all(MYSQLI_ASSOC);
admin_header('Admins', 'admins');
?>
<?php if ($msg): ?><div class="alert-a <?= $err ? 'err' : '' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-h"><h2>Add a new admin</h2></div>
      <form method="POST" style="padding:22px">
        <label class="mb-1"><strong>Email (login name)</strong></label>
        <input class="fi" type="email" name="email" placeholder="name@gmail.com" required>
        <label class="mb-1"><strong>Password</strong></label>
        <input class="fi" type="password" name="password" minlength="8" placeholder="At least 8 characters" required>
        <label class="mb-1"><strong>Confirm password</strong></label>
        <input class="fi" type="password" name="password2" minlength="8" required>
        <button class="abtn" name="add_admin"><i class="bi bi-person-plus"></i> Add admin</button>
        <p class="text-muted mt-3 mb-0" style="font-size:13px">The new admin logs in at the normal login page using this email and password.</p>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-h"><h2>Admin accounts (<?= count($admins) ?>)</h2></div>
      <div class="tbl-wrap"><table class="tbl">
        <thead><tr><th>Email / username</th><th>Security</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($admins as $a): $hashed = str_starts_with($a['password'], '$2y$'); ?>
          <tr>
            <td><strong><?= htmlspecialchars($a['username']) ?></strong> <?php if ($a['username'] === $me): ?><span class="pill p-ok">You</span><?php endif; ?></td>
            <td><?php if ($hashed): ?><span class="pill p-ok">Secure</span><?php else: ?><span class="pill p-bad" title="Old plain-text password. It cannot log in; delete it or set a new password.">Legacy</span><?php endif; ?></td>
            <td class="text-nowrap">
              <form method="POST" class="inl"><input type="hidden" name="username" value="<?= htmlspecialchars($a['username']) ?>">
                <input type="password" name="password" minlength="8" placeholder="New password" class="search" style="min-width:140px;padding:6px 10px" required>
                <button class="abtn sm ghost" name="change_pw">Set</button></form>
              <?php if ($a['username'] !== $me): ?>
              <form method="POST" class="inl" onsubmit="return confirm('Remove this admin?')"><input type="hidden" name="username" value="<?= htmlspecialchars($a['username']) ?>"><button class="abtn sm red" name="delete_admin"><i class="bi bi-trash"></i></button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div>
<?php admin_footer(); ?>
