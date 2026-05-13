<?php

session_start();

require_once '../inc/DB.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {

        $error = 'همه فیلدها الزامی هستند';

    } else {

        $db = new DB();

        if (is_numeric($login)) {

            $user = $db
                ->select('*')
                ->from('users')
                ->where('mobile', '=', $login)
                ->first();

        } else {

            $user = $db
                ->select('*')
                ->from('users')
                ->where('username', '=', $login)
                ->first();
        }
        if (!$user) {

            $error = 'کاربر یافت نشد';

        } elseif ($user['status'] !== 'active') {

            $error = 'حساب کاربری غیرفعال است';

        } elseif (!password_verify($password, $user['password'])) {

            $error = 'رمز عبور اشتباه است';

        } else {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['username'] = $user['username'];

            $token = bin2hex(random_bytes(32));

            $db->insert('sessions', [

                'user_id' => $user['user_id'],

                'token' => $token,

                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,

                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,

                'last_activity' => date('Y-m-d H:i:s'),

                'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),

                'is_valid' => 1,

                'created_at' => date('Y-m-d H:i:s')

            ]);

            $_SESSION['token'] = $token;


            header('Location: ../index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>پنل مدیریت | صفحه ورود</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/plugins/iCheck/square/blue.css">

    <link rel="stylesheet" href="../assets/dist/css/bootstrap-rtl.min.css">
    <link rel="stylesheet" href="../assets/dist/css/custom-style.css">
</head>

<body class="hold-transition login-page">

<div class="login-box">

    <div class="login-logo">
        <a href="#"><b>ورود به سامانه</b></a>
    </div>

    <div class="card">

        <div class="card-body login-card-body">

            <p class="login-box-msg">
                فرم زیر را تکمیل کنید و ورود بزنید
            </p>

            <?php if ($error): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <form method="post">

                <div class="input-group mb-3">

                    <input
                            type="text"
                            name="login"
                            class="form-control"
                            placeholder="موبایل یا نام کاربری"
                            required
                    >

                    <div class="input-group-append">
                        <span class="fa fa-user input-group-text"></span>
                    </div>

                </div>

                <div class="input-group mb-3">

                    <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="رمز عبور"
                            required
                    >

                    <div class="input-group-append">
                        <span class="fa fa-lock input-group-text"></span>
                    </div>

                </div>

                <div class="row">

                    <div class="col-8"></div>

                    <div class="col-4">

                        <button
                                type="submit"
                                class="btn btn-primary btn-block btn-flat"
                        >
                            ورود
                        </button>

                    </div>

                </div>

            </form>

            <p class="mb-1">
                <a href="#">رمز عبورم را فراموش کرده ام.</a>
            </p>

        </div>

    </div>

</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>

<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="../assets/plugins/iCheck/icheck.min.js"></script>

<script>
    $(function () {

        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });

    });
</script>

</body>
</html>
