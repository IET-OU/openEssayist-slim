// From: 'templates/drafts/view.cloud.twig' -- Unused code !!

/* <div class="widget hidden">

	  <div class="widget-header" data-toggle="collapse" data-target="#demo">
				<h2> Display Options </h2>
	  </div>

		<div class="widget-content" id='svg-angles'>

			<input type="number" id="angle-count" value="5" min="1"> <label for="angle-count">orientations</label>
      <label for="angle-from">from</label> <input type="number" id="angle-from" value="-60" min="-90" max="90"> °●@
      <label for="angle-to">to</label> <input type="number" id="angle-to" value="60" min="-90" max="90"> °
		</div>
</div> */

(function() {
	  var r = 40.5,
	      px = 35,
	      py = 20;

	  var angles = d3.select("#svg-angles").append("svg")
	      .attr("width", 2 * (r + px))
	      .attr("height", r + 1.5 * py)
	    .append("g")
	      .attr("transform", "translate(" + [r + px, r + py] +")");

	  angles.append("path")
	      .style("fill", "none")
	      .style("stroke", "#666666")
	      .attr("d", ["M", -r, 0, "A", r, r, 0, 0, 1, r, 0].join(" "));

	  angles.append("line")
	  .style("stroke", "#666666")
	      .attr("x1", -r - 7)
	      .attr("x2", r + 7);

	  angles.append("line")
	  .style("stroke", "#666666")
	      .attr("y2", -r - 7);

	  angles.selectAll("text")
	      .data([-90, 0, 90])
	    .enter().append("text")
	      .attr("dy", function(d, i) { return i === 1 ? null : ".3em"; })
	      .attr("text-anchor", function(d, i) { return ["end", "middle", "start"][i]; })
	      .attr("transform", function(d) {
	        d += 90;
	        return "rotate(" + d + ")translate(" + -(r + 10) + ")rotate(" + -d + ")translate(2)";
	      })
	      .text(function(d) { return d + "°"; });

	  var radians = Math.PI / 180,
	      from,
	      to,
	      count,
	      scale = d3.scale.linear(),
	      arc = d3.svg.arc()
	        .innerRadius(0)
	        .outerRadius(r);

	  d3.selectAll("#angle-count, #angle-from, #angle-to")
	      .on("change", getAngles)
	      .on("mouseup", getAngles);

	  getAngles();

	  function getAngles() {
	    count = +d3.select("#angle-count").property("value");
	    from = Math.max(-90, Math.min(90, +d3.select("#angle-from").property("value")));
	    to = Math.max(-90, Math.min(90, +d3.select("#angle-to").property("value")));
	    update();
	  }

	  function update() {
	    scale.domain([0, count - 1]).range([from, to]);
	    var step = (to - from) / count;

	    var path = angles.selectAll("path.angle")
	        .data([{startAngle: from * radians, endAngle: to * radians}]);
	    path.enter().insert("path", "circle")
	        .attr("class", "angle")
	        .style("stroke", "#666666")
	        .style("fill", "#fc0");
	    path.attr("d", arc);

	    var line = angles.selectAll("line.angle")
	        .data(d3.range(count).map(scale));
	    line.enter().append("line")
	        .attr("class", "angle")
	    	.style("stroke", "#666666");
	    line.exit().remove();
	    line.attr("transform", function(d) { return "rotate(" + (90 + d) + ")"; })
	        .attr("x2", function(d, i) { return !i || i === count - 1 ? -r - 5 : -r; });

	    var drag = angles.selectAll("path.drag")
	        .data([from, to]);
	    drag.enter().append("path")
	        .attr("class", "drag")
	        .attr("d", "M-9.5,0L-3,3.5L-3,-3.5Z")
	        .call(d3.behavior.drag()
	          .on("drag", function(d, i) {
	            d = (i ? to : from) + 90;
	            var start = [-r * Math.cos(d * radians), -r * Math.sin(d * radians)],
	                m = [d3.event.x, d3.event.y],
	                delta = ~~(Math.atan2(cross(start, m), dot(start, m)) / radians);
	            d = Math.max(-90, Math.min(90, d + delta - 90)); // remove this for 360°
	            delta = to - from;
	            if (i) {
	              to = d;
	              if (delta > 360) from += delta - 360;
	              else if (delta < 0) from = to;
	            } else {
	              from = d;
	              if (delta > 360) to += 360 - delta;
	              else if (delta < 0) to = from;
	            }
	            update();
	          })
	          .on("dragend", generate));
	    drag.attr("transform", function(d) { return "rotate(" + (d + 90) + ")translate(-" + r + ")"; });


	    d3.select("#angle-count").property("value", count);
	    d3.select("#angle-from").property("value", from);
	    d3.select("#angle-to").property("value", to);
	  }
	  function generate() {};
	  function cross(a, b) { return a[0] * b[1] - a[1] * b[0]; }
	  function dot(a, b) { return a[0] * b[0] + a[1] * b[1]; }
	})();

// </script>
// {% endblock %}
