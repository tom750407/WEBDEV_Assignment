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
    let url = "Scatter_Chart.php";

    //params
    let file = $('#files').val();
    let location = $('#files option:selected').text();
    let date = $('#dates').val();
//    let time = document.getElementById("time").innerHTML;
    let time = $('#time').html();
    console.log(time);

    let params = "?file=" + file + "&date=" + date + "&time=" + time;
    let json = $.ajax({
        url: url + params,
        dataType: "json",
        async: false
    }).responseText;

    //google maps
    console.log(json);
    var data = new google.visualization.DataTable(json);
    var options = {
        title: 'Air Quality: ' + location,
        hAxis: {title: 'Date'},
        vAxis: {title: 'NO2'},
        legend: {position: 'right'}
    };

    let chart = new google.visualization.ScatterChart(document.getElementById("chart"));

    chart.draw(data, options);

}

