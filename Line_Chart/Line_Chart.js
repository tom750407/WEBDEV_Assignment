/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Task is to showing levels in any 24 hour period on any day (user selectable) for any of the six stations
 * Code is similar to other chart with some chart costume like changing colour or costmize tooltip
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

    let url = "Line_Chart.php";

    //params
    let file = $('#files').val();
    let location = $('#files option:selected').text();
    let date = $('#dates').val();
    let dateCurrent = datetry(date);
    let dateCurrentString = dateFormat(dateCurrent);

    let time = $('#time').html();

    let params = "?file=" + file + "&date=" + dateCurrentString + "&time=" + time;

    //http://api.jquery.com/jQuery.ajax/
    //https://www.w3schools.com/jquery/jquery_ajax_intro.asp
    //send data into server(php), the data can be process without reloading the page
    //in this case, the date select can shows the updating in the chart.
    let json = $.ajax({
        url: url + params,
        dataType: "json",
        async: false
    }).responseText;

    var data = new google.visualization.DataTable(json);
    var dataView = new google.visualization.DataView(data);
    //costume google chart
    dataView.setColumns([
        0, 1,
        {
            //colour change
            calc: function (data, row) {
                var val = data.getValue(row, 1);
                if (val >= 0 && val <= 67) {
                    return "#DAF7A6";
                }
                if (val >= 68 && val <= 134) {
                    return "#80FF00";
                }
                if (val >= 135 && val <= 200) {
                    return "#94C800";
                }
                if (val >= 201 && val <= 267) {
                    return "#F3F000";
                }
                if (val >= 268 && val <= 334) {
                    return "#FFC300";
                }
                if (val >= 335 && val <= 400) {
                    return "#F19A00";
                }
                if (val >= 401 && val <= 467) {
                    return "#FF5F5F";
                }
                if (val >= 468 && val <= 534) {
                    return "#FE0404";
                }
                if (val >= 535 && val <= 600) {
                    return "#900C3F";
                }
                if (val >= 601) {
                    return "#BE02E3";
                }
                return "000000";
            },
            type: 'string',
            role: 'style'
        },
        {
            //tooltip
            calc: function (data, row) {
                let date = new Date(data.getValue(row, 0));
                let dateInFormate = (date.getDate() < 10 ? "0" : "") + date.getDate() + "/"
                        + (date.getMonth() < 10 ? "0" : "") + date.getMonth() + "/"
                        + date.getFullYear() + " " + date.getHours() + ":" + date.getMinutes()
                        + ":" + date.getSeconds();
                return dateInFormate + "\nNO2 Value: " + data.getValue(row, 1);

            },
            type: 'string',
            role: 'tooltip'
        }
    ]);

    var options = {
        title: 'Air Quality: ' + location,
        hAxis: {
            title: 'Time',
            gridlines: {
                count: -1
            }
        },
        vAxis: {title: 'NO2'},
        legend: {position: 'right'},
    };

    let chart = new google.visualization.LineChart(document.getElementById("chart"));

    chart.draw(dataView, options);

}

