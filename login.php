<?php
require __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect(current_user()['role'] === 'admin' ? 'admin/index.php' : 'my-bookings.php');
$pageTitle = 'Welcome back'; $errors = [];
if (is_post()) {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT user_id, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]); $record = $stmt->fetch();
    if ($record && password_verify($password, $record['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$record['user_id'];
        $target = $_SESSION['after_login'] ?? ($record['role'] === 'admin' ? 'admin/index.php' : 'my-bookings.php');
        unset($_SESSION['after_login']);
        flash('success', 'Welcome back! You are now logged in.');
        redirect($target);
    }
    $errors[] = 'Invalid email address or password.';
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-section"><div class="auth-decor"><div class="auth-decor-inner"><span class="eyebrow light">THE SHOREHAVEN CLUB</span><h2>Your next chapter<br><em>starts at the shore.</em></h2><p>Come back to the coast, just as you are.</p></div></div><div class="auth-panel"><div class="auth-content"><span class="eyebrow">HELLO AGAIN</span><h1>Welcome <em>back.</em></h1><p>Sign in to manage your coastal escape.</p><?php display_errors($errors); ?><form method="post" class="stack-form"><?= csrf_field() ?><label>Email address<input type="email" name="email" autocomplete="email" value="<?= e((string)($_POST['email'] ?? '')) ?>" required></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button type="submit" class="btn btn-primary btn-wide">Sign in ↗</button></form><p class="auth-foot">New to Shorehaven? <a href="<?= e(url('register.php')) ?>">Create an account</a></p></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
