<!-- C:\xampp\htdocs\payment-gateway\app\Views\auth\login.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pembayaran Ponpes</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .bg-pattern {
            background-image: url('https://images.unsplash.com/photo-1558283478-43993514c33e?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-200">

    <div class="bg-pattern min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center p-4" style="background-color: #0F4258;">

            <div class="w-full max-w-md">
                <!-- Logo dan Judul -->
                <div class="text-center mb-8">
                    <img src="<?= base_url('pondok_kecil_logo.png') ?>" alt="Logo Ponpes" class="w-20 h-20 mx-auto mb-4 bg-white p-2 rounded-full shadow-md">
                    <h1 class="text-3xl font-bold text-white">Sistem Pembayaran Ponpes</h1>
                    <p class="text-gray-300 mt-2">Silakan login untuk melanjutkan</p>
                </div>

                <!-- Form Container -->
                <div class="bg-white p-8 rounded-2xl shadow-xl">
                    
                    <form method="post" action="<?= site_url('login') ?>">
                        <?= csrf_field() ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                                <p class="font-bold">Login Gagal</p>
                                <p><?= session()->getFlashdata('error') ?></p>
                            </div>
                        <?php endif; ?>
                         <?php if (session()->getFlashdata('success')): ?>
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                                <p><?= session()->getFlashdata('success') ?></p>
                            </div>
                        <?php endif; ?>


                        <!-- Input Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="bi bi-envelope text-gray-400"></i>
                                </span>
                                <input class="form-control w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="email" id="email" name="email" placeholder="contoh@gmail.com" required>
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                             <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="bi bi-key text-gray-400"></i>
                                </span>
                                <input class="form-control w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="password" id="password" name="password" placeholder="••••••••" required>
                                <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                                    <i class="bi bi-eye-slash text-gray-400"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Login -->
                        <div class="form-group">
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300" type="submit">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
                
                <p class="text-center text-gray-400 text-xs mt-6">
                    &copy;<?= date('Y') ?> Pondok Pesantren Darul Quran. All rights reserved.
                </p>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function () {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the icon
                if (type === 'password') {
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                } else {
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                }
            });
        });
    </script>

</body>
</html>
