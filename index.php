<html>
<head>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {'packages':['gantt']});
    google.charts.setOnLoadCallback(drawCharts);

    var jsonData;
    var jsonObject;
    var trackHeight = 30;

    function drawCharts() {

      jsonData = $.ajax({
        url: "data.php",
        dataType: "json",
        async: false,

      }).responseText;

      jsonObject = JSON.parse(jsonData);

      for (idx in jsonObject.boards) {
        var chartDivId = "chart_div_" + idx;

        $('body').append('<h1>' + jsonObject.boards[idx].name + '</h1><div id="' + chartDivId + '"></div>');
        drawChart(jsonObject.boards[idx], chartDivId);
      //  data.addRows([  ['2004', 1000 , 400], ['2005', 1170, 460], ['2006', 660, 1120], ['2007',1030,540]]);
      }

    }

    function drawChart(board, elId) {

      var boardJsonData = {
        "cols": board.cols,
        "rows": board.rows
      };

      var data = new google.visualization.DataTable(boardJsonData);

      var options = {
        height: (trackHeight * board.number_of_cards) + 200,
        gantt: {
          trackHeight: trackHeight
        }
      };

      var chart = new google.visualization.Gantt(document.getElementById(elId));

      chart.draw(data, options);
    }

  </script>
  
</head>
<body>
</body>
</html>