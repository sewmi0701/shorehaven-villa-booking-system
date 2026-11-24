<?php
require __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('my-bookings.php');
$pageTitle = 'Create an account'; $errors = [];
if (is_post()) {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (strlen($name) < 2 || strlen($name) > 100) $errors[] = 'Please enter a name between 2 and 100 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[+0-9() .-]{7,24}$/', $phone)) $errors[] = 'Enter a valid contact number.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        try {
            $stmt = db()->prepare("INSERT INTO users (name, email, contact_number, password, role) VALUES (?, ?, ?, ?, 'guest')");
            $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) db()->lastInsertId();
            flash('success', 'Your account has been created. Welcome to Shorehaven!');
            $target = $_SESSION['after_login'] ?? 'my-bookings.php';
            unset($_SESSION['after_login']);
            redirect($target);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') $errors[] = 'An account already exists with this email address.';
            else { error_log($ex->getMessage()); $errors[] = 'Unable to register right now. Please try again.'; }
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-section"><div class="auth-decor"><div class="auth-decor-inner"><span class="eyebrow light">A PLACE TO COME BACK TO</span><h2>The coast is<br><em>calling you.</em></h2><p>Create your account and make room for something memorable.</p></div></div><div class="auth-panel"><div class="auth-content"><span class="eyebrow">JOIN US AT THE COAST</span><h1>Start <em>exploring.</em></h1><p>One quick step before your stay.</p><?php display_errors($errors); ?><form method="post" class="stack-form"><?= csrf_field() ?><label>Full name<input type="text" name="name" autocomplete="name" maxlength="100" value="<?= e((string)($_POST['name'] ?? '')) ?>" required></label><label>Email address<input type="email" name="email" autocomplete="email" maxlength="190" value="<?= e((string)($_POST['email'] ?? '')) ?>" required></label><label>Contact number<input type="tel" name="phone" autocomplete="tel" maxlength="24" value="<?= e((string)($_POST['phone'] ?? '')) ?>" required></label><label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label><label>Confirm password<input type="password" name="confirm_password" autocomplete="new-password" minlength="8" required></label><button class="btn btn-primary btn-wide" type="submit">Create account ↗</button></form><p class="auth-foot">Already have an account? <a href="<?= e(url('login.php')) ?>">Log in</a></p></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
