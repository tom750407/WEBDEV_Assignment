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
    googleChart();
});

function googleChart() {

    let url = "Map_Chart.php";

    let json = $.ajax({
        url: url,
        dataType: "json",
        async: false
    }).responseText;

    var data = new google.visualization.DataTable(json);
    var dataView = new google.visualization.DataView(data);
    dataView.setColumns([
        0, 1,
        {
            calc: function (data, row) {
                return "location: " + data.getValue(row, 2) + ", lat:" + data.getValue(row, 0) + ", long:" + data.getValue(row, 1);
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

