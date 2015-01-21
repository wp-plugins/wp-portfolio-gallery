<?php  
/*
 * Template Name: WP Portfolio Gallery
*/

get_header(); 
query_posts(array('post_type' => 'portfolio', 'posts_per_page' => -1));  
?>  
    
            <div class="jw-portfolio-filters">
                <button class="filter" data-filter="all"><?php _e('Show All'); ?></button>
                <?php  $tags = get_terms("jwtag");
                foreach ($tags as $tag) { ?>        
                
                    <button class="filter" data-filter=".<?php echo trim($tag->slug) ?>"><?php echo $tag->name ?></button>
                <?php } ?>
            </div>

            <div id="Container" class="wp-portfolio-gallery">
                    <?php 
                    while( have_posts() ) { 
                        the_post();
                        $terms = wp_get_post_terms(get_the_ID(), 'jwtag' );
                        $t = array();
                        foreach($terms as $term) $t[] = $term->slug;
                        ?> 
                        <?php $jeweltheme_items= get_option('jeweltheme_items'); ?>
                        <?php $jeweltheme_thumb_width = get_option('jeweltheme_thumb_width'); ?>
                        <?php $jeweltheme_thumb_height= get_option('jeweltheme_thumb_height'); ?>

                        <div style="width:<?php echo round(100/$jeweltheme_items); ?>%" class="mix <?php echo implode(' ', $t); $t = array(); ?>">
                            <?php $urls = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID(),array($jeweltheme_thumb_width,$jeweltheme_thumb_height)) ); ?>

                            <div class="wp-portfolio-inner">
                                <a title="<?php the_title(); ?>" rel="prettyPhoto[gallery1]" href="<?php echo $urls; ?>">
                                    <img alt="" src="<?php echo $urls; ?>" width="100%" style="height:<?php echo $jeweltheme_thumb_height;?>px;"/>
                                    <span class="wp-portfolio-preview">+</span>
                                </a>
                                <div class="wp-portfolio-desc">
                                    <h4><?php the_title(); ?></h4>                                    
                                    <?php $url=get_post_meta($post->ID, '_url', true);
                                    if($url){
                                        echo "<p><span><a class='url' href=\"$url\" target=\"_blank\">$url</a></span></p>"; } ?>
                                        <?php the_content(); ?>
                                    </div>
                                </div>


                        </div>
                    <?php } ?>

            </div>


                
            

           
    <script type="text/javascript">
        jQuery(function($){

        $("a[rel^='prettyPhoto']").prettyPhoto();

        $('#Container').mixItUp();

        });
    </script>
<?php
get_footer();