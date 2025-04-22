<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../baseTemplate.php'; ?>
    <title>Register</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-card {
            max-width: 500px;
            margin: 60px auto;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card register-card p-4">
            <h3 class="text-center mb-4">Регистрация</h3>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Имя пользователя</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Введите имя">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email адрес</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Введите email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Введите пароль">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                event.preventDefault(); 

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Регистрация прошла успешно!');
                        setAuthCookies(data.data.access_token, data.data.refresh_token);
                        window.location.href = '/main'; 
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                })
                .catch(error => console.error('Ошибка:', error));
            });

            function setAuthCookies(accessToken, refreshToken) {
                const accessExpires = new Date(Date.now() + 60 * 60 * 1000); 
                const refreshExpires = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000); 
                document.cookie = `access_token=${accessToken}; path=/; expires=${accessExpires.toUTCString()}; Secure; SameSite=Strict`;
                document.cookie = `refresh_token=${refreshToken}; path=/; expires=${refreshExpires.toUTCString()}; Secure; SameSite=Strict`;
            }
        });
    </script>
</body>
</html>
