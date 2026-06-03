<?php



declare(strict_types=1);



require_once dirname(__DIR__) . '/includes/bootstrap.php';



if (is_logged_in()) {

    redirect(base_url('admin/index.php'));

}



$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');

    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $error = 'Please enter username and password.';

    } elseif (attempt_login($username, $password)) {

        redirect(base_url('admin/index.php'));

    } else {

        $error = 'Invalid credentials.';

    }

}

$siteName = Settings::get('restaurant_name', 'Hargeisa Village Restaurant');

?>

<!DOCTYPE html>

<html lang="en" class="h-full">

<head>
    <script>
    (function(){try{var t=localStorage.getItem('hv-theme');if(t==='dark'){document.documentElement.classList.add('dark');document.documentElement.style.colorScheme='dark';}}catch(e){}})();
    </script>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#451a03">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | <?= e($siteName) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>

        tailwind.config = {
            darkMode: 'class',
            theme: {

                extend: {

                    colors: {

                        brand: { 900: '#451a03' },

                        accent: { DEFAULT: '#e85d04', dark: '#dc2f02' },

                    },

                    fontFamily: {

                        display: ['"Playfair Display"', 'Georgia', 'serif'],

                        sans: ['"DM Sans"', 'system-ui', 'sans-serif'],

                    },

                },

            },

        };

    </script>

    <link rel="stylesheet" href="<?= asset_url('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/admin-dark-mode.css') ?>">
    <?= port_fix_script_tag() ?>
</head>

<body class="admin-login flex min-h-full items-center justify-center bg-gradient-to-br from-brand-900 via-stone-800 to-stone-950 p-6 font-sans antialiased">

    <div class="admin-login-card w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl shadow-black/40 relative">
        <div class="absolute top-4 right-4">
            <?php require __DIR__ . '/includes/theme-toggle.php'; ?>
        </div>

        <div class="mb-8 text-center">

            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-accent font-display text-xl font-bold text-white shadow-lg shadow-accent/30">HV</div>

            <h1 class="font-display text-2xl font-bold text-brand-900">Admin Login</h1>

            <p class="mt-1 text-sm text-stone-500"><?= e($siteName) ?></p>

        </div>

        <?php if ($error): ?>

        <div class="admin-alert--error mb-5" role="alert"><?= e($error) ?></div>

        <?php endif; ?>

        <form method="post" class="space-y-4">

            <div>

                <label class="mb-1 block text-sm font-medium text-stone-700">Username</label>

                <input type="text" name="username" required autofocus autocomplete="username"

                       class="w-full rounded-xl border border-stone-300 px-4 py-2.5 outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20">

            </div>

            <div>

                <label class="mb-1 block text-sm font-medium text-stone-700">Password</label>

                <input type="password" name="password" required autocomplete="current-password"

                       class="w-full rounded-xl border border-stone-300 px-4 py-2.5 outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20">

            </div>

            <button type="submit" class="admin-btn admin-btn--primary w-full py-3">Sign In</button>

        </form>

        <p class="mt-6 text-center text-xs text-stone-500">

            <a href="<?= e(home_url()) ?>" class="font-medium text-accent hover:underline">&larr; Back to website</a>

        </p>

    </div>

<script src="<?= asset_url('js/admin-theme.js') ?>"></script>
</body>

</html>

