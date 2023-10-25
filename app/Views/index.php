<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Developer Test</title>
  <meta name="description" content="The small framework with powerful features">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="/favicon.ico">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

  <style>
    .input-group-append {
      cursor: pointer;
    }
  </style>
</head>

<body>

  <div class="container py-5">
    <div class="d-flex">
      <form class="d-flex" id="filter">
        <div class="mx-3">
          <select class="form-select" id="storearea" name="storearea[]" data-placeholder="Select Area" multiple
            required>
            <?php foreach ($storeAreas as $storeArea): ?>
              <option value=<?= $storeArea['area_id']; ?>><?= $storeArea['area_name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mx-3">
          <div class="input-group date" id="seldatefrom">
            <input type="text" class="form-control" id="datefrom" name="datefrom" required
              data-date-format="yyyy-mm-dd" />
            <span class="input-group-append">
              <span class="input-group-text bg-light d-block" style="border-radius:0px;">
                <i class="fa fa-calendar"></i>
              </span>
            </span>
          </div>
        </div>

        <div class="mx-3">
          <div class="input-group date" id="seldateto">
            <input type="text" class="form-control" id="dateto" name="dateto" required data-date-format="yyyy-mm-dd" />
            <span class="input-group-append">
              <span class="input-group-text bg-light d-block" style="border-radius:0px;">
                <i class="fa fa-calendar"></i>
              </span>
            </span>
          </div>
        </div>

        <button class="btn btn-primary" type="submit">View</button>
      </form>

    </div>

    <div class="py-5">
      <div id="chart"></div>
    </div>

    <div>
      <table class="table" id="data-table">
        <thead>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>


  <script>
    $('#storearea').select2({
      theme: "bootstrap-5",
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    $(function () {
      $('#seldatefrom').datepicker({
        format: "yyyy-mm-dd",
      });
    });

    $(function () {
      $('#seldateto').datepicker({
        format: "yyyy-mm-dd",
      });
    });
  </script>

  <script>
    document.getElementById('filter').addEventListener('submit', function (event) {
      event.preventDefault();
      submitForm();
    });

    function submitForm() {
      const form = document.getElementById('filter');
      const formData = new FormData(form);

      axios.post('/filter', formData)
        .then(function (response) {
          data = response.data;
          const categories = data.map(item => item.area_name);
          const data_series = data.map(item => parseFloat(item.value));

          var averageValues = {};

          for (var i = 0; i < response.data.length; i++) {
            var item = response.data[i];
            var area = item.area_name;
            var value = parseFloat(item.value);

            if (!averageValues[area]) {
              averageValues[area] = { total: value, count: 1 };
            } else {
              averageValues[area].total += value;
              averageValues[area].count++;
            }
          }

          for (var area in averageValues) {
            averageValues[area].average = averageValues[area].total / averageValues[area].count;
          }

          var result_value = [];
          for (var area in averageValues) {
            result_value.push(averageValues[area].average);
          }

          var result_area = [];
          for (var area in averageValues) {
            result_area.push(area);
          }

          chart.updateSeries([{
            name: 'Percentage',
            data: result_value
          }]),
            chart.updateOptions({
              xaxis: {
                categories: result_area
              }
            });

          var tableData = {};
          for (var i = 0; i < response.data.length; i++) {
            var item = response.data[i];
            var area = item.area_name;
            var brand = item.brand_name;
            var value = item.value;

            if (!tableData[area]) {
              tableData[area] = {};
            }

            if (!tableData[area][brand]) {
              tableData[area][brand] = value;
            }
          }

          var table = "<table>";
          var headers = ["Brand"].concat(Object.keys(tableData));
          table += "<tr>";
          headers.forEach(function (header) {
            table += "<th>" + header + "</th>";
          });
          table += "</tr>";

          for (var brand in tableData[Object.keys(tableData)[0]]) {
            table += "<tr><td>" + brand + "</td>";
            for (var area in tableData) {
              table += "<td>" + (tableData[area][brand] || '') + "</td>";
            }
            table += "</tr>";
          }

          table += "</table>";

          document.getElementById("data-table").innerHTML = table;


        })
        .catch(function (error) {
          console.log(error);
        });
    }
  </script>

  <script>
    var options = {
      noData: {
        text: 'Loading...'
      },
      series: [],
      chart: {
        height: 350,
        type: 'bar',
      },
      plotOptions: {
        bar: {
          dataLabels: {
            position: 'top',
          },
        }
      },
      dataLabels: {
        enabled: true,
        formatter: function (val) {
          return val + "%";
        },
        offsetY: -20,
        style: {
          fontSize: '12px',
          colors: ["#304758"]
        }
      },

      xaxis: {
        position: 'top',
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        crosshairs: {
          fill: {
            type: 'gradient',
            gradient: {
              colorFrom: '#D8E3F0',
              colorTo: '#BED1E6',
              stops: [0, 100],
              opacityFrom: 0.4,
              opacityTo: 0.5,
            }
          }
        },
        tooltip: {
          enabled: true,
        }
      },
      yaxis: {
        axisBorder: {
          show: true
        },
        axisTicks: {
          show: false,
        },
        labels: {
          show: true,
          formatter: function (val) {
            return val + "%";
          }
        }

      },
    };
    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
  </script>
</body>

</html>