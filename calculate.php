<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $expression_raw = $_POST['expression'] ?? '';

    $expression = preg_replace('/[^0-9+\-*\/.]/', '', $expression_raw);

    if (empty($expression)) {
        $error = "Выражение не может быть пустым.";
    } else {
        try {
            $result = eval("return $expression ;");
            if ($result === false) {
                $error = "Ошибка при вычислении.";
            }
        } catch (ParseError $e) {
            $error = "Неверное выражение.";
        } catch (Throwable $e) {
            $error = "Ошибка: " . $e->getMessage();
        }
    }
} else {
    $error = "Данные не были отправлены корректно.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Результат</title>
    <link rel="stylesheet" href="/public/css/Calc.css" />
</head>
<body>

<div class="calculator-container">
    <h1>Результат вычисления</h1>

    <?php if (isset($error)) : ?>
        <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
        <a href="/templates/calc.html" style="color:#0078d7; display:inline-block; margin-top:15px;">Назад</a>
    <?php else : ?>
        <input type="text" readonly value="<?php echo htmlspecialchars($expression); ?>" style="margin-bottom:10px; font-size:1rem;"/>
        <input type="text" readonly value="<?php echo $result; ?>" style="font-size:1.3rem; font-weight:700; color:#005499;"/>
        <form action="/templates/calc.html" method="get" style="margin-top:20px;">
            <button type="submit" class="btn equals" style="width:100%;">Новый расчёт</button>
        </form>
        <form action="/" method="get" style="margin-top:20px;">
            <button type="submit" class="btn equals" style="width:100%;">Главная страница</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>