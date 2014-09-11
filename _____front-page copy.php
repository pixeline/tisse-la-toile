<?php 
/*
 Template Name: Frontpage

*/


/* Fetch all tags as json: http://tisse-la-toile/api/get_tag_index/?count=1000  */

get_header(); ?>


							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article role="article" itemscope itemtype="http://schema.org/BlogPosting">
								<section class="entry-content cf" itemprop="articleBody" style="text-align:center;">
									<?php
										// the content (pretty self explanatory huh)
										the_content();

									?>
								</section>
							
<h2>Par mots-clefs</h2>
<?php
$tags = wp_get_all_tags();
 ?>
 <dl class="items-list">
<?php

$current_header = '';

foreach ($tags as $tag){
	

			$tag_link = get_tag_link($tag->term_id);
			
			$first_letter = strtoupper(substr($tag->name, 0, 1));
			
			if($current_header != $first_letter){
				$current_header = $first_letter;
				echo '<span class="glossary-letter">'.$current_header.'</span>';
				
			}
			echo "<dt><a class='content-link' href='{$tag_link}' title='{$tag->name} Tag' class='{$tag->slug}'>{$tag->name}</a></dt>";
			
			echo '<dd><ol>';

			// get all posts by that tag

			
    $args=array(
      'tag' => $tag->name,
      'showposts'=>500,
      'ignore_sticky_posts'=>1
    );
    $my_query = new WP_Query($args);
    if( $my_query->have_posts() ) {
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
							</article>

							<?php endwhile; endif ?>

<?php get_footer(); ?>