<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $adminUser = 'admin';
    $adminPass = 'password123';

    if ($username === $adminUser && $password === $adminPass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $message = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id" >
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 15px;
        }
        .login-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 40px 35px;
            max-width: 400px;
            width: 100%;
            animation: fadeInUp 0.7s ease forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        h3 {
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
        }
        .input-group-text {
            background-color: #0d6efd;
            border: none;
            color: white;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
            font-weight: 700;
            padding: 12px;
            transition: background 0.3s ease;
            border-radius: 0.5rem;
        }
        .btn-primary:hover {
            background: #0b5ed7;
        }
        .alert {
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="login-card shadow-sm">
        <h3>Admin Login</h3>

        <?php if ($message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text" id="user-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z"/>
                          <path fill-rule="evenodd" d="M8 8a3 3 0 100-6 3 3 0 000 6z"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control"
                        aria-describedby="user-addon"
                        required
                        autofocus
                        placeholder="Masukkan username"
                    />
                </div>
                <div class="invalid-feedback">Username wajib diisi.</div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text" id="pass-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                          <path d="M2 8a2 2 0 012-2h8a2 2 0 012 2v5a2 2 0 01-2 2H4a2 2 0 01-2-2V8z"/>
                          <path d="M8 1a3 3 0 00-3 3v2h6V4a3 3 0 00-3-3z"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        aria-describedby="pass-addon"
                        required
                        placeholder="Masukkan password"
                    />
                </div>
                <div class="invalid-feedback">Password wajib diisi.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap 5 form validation
        (() => {
            'use strict';
            const forms = document.querySelectorAll('form');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', e => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
