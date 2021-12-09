<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header>
        <button class="mainbutton" id="ChoosePlatform">platforms</button>
        <h2 class="loft">Project-S</h2>
    </header>
    <div class="modal" style="display: none;">
        <div class="modal__content">
            <div class="modal__title"></div>
            <div class="modal__items"></div>
        </div>
        <button class="mainbutton modal__submit">accept</button>
    </div>
    <table class="tables" id="CourseStudentsTable">
        <thead>
            <tr>
                <th>ФИО</th>
                <th>Количество баллов</th>
                <th>Результат в %</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/slept.js') }}"></script>
</body>
</html>