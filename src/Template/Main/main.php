<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../baseTemplate.php'; ?>
    <title>Main</title>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-3">
        <h2 class="mb-0">Main Page</h2>
        <button id="logoutButton" class="btn btn-danger">Logout</button>
    </div>

    <hr>

    <h4 class="mt-4 mb-3">Список пользователей:</h4>

    <?php if (!empty($list)) : ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle text-nowrap">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Имя</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $index => $user): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p class="text-muted">Пользователи не найдены.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoutButton = document.getElementById('logoutButton');
        logoutButton.addEventListener('click', function () {
            fetch('/api/logout', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Logout successful!');
                    clearAuthCookies();
                    window.location.href = '/login';
                } else {
                    alert('Logout failed: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        function clearAuthCookies() {
            document.cookie = "access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "refresh_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
    });
</script>

</body>
</html>
