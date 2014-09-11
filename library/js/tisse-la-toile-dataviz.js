
	function tisse_la_toile(){

		var w = jQuery(window).width(),
			h = jQuery(window).height(), 
			root;

		var margin = {
			x: 10,
			y: 10
		};
		var padding = {
			x: 10,
			y: 10
		};

		var width = w - margin.x;
		var height = h - margin.y;


		var force = d3.layout.force()
								.size([width, height])
								.on("tick", tick);
		
		
		var svg = d3.select("#svg").append("svg").attr("width", width).attr("height", height);
		var link = svg.selectAll(".link"),
			node = svg.selectAll(".node");


		// runtime
		var data = {
		'action': 'tisse_la_toile'
		};


/*
		d3.json("readme.json", function(json) {
			root = json;
			update();
		});
		
*/


jQuery.get(MyAjax.ajaxurl, data, function(JSONData) {
			//console.log(JSONData);
/*
[
	{
	"id":17,
	name:"Doug Englebart",
	"children":[
		{"term_id":8,"name":"american","slug":"american","term_group":0,"term_taxonomy_id":8,"taxonomy":"post_tag","description":"","parent":0,"count":2,"filter":"raw"},
		{"term_id":7,"name":"Engelbart's Law","slug":"engelbarts-law","term_group":0,"term_taxonomy_id":7,"taxonomy":"post_tag","description":"","parent":0,"count":1,"filter":"raw"},
		{"term_id":6,"name":"hypertext","slug":"hypertext","term_group":0,"term_taxonomy_id":6,"taxonomy":"post_tag","description":"","parent":0,"count":1,"filter":"raw"},
		{"term_id":9,"name":"interaction","slug":"interaction","term_group":0,"term_taxonomy_id":9,"taxonomy":"post_tag","description":"","parent":0,"count":1,"filter":"raw"},
		{"term_id":5,"name":"mouse","slug":"mouse","term_group":0,"term_taxonomy_id":5,"taxonomy":"post_tag","description":"","parent":0,"count":1,"filter":"raw"}
		]
	}
]
*/
		root = JSONData.slice();
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
//      .attr("r", function(d) { return Math.sqrt(d.size) / 10 || 4.5; })
      .attr("r", function(d) { return 4.5; })
      .style("fill", color)
      .on("click", click)
      .call(force.drag);



		});
	// HELPERS
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
  return d._children ? "#3182bd" : d.children ? "#c6dbef" : "#fd8d3c";
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
//END 
}




