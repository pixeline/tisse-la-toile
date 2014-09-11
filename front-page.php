<?php 
/*
 Template Name: Frontpage

*/


/* Fetch all tags as json: http://tisse-la-toile/api/get_tag_index/?count=1000  */

get_header();

if (have_posts()) : while (have_posts()) : the_post(); ?>
	<article role="article" itemscope>
		<section class="entry-content cf" style="text-align:center;">
			<?php
				the_content();
			?>
		</section>
	</article>
<?php endwhile; endif ?>
	<div id="dataviz-container"></div>
<script type="text/javascript" charset="utf-8">
var w = 960, h = 500;

var svg = d3.select("#dataviz-container").append("svg:svg").attr("width", w).attr("height", h);
// Necessary for wordpress ajaxing
var action = {'action': 'tisse_la_toile'};

var root =null;
var force = d3.layout.force()
    .size([w, h])
    .on("tick", tick);

var link = svg.selectAll(".link"),
    node = svg.selectAll(".node");

var tooltip = d3.select("body").append("div")   
    .attr("class", "tooltip")               
    .style("opacity", 0);

//jQuery.get(MyAjax.ajaxurl, action, function(json_data) {
d3.json(MyAjax.ajaxurl+"?action=tisse_la_toile", function(json_data) {

  root = json_data;
  update();

});

function update() {
  var nodes = flatten(root),
      links = d3.layout.tree().links(nodes);

  // Restart the force layout.
  force
      .nodes(nodes)
      .links(links)
      .start();

  // Update the links…
  link = link.data(links, function(d) { return d.target.id; });

  // Exit any old links.
  link.exit().remove();

  // Enter any new links.
  link.enter().insert("line", ".node")
      .attr("class", "link")
      .attr("x1", function(d) { return d.source.x; })
      .attr("y1", function(d) { return d.source.y; })
      .attr("x2", function(d) { return d.target.x; })
      .attr("y2", function(d) { return d.target.y; });

  // Update the nodes…
  node = node.data(nodes, function(d) { return d.id; }).style("fill", color);

  // Exit any old nodes.
  node.exit().remove();

  // Enter any new nodes.
  node.enter().append("circle")
      .attr("class", "node")
      .attr("cx", function(d) { return d.x; })
      .attr("cy", function(d) { return d.y; })
      .attr("r", function(d) { return Math.sqrt(d.size) / 10 || 4.5; })
	  //.attr("r", function(d) { return (((d.type=="article") ? 10 : 4)+ d.count); })
      .style("fill", color)
      .on("click", click)
	  .on("mouseover", function(d) {      
            tooltip.transition()        
                .duration(200)      
                .style("opacity", .9);      
            tooltip .html(d.name)  
                .style("left", (d3.event.pageX) + "px")     
                .style("top", (d3.event.pageY - 28) + "px");    
            })                  
        .on("mouseout", function(d) {       
            tooltip.transition()        
                .duration(500)      
                .style("opacity", 0);   
        })
      .call(force.drag);
}

function tick() {
  link.attr("x1", function(d) { return d.source.x; })
      .attr("y1", function(d) { return d.source.y; })
      .attr("x2", function(d) { return d.target.x; })
      .attr("y2", function(d) { return d.target.y; });

  node.attr("cx", function(d) { return d.x; })
      .attr("cy", function(d) { return d.y; });
}

// Color leaf nodes orange, and packages white or blue.
function color(d) {
  return ((d.type=="article") ? "#333333" : "#990000");
}

// Toggle children on click.
function click(d) {
  if (!d3.event.defaultPrevented) {
    if (d.children) {
      d._children = d.children;
      d.children = null;
    } else {
      d.children = d._children;
      d._children = null;
    }
    update();
  }
}

// Returns a list of all nodes under the root.
function flatten(root) {
  var nodes = [], i = 0;

  function recurse(node) {
    if (node.children) node.children.forEach(recurse);
    if (!node.id) node.id = ++i;
    nodes.push(node);
  }

  recurse(root);
  return nodes;
}

</script>						

							

<?php get_footer(); ?>