<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth.php");
    global $title_header;
    $lang = detectLanguage();

    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $success = false;
    $error = '';

    if (!empty($token)) {
        global $localhost_db, $username_db, $password_db, $database_savmrl;
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
        if (!$c->connect_error) {
            $c->set_charset("utf8mb4");

            $stmt = $c->prepare("SELECT `user_id` FROM `otp_codes_savmrl` WHERE `temp_token` = ? AND `purpose` = 'email_verification' AND `used` = 0 AND `expires_at` > NOW()");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $uid = $row['user_id'];

                $upd = $c->prepare("UPDATE `users_savmrl` SET `email_verified` = 1 WHERE `id` = ?");
                $upd->bind_param("i", $uid);
                $upd->execute();
                $upd->close();

                $mark = $c->prepare("UPDATE `otp_codes_savmrl` SET `used` = 1 WHERE `temp_token` = ?");
                $mark->bind_param("s", $token);
                $mark->execute();
                $mark->close();

                $success = true;
            } else {
                $error = 'Invalid or expired verification link.';
            }
            $stmt->close();
            $c->close();
        } else {
            $error = 'Service temporarily unavailable.';
        }
    } else {
        $error = 'No verification token provided.';
    }
    ?>
    <title>Email Verification — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main class="main-centered">
    <div class="horizontal-center">
        <div class="auth-form-container">
            <?php if ($success): ?>
                <h2 class="title-section brilors">Email verified!</h2>
                <p class="text-align-center">Your email has been verified successfully.</p>
                <p class="text-align-center"><a href="/alpha/account/login/" class="button-link" style="display:inline-block;margin-top:16px;text-decoration:none;"><?php echo t('login', $lang); ?></a></p>
            <?php else: ?>
                <h2 class="title-section brilors">Verification failed</h2>
                <p class="text-align-center error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>
</body>
</html>
