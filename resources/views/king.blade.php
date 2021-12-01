<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title></title>
  <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
</head>
<body>

  <button class="mainbutton" id="ChoosePlatform">Выбор платформы/Chosing platfotm</button>
  <div class="modalewin" style="display: none;">
    <div class="inmodale">
      <div class="modal__title"></div>
      <div class="flexer">
        
      </div>
    </div>
    <button class="mainbutton modal__submit">Применить</button>
  </div>
  <table style="display:none;" class="tables">
  <colgroup>
    <col span="2" style="background:Khaki"><!-- С помощью этой конструкции задаем цвет фона для первых двух столбцов таблицы-->
    <col style="background-color:LightCyan"><!-- Задаем цвет фона для следующего (одного) столбца таблицы-->
  </colgroup>
  <tr>
    <th>ФИО</th>
    <th>Количество баллов</th>
    <th>Результат в %</th>
  </tr>
  <tr>
    <td>Леонов</td>
    <td>68</td>
    <td>20,00</td>
  </tr>
  <tr>
    <td>Дударев</td>
    <td>67</td>
    <td>30,00</td>
  </tr>
</table>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="{{ asset('js/slept.js') }}"></script>
</body>
</html>