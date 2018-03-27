/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    function dateFormat(date) {
        //formate date to DD/MM/YYYY
        let day = date.getDate();
        day = (day < 10 ? "0" : "") + day;
        let month = date.getMonth() + 1;
        month = (month < 10 ? "0" : "") + month;
        let dateToString = day + "/" + month + "/" + date.getFullYear();
        return dateToString;
    }

    function datetry(date) {
        var parts = date.split('-');
        var mydate = new Date(parts[0], parts[1] - 1, parts[2]);
        return mydate;
    }

    let url = "Bar_Chart.php";

    //params
    let date = $('#dates').val();
    let dateCurrent = datetry(date);
    let dateCurrentString = dateFormat(dateCurrent);
    dateCurrent.setDate(dateCurrent.getDate() + 1);
//    let dateNextString = dateFormat(dateCurrent);

//    console.log("current " + dateCurrentString);
//    console.log("next " + dateNextString);

    let time = $('#time').html();
//    console.log(time);

    let params = "?date=" + dateCurrentString + "&time=" + time;
    let json = $.ajax({
        url: url + params,
        dataType: "json",
        async: false
    }).responseText;

    //google maps
//    console.log(json);
    var data = new google.visualization.DataTable(json);
//    var dataView = new google.visualization.DataView(data);
//    dataView.setColumns([
//        0, 1,
//        {
//            calc: function (data, row) {
//                var val = data.getValue(row, 1);
//                if (val >= 0 && val <= 67) {
//                    return "#DAF7A6";
//                }
//                if (val >= 68 && val <= 134) {
//                    return "#80FF00";
//                }
//                if (val >= 135 && val <= 200) {
//                    return "#94C800";
//                }
//                if (val >= 201 && val <= 267) {
//                    return "#F3F000";
//                }
//                if (val >= 268 && val <= 334) {
//                    return "#FFC300";
//                }
//                if (val >= 335 && val <= 400) {
//                    return "#F19A00";
//                }
//                if (val >= 401 && val <= 467) {
//                    return "#FF5F5F";
//                }
//                if (val >= 468 && val <= 534) {
//                    return "#FE0404";
//                }
//                if (val >= 535 && val <= 600) {
//                    return "#900C3F";
//                }
//                if (val >= 601) {
//                    return "#BE02E3";
//                }
//                return "000000";
//            },
//            type: 'string',
//            role: 'style'
//        },
//        {
//            calc: function (data, row) {
//                let date = new Date(data.getValue(row, 0));
//                let dateInFormate = (date.getDate() < 10 ? "0" : "") + date.getDate() + "/"
//                        + (date.getMonth() < 10 ? "0" : "") + date.getMonth() + "/"
//                        + date.getFullYear() + " " + date.getHours() + ":" + date.getMinutes()
//                        + ":" + date.getSeconds();
//                return dateInFormate + "\nNO2 Value: " + data.getValue(row,1);
//
//            },
//            type: 'string',
//            role: 'tooltip'
//        }
//    ]);

    var options = {
        title: 'Air Quality:'
    };

    let chart = new google.visualization.BarChart(document.getElementById("chart"));

    chart.draw(data, options);

}

