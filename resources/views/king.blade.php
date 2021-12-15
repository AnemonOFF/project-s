<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/normalize.css') }}">
</head>
<body>
            <!--<div class="loader__container">
                1) <div class="loader"></div>
                2) <div class="circle-loader">
                    <div class="checkmark draw"></div>
                </div>
            </div>-->

    <header>
        <button class="mainbutton main_flex" id="ChoosePlatform">platforms</button>
        <h2 class="loft">Project-S</h2>
    </header>
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
    <nav class="norm_class" >
      <button class="mainbutton fir" style="color:#000;" >Previous</button>
      <button class="mainbutton sec" style="color:#000;">next</button>
    </nav>

    <!-- MODALS -->

    <div class="modal-course" style="display: none;">
        <div class="modal-course__title">
            <button class="button-back">Back</button>
            <h1>Course blocks</h1>
        </div>
        <div class="modal-course__blocks">
        </div>
    </div>

    <div class="modal" style="display: none;">
        <div class="modal__content">
            <div class="modal__title"></div>
            <div class="modal__items"></div>
        </div>
        <button class="mainbutton modal__submit">accept</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/slept.js') }}"></script>
</body>
</html>