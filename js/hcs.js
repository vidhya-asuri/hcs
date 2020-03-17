var hcsChartData = {};

$( document ).ready(function() {
    console.log( "ready!" );
    
	var xhr = new XMLHttpRequest();
	xhr.open("POST", '../db.php', true);

	  xhr.onreadystatechange = function() { // Call a function when the state changes.
	      if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
	    	  var data = JSON.parse(xhr.response);
	    	  // Populate the global chartData variable.
	    	  hcsChartData.monthlyData = data[0];
	    	  hcsChartData.quarterlyData = data[1];
	    	  hcsChartData.yearlyData = data[2];
	    	  
	          // Request finished. Do processing here.
	    	  console.log("request is complete!");
	    	  // var countyData = JSON.parse(xhr.response);
	    		d3.json("county.json",function(err,data) {
	    			  displayChart(data);
	    			  // update(data1,transition=false);
	    			  
	    			  
	    		});
	    		
	    		var data = xhr.response;
	      }
	  }
	  
	  var formData = new FormData();
	  formData.append("countyID", '0115');

	  xhr.send(formData);

	  // Set up button click handlers.
	  document.getElementById('monthly').onclick = displayMonthlyData;
	  document.getElementById('quarterly').onclick = displayQuarterlyData;
	  document.getElementById('yearly').onclick = displayYearlyData;

	  
	  var ctx = document.getElementById('myChart').getContext('2d');
	  var myChart = new Chart(ctx, {
	      type: 'bar',
	      data: {
	          labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
	          datasets: [{
	              label: '# of Votes',
	              data: [12, 19, 3, 5, 2, 3],
	              backgroundColor: [], /*[ 
	                  'rgba(255, 99, 132, 0.2)',
	                  'rgba(54, 162, 235, 0.2)',
	                  'rgba(255, 206, 86, 0.2)',
	                  'rgba(75, 192, 192, 0.2)',
	                  'rgba(153, 102, 255, 0.2)',
	                  'rgba(255, 159, 64, 0.2)'
	              ],*/
	              borderColor: [
	                  'rgba(255, 99, 132, 1)',
	                  'rgba(54, 162, 235, 1)',
	                  'rgba(255, 206, 86, 1)',
	                  'rgba(75, 192, 192, 1)',
	                  'rgba(153, 102, 255, 1)',
	                  'rgba(255, 159, 64, 1)'
	              ],
	              borderWidth: 1
	          }]
	      },
	      options: {
	          scales: {
	              yAxes: [{
	                  ticks: {
	                      beginAtZero: true
	                  }
	              }]
	          }
	      }
	  });
	  
	  
});


function displayChart(data)
{
	update(data[0],transition=false);
}

function displayMonthlyData()
{
	update(hcsChartData.monthlyData,transition=false);
}

function displayQuarterlyData()
{
	update(hcsChartData.quarterlyData,transition=false);
}

function displayYearlyData()
{
	update(hcsChartData.yearlyData,transition=false);
}




		var margin = {top: 80, right: 80, bottom: 70, left: 60},
		    width = 2000 - margin.left - margin.right,
		    height = 400 - margin.top - margin.bottom;
	
		  // append the svg object to the body of the page
		var svg = d3.select("#hcsChart")
		    .append("svg")
		    .attr("width", width + margin.left + margin.right)
		    .attr("height", height + margin.top + margin.bottom)
		    .append("g")
		    .attr("transform","translate(" + margin.left + "," + margin.top + ")");
		svg
		  .append('defs')
		  .append('pattern')
		    .attr('id', 'diagonalHatch')
		    .attr('patternUnits', 'userSpaceOnUse')
		    .attr('width', 4)
		    .attr('height', 4)
		  .append('path')
		    .attr('d', 'M-1,1 l2,-2 M0,4 l4,-4 M3,5 l2,-2')
		    .attr('stroke', '#000000')
		    .attr('stroke-width', 1);
	
		  var tooltip = d3.select("body").append("div").attr("class", "toolTip");
	
		  // Initialize the X axis
		  var x = d3.scaleBand()
		    .range([0, width])
		    .padding(0.2);
		  var xAxis = svg.append("g")
		    .attr("transform", "translate(0," + height + ")")
	
		  // Initialize the Y axis
		  var y = d3.scaleLinear()
		    .range([height, 0]);
		  var yAxis = svg.append("g")
		    .attr("class", "myYaxis")
	  
		var label = svg.append('g')
		    //.attr('transform', 'translate(' + [margin.left - 45, margin.top] + ')');
		    .attr('transform', 'translate(' + [margin.left - 90, margin.top] + ')');
		label.append('text')
		  //.text('Placeholder')
		  .text('Numerator')
		  .attr('transform', 'rotate(-90)')
		  //.attr({'text-anchor': 'start',x: -75,y: 20,})
		label.append('text')
		  .text('Placeholder County')
		  .attr('transform', 'translate(' + [width / 2, - margin.top -40] + ')')
		  .attr('font-size', '20px')
		  .attr('text-anchor', 'middle')
		label.append('text')
		  .text('Time')
		  .attr('transform', 'translate(' + [width - 30, margin.top + 135] + ')')

		  var g = svg.append("g")
		    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


	  // A function that create / update the plot for a given variable:
	  function update(data,transition=true) {

	    // Update the X axis
	    x.domain(data.map(function (d) { return d.group; }))
		// x.domain(data.keys());
	    xAxis.call(d3.axisBottom(x))

	    // Update the Y axis
	    y.domain([0, d3.max(data, function (d) { return d.value })]);
	    yAxis.transition().duration(1000).call(d3.axisLeft(y));

	    svg.exit().remove() // wipe clean
	    // Create the u variable
	    var u = svg.selectAll(".bar")
	      .data(data)
	  
	      u.enter().append("g")
	      .attr("class", "bars")
	      .append("rect")
	      .attr("class", "bar") // Add a new rect for each new elements
	      .merge(u) 
	      .transition() // and apply changes to all of them
	      .duration(function(){if (transition) {return 1000} else { return 0;};})
	      .attr("x", function (d) { return x(d.group); })
	      .attr("y", function (d) { return y(d.value); })
	      .attr("width", x.bandwidth())
	      .attr("height", function (d) { return height - y(d.value); })
	      .attr("fill", function(d) { if (d.sup == 1) { return "url(#diagonalHatch)"; } 
			else { return "#982568";}} )
	      .attr("stroke", "#982568")
	      .attr("stroke-width", 2)
	        var u2 = svg.selectAll(".bar")
	          .data(data)// get the already existing elements as well
	      u2
	      .on("mousemove", function (d) {
	        tooltip
	          .style("left", d3.event.pageX - 50 + "px")
	          .style("top", d3.event.pageY - 70 + "px")
	          .style("display", "inline-block")
	          .html(function(){if (d.sup == 1) { return "Date: " + (d.group) + "<br>Suppressed value is between 1 and 5."} else { return "Date: " + (d.group) + "<br>" + "Value: " + (d.value);}} );
	      })
	      .on("mouseout", function (d) { tooltip.style("display", "none"); })
	        
	    var bars = svg.selectAll(".bars").data(data);
	    d3.selectAll(".textlabel").remove();
	    bars.append('text') 
	      .attr("class", "textlabel")
	      //.text(function (d) { return d.value; })
	      .text(function(d) { if (d.sup == 1) { return "1-5"; } else {return d.value;}} )
	      //.attr("x", function (d) { return x(d.group) + x.bandwidth()/2.4; })
	      .attr("x", function (d) { if (d.sup == 1) { return x(d.group) + x.bandwidth()/6; ; } else {return x(d.group) + x.bandwidth()/2.4; }} )
	      .attr("y", function (d) { return y(d.value) - 5; })

	    // If less group in the new dataset, I delete the ones not in use anymore
	    u
	      .exit()
	      .remove()
	  } // End update function
	  
	  
	  
//	  d3.json("./county.json", function(err,data){
//		    console.log(data);
////			data1 = data[0];
////			data2 = data[1];
////			data3 = data[2];
//			update(data,transition=false);
//	  });
//	  

	  	
	

// });