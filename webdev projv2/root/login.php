<?php
session_start();
$users_file = '../data/users.json';
$users = file_exists($users_file) ? json_decode(file_get_contents($users_file), true) : [];

$page = $_GET['page'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['do_login'])) {
        $email = trim($_POST['email']);
        $password = md5($_POST['password']);
        foreach ($users as $u) {
            if ($u['email'] === $email && $u['password'] === $password) {
                $_SESSION['user'] = ['email' => $u['email'], 'name' => $u['name'], 'username' => $u['username']];
                $_SESSION['registered_user'] = $u;
                header('Location: profile.php'); exit;
            }
        }
        $login_error = 'Invalid email or password.';
    }
    if (isset($_POST['do_register'])) {
        $name = trim($_POST['full_name']);
        $username = trim($_POST['username']);
        $phone = trim($_POST['phone']);
        $dob = trim($_POST['dob']);
        $email = trim($_POST['reg_email']);
        $password = md5($_POST['reg_password']);
        if ($name && $username && $email && $_POST['reg_password']) {
            $new_user = compact('name','username','phone','dob','email','password');
            $users[] = $new_user;
            file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
            $_SESSION['user'] = ['email' => $email, 'name' => $name, 'username' => $username];
            $_SESSION['registered_user'] = $new_user;
            header('Location: profile.php'); exit;
        }
        $reg_error = 'Please fill in all required fields.';
    }
    if (isset($_POST['do_reset'])) {
        $code = trim($_POST['code']);
        $new_password = md5($_POST['new_password']);
        if ($code === '1234' && $_POST['new_password']) {
            if (isset($_SESSION['registered_user'])) {
                foreach ($users as &$u) {
                    if ($u['email'] === $_SESSION['registered_user']['email']) {
                        $u['password'] = $new_password;
                        $_SESSION['registered_user']['password'] = $new_password;
                        break;
                    }
                }
                file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
                $reset_success = true;
            }
        } else { $reset_error = 'Invalid code. (Hint: 1234)'; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= match($page){'register'=>'Create Account','reset-password'=>'Reset Password',default=>'Sign In'} ?> — King's Cup</title><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="style.css"></head>
<body class="auth-body"><div class="auth-page"><a href="index.php" class="auth-logo"><svg width="40" height="40" viewBox="0 0 36 36"><circle cx="18" cy="18" r="18" fill="#3b1f0e"/><path d="M10 22 Q12 14 18 12 Q24 14 26 22 Q22 26 18 27 Q14 26 10 22Z" fill="#c8860a"/><circle cx="18" cy="19" r="4" fill="#3b1f0e"/></svg></a>
<?php if ($page === 'login'): ?>
<div class="auth-card"><h2 class="auth-title">Sign in or create an account</h2><?php if (!empty($login_error)) echo '<p class="auth-error">'.htmlspecialchars($login_error).'</p>'; ?><form method="POST"><input type="hidden" name="do_login" value="1"/><div class="auth-field"><input type="email" name="email" placeholder="Email" required class="auth-input"/></div><div class="auth-field"><input type="password" name="password" placeholder="Password" required class="auth-input"/></div><a href="login.php?page=reset-password" class="auth-forgot">Forgot password?</a><button type="submit" class="btn-auth">Log in</button></form><p class="auth-switch">Don't have an account? <a href="login.php?page=register">Register here</a></p></div>
<?php elseif ($page === 'register'): ?>
<div class="auth-card"><h2 class="auth-title">Set up your account!</h2><?php if (!empty($reg_error)) echo '<p class="auth-error">'.htmlspecialchars($reg_error).'</p>'; ?><form method="POST"><input type="hidden" name="do_register" value="1"/><div class="auth-field"><input type="text" name="full_name" placeholder="Full Name" required class="auth-input"/></div><div class="auth-field"><input type="text" name="username" placeholder="Username" required class="auth-input"/></div><div class="auth-field"><input type="tel" name="phone" placeholder="Phone Number" class="auth-input"/></div><div class="auth-field"><input type="date" name="dob" placeholder="Date of Birth" class="auth-input"/></div><div class="auth-field"><input type="email" name="reg_email" placeholder="Email" required class="auth-input"/></div><div class="auth-field"><input type="password" name="reg_password" placeholder="Password" required class="auth-input"/></div><button type="submit" class="btn-auth">Get Started</button></form><p class="auth-switch">Already have an account? <a href="login.php">Sign in here</a></p></div>
<?php elseif ($page === 'reset-password'): ?>
<div class="auth-card"><h2 class="auth-title">Change Password</h2><?php if (!empty($reset_success)): ?><p class="auth-success">Password updated! <a href="login.php">Sign in</a></p><?php else: ?><?php if (!empty($reset_error)) echo '<p class="auth-error">'.htmlspecialchars($reset_error).'</p>'; ?><form method="POST"><input type="hidden" name="do_reset" value="1"/><div class="auth-field auth-field-row"><input type="text" name="code" placeholder="Code" required class="auth-input"/><button type="button" class="btn-resend" onclick="alert('Code sent! (Hint: 1234)')">Resend</button></div><div class="auth-field"><input type="password" name="new_password" placeholder="New Password" required class="auth-input"/></div><button type="submit" class="btn-auth">Reset Password</button></form><?php endif; ?><p class="auth-switch"><a href="login.php">&#8592; Back to Sign in</a></p></div>
<?php endif; ?></div><script src="main.js"></script></body></html>