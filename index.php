<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melo-fi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body >
    <header>
        <h1 style="margin: 1%, ">Melo-fi</h1>
    </header>

    <main>
        <div id="container" style="max-width: 80%; margin: 0 auto; ">
            <div id="mainbox">
                <h2>Login ke Akun Anda</h2><br>
                <form action="login.php" method="post" autocomplete="on" novalidate>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            required
                            aria-label="Username"
                        >
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required
                            aria-label="Password"
                        >
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="Login" class="button primary">Login</button>
                        <button type="submit" name="Register" class="button secondary">Register</button>
                    </div>
                </form>

                <?php if (isset($_GET['error'])): ?>
                    <p class="error-message" role="alert">
                        <?php
                            switch ($_GET['error']) {
                                case 'login gagal':
                                    echo "Username atau password salah!";
                                    break;
                                case 'username digunakan':
                                    echo "Username tersebut telah digunakan!";
                                    break;
                                case 'gagal register':
                                    echo "Proses register gagal!";
                                    break;
                                case 'akses ditolak':
                                    echo "Akses terlarang";
                                    break;
                                case 'belum login':
                                    echo "Silahkan login!";
                                    break;
                                default:
                                    echo "Terjadi kesalahan!";
                            }
                        ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>