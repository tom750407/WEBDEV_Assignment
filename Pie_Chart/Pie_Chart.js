/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Extension Chart
 * Code is same with Bar Chart
 * Costmize to 3D with tooltip location + no2 value
 */

//when document is ready load google chart api and draw the graph
$(document).ready(function () {
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(googleChart);
    displayTime();
});
function displayTime() {
    let slider = document.getElementById("times");
    let output = document.getElementById("time");
    let minutes = (slider.value - Math.floor(slider.value));
    minutes = minutes * 60;
    if (minutes === 0) {
        minutes = "00";
    }

    //display initial output
    output.innerHTML = (slider.value < 10 ? "0" : "") + Math.floor(slider.value) + ":" + minutes + ":00";
    googleChart();
}

function googleChart() {
//formate date to DD/MM/YYYY e.g.01/06/2015
    function dateFormat(date) {
        let day = date.getDate();
        day = (day < 10 ? "0" : "") + day;
        let month = date.getMonth() + 1;
        month = (month < 10 ? "0" : "") + month;
        let dateToString = day + "/" + month + "/" + date.getFullYear();
        return dateToString;
    }

//formate to date format e.g. Mon Jun 01 2015 00:00:00 GMT+0100 (BST) 
    function datetry(date) {
        var parts = date.split('-');
        var mydate = new Date(parts[0], parts[1] - 1, parts[2]);
        return mydate;
    }

    let url = "Pie_Chart.php";
    //params
    let date = $('#dates').val();
    let dateCurrent = datetry(date);
    let dateCurrentString = dateFormat(dateCurrent);
    let time = $('#time').html();
    let params = "?date=" + dateCurrentString + "&time=" + time;

    //send data into server(php), the data can be process without reloading the page
    //in this case, the date select can shows the updating in the chart.
    let json = $.ajax({
        url: url + params,
        dataType: "json",
        async: false
    }).responseText;

    var data = new google.visualization.DataTable(json);
    var dataView = new google.visualization.DataView(data);
    dataView.setColumns([
        0, 1,
        {
            calc: function (data, row) {
                return data.getValue(row, 0).toLowerCase() + '\nNO2 Value: ' + data.getValue(row, 1);
            },
            type: 'string',
            role: 'tooltip'
        }
    ]);
    var options = {
        title: 'Air Quality:',
        is3D: true,
        pieSliceText:'value'
    };
    let chart = new google.visualization.PieChart(document.getElementById("chart"));
    chart.draw(dataView, options);
}

