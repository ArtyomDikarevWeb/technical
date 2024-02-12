<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Technical</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="wrapper">
    <div class="form">
        <div id="question">
            <h2 >Хотите загрузить товары?</h2>
        </div>
        <div id="pending" class="hidden" style="width: 100%; height: 60px">
            <h2>Выполняется, ожидайте...</h2>
        </div>
        <div id="failed" class="hidden" style="width: 100%; background-color: #ef4444">
            <h2>Ошибка, попробуйте позже или обратитесь в техподдержку</h2>
        </div>
        <div id="success" class="hidden" style="width: 100%; height: 60px; background-color: aquamarine; color: #636363">
            <h3 >Товары успешно добавлены</h3>
        </div>
        <div class="loader hidden"></div>
        <button class="button_form">Загрузить</button>
        <button class="button_form button_form_stop hidden">Остановить</button>
    </div>
</div>
</body>
</html>
