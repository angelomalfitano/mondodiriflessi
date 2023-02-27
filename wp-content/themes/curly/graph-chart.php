 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
	var jsonData = <?php echo json_encode($attandant_array); ?>;

    function drawChart() {
      var data = google.visualization.arrayToDataTable(jsonData);

      var view = new google.visualization.DataView(data);
  

      var options = {
        title: "Services for employee",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
      chart.draw(view, options);
  }
  </script>
<table class="wp-list-table widefat fixed striped table-view-list" style="width:600px;float: right;margin-right: 100px;">
<?php  
foreach($attandant_array as $key=>$att_val){  ?>
	<tr>
		<td><?php echo $att_val[0]; ?></td>
		<td><?php echo $att_val[1]; ?></td>
	</tr>
	
<?php } ?>

</table>
<div id="columnchart_values" style="width: 600px; height: 300px;"></div>
</br></br></br></br><br class="clear"> 
 <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart2);
	var jsonData2 = <?php echo json_encode($best_service_array); ?>;

    function drawChart2() {
      var data2 = google.visualization.arrayToDataTable(jsonData2);

      var view = new google.visualization.DataView(data2);
  

      var options = {
        title: "Best Services",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values2"));
      chart.draw(view, options);
  }
  </script>
<br class="clear"> <br class="clear">   
  <table class="wp-list-table widefat fixed striped table-view-list" style="width:600px;float: right;margin-right: 100px;">
<?php  
foreach($best_service_array as $key=>$att_val){  ?>
	<tr>
		<td><?php echo $att_val[0]; ?></td>
		<td><?php echo $att_val[1]; ?></td>
	</tr>
	
<?php } ?>

</table>
<div id="columnchart_values2" style="width:600px; height: 300px;"></div>

<br class="clear"> 
</br></br>


<script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart3);
	var jsonData3 = <?php echo json_encode($cust_output_array); ?>;

    function drawChart3() {
      var data3 = google.visualization.arrayToDataTable(jsonData3);

      var view = new google.visualization.DataView(data3);
  

      var options = {
        title: "Best Customer",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values3"));
      chart.draw(view, options);
  }
  </script>
 <br class="clear"> 
 <div style="width:100%;float:left;"></br></div>
<table class="wp-list-table widefat fixed striped table-view-list" style="width:600px;float: right;margin-right: 100px;">
<?php  
foreach($cust_output_array as $key=>$att_val){ ?>
	<tr>
		<td><?php echo $att_val[0]; ?></td>
		<td><?php echo $att_val[1]; ?></td>
	</tr>
	
<?php } ?>

</table></br></br>
<div id="columnchart_values3" style="width: 600px; height: 300px;"></div>