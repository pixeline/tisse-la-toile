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
</div>
<div id="dataviz-container"></div>
<script type="text/javascript" charset="utf-8">

/*
	Graph chart visualisation of network
	inspired from: http://bl.ocks.org/mbostock/2706022
*/
// Necessary for wordpress ajaxing
var action = {'action': 'tisse_la_toile'};

var w = jQuery(window).width()-100,
	h = jQuery(window).height()-100
jQuery("#dataviz-container").width(w).height(h);

var svg = d3.select("#dataviz-container").append("svg")
    .attr("width", w)
    .attr("height", h);

d3.json(MyAjax.ajaxurl+"?action=tisse_la_toile", function(links) {
	
	var nodes = {};
	// Compute the distinct nodes from the links.
	links.forEach(function(link) {
		if(link.source=="Histoire du Web"){
			link.source = nodes[link.source] || (nodes[link.source] = {name: link.source, type: "root", url: link.url});
			link.target = nodes[link.target] || (nodes[link.target] = {name: link.target, type: link.type, url: link.url});
		}
else{
	  link.source = nodes[link.source] || (nodes[link.source] = {name: link.source, type: link.type, url: link.url});
	  link.target = nodes[link.target] || (nodes[link.target] = {name: link.target, type: link.type, url: link.url});
}
	});
	

	
	var force = d3.layout.force()
	    .nodes(d3.values(nodes))
	    .links(links)
	    .size([w, h])
		.linkDistance(100)
    .charge(-900)
/*
	    .linkDistance(300)
		.friction(.01)
		.chargeDistance(30)
		//.gravity(.005)
		.charge(300)
*/
	    .gravity(0.2)
		.on("tick", tick)
	    .start();
	
	var link = svg.selectAll(".link")
	    .data(force.links())
		.enter().append("line")
	    .attr("class", "link");
	
	var node = svg.selectAll(".node")
	    .data(force.nodes())
		.enter()
		.append("g")
	    .attr("class", "node")
		.attr("class", function(d) { return d.type; })
	    .on("mouseover", mouseover)
	    .on("mouseout", mouseout)
		.on("click", click)
	    .call(force.drag);
	
	node.append("circle")
		.attr("class", function(d) { return d.type; })
	    .attr("r", 6);
	
	node.append("text")
	    .attr("x", 12)
	    .attr("dy", ".35em")
	    .text(function(d) { 
			//truncate
			var n = 25;
			return (d.name.substr(0, n-1) + (d.name.length>n ? '...':'')); 
		});
	
	function tick() {
	  link
	      .attr("x1", function(d) { return d.source.x; })
	      .attr("y1", function(d) { return d.source.y; })
	      .attr("x2", function(d) { return d.target.x; })
	      .attr("y2", function(d) { return d.target.y; });
	
	  node
	      .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
	}
	
	function mouseover(d) {
	  d3.select(this).select("circle").transition()
	      .duration(750)
	      .attr("r", 16);
	  d3.select(this).select("text").transition()
	      .duration(750)
		  .attr("x", 32)
	      .style("font-size", "18px");
		  if(d.url){
			  d3.select("body").style("cursor", "pointer");
		 }
		
	}
	
	function mouseout() {
	  d3.select(this).select("circle").transition()
	      .duration(750)
	      .attr("r", 8);
	
	  d3.select(this).select("text").transition()
	      .duration(750)
		  .attr("x", 12)
	      .style("font-size", "12px");
	d3.select("body").style("cursor", "default");
	}
	function click(d) {
	
		if(d.url){
			//console.log(d.url);
			window.open(d.url);
		}
	}
	
	

});

</script>						
<div class="wrap">
<h2 style="text-transform:uppercase;line-height:1;margin:3em 0;padding-left:100px"><span class="byline" style="display:block;font-size:60%;">Taxonomie</span> mots-clefs</h2>
<?php
$tags = wp_get_all_tags();

if(count($tags)<0){
?>
<p>Aucun fil tissé pour l'instant... Reviens plus tard.</p>
<?
} else{
 ?>
 <dl class="items-list">
<?php

$current_header = '';

foreach ($tags as $tag){
	
			


			// get all posts by that tag

			
    $args=array(
      'tag' => $tag->name,
      'showposts'=>500,
      'ignore_sticky_posts'=>1
    );
    $my_query = new WP_Query($args);
    if( $my_query->have_posts() ) {

			$first_letter = strtoupper(substr($tag->name, 0, 1));
			
			if($current_header != $first_letter){
				$current_header = $first_letter;
				echo '<span class="glossary-letter">'.$current_header.'</span>';
				
			}
			echo "<dt>{$tag->name}</dt>";
			echo '<dd><ol>';

      while ($my_query->have_posts()) : $my_query->the_post(); ?>
        <li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
       <?php
      endwhile;
    } //if ($my_query)
  wp_reset_query();


			echo '</ol></dd>';
			//edit_term_link('edit tag','<p>','</p>',$tag);
}

?>
 </dl>
<?
}
?>
</div>

<?php get_footer(); ?>