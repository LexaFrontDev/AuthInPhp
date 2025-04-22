# TestListen — Тестовое задание

## Описание
Простой проект авторизации, реализованный на PHP и чистом JavaScript (без фреймворков).  
Для создания JWT токенов используется Firebase.

### Стек:
- **PHP**
- **JavaScript (Vanilla)**
- **Firebase (JWT)**
- **Phinx** — только для миграций, не используется в runtime запросах
- **Dotenv** — для хранения конфигураций в `.env`

---

## Установка и запуск

1. Клонируйте репозиторий:
   ```bash
   git clone https://github.com/LexaFrontDev/AuthInPhp
   ```

2. Перейдите в папку проекта:
   ```bash
   cd AuthInPhp
   ```

3. Установите зависимости через Composer:
   ```bash
   composer install
   ```

4. Создайте базу данных, затем настройте подключение в `.env`:
   ```
   DB_HOST=ваш_хост
   DB_PORT=порт_бд
   DB_NAME=название_базы
   DB_USER=пользователь
   DB_PASSWORD=пароль
   DB_CHARSET=utf8mb4
   ```

5. Убедитесь, что домен проекта прописан:
   - либо в `hosts` файле (`C:\Windows\System32\drivers\etc\hosts`)
   - либо в настройках **OpenServer** (или вашего локального окружения)

6. Запустите миграции:
   ```bash
   vendor/bin/phinx migrate
   ```

7. Готово! Откройте проект в браузере по указанному домену (например, `http://testlisten.local`)
