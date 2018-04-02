/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Extension Chart
 * Code is same with others, require an api key
 * Costmize tooltip with lat and long
 */

//when document is ready load google chart api and draw the graph, it need api key for google map display
$(document).ready(function () {
    google.charts.load('current', {packages: ['map'], "mapsApiKey": "AIzaSyCowE35iCkaDqWdyz5_7-gjaX8SlU02H2w"});
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

    let url = "Map_Chart.php";

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

    console.log(json)
    let jsonArr = JSON.parse(json);
    console.log(jsonArr);
    console.log(jsonArr.rows);
    console.log(jsonArr.rows[0]);


    var data = new google.visualization.DataTable(json);
    var dataView = new google.visualization.DataView(data);
    dataView.setColumns([
        0, 1,
        {
            calc: function (data, row) {
                return  " lat:" + data.getValue(row, 0) + ", long:" + data.getValue(row, 1);
            },
            type: 'string',
            role: 'tooltip'
        },
    ]);

    var options = {
        title: 'Air Quality:',
        showTooltip: true,
        showInfoWindow: true
    };

    let map = new google.visualization.Map(document.getElementById("chart"));

    map.draw(dataView, options);

}

