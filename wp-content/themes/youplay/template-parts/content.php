<?php
/**
 * @package Youplay
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class("news-one row"); ?>>
	<div class="col-md-4">
		<?php youplay_post_thumbnail(); ?>
	</div>
	<div class="col-md-8">
		<div class="clearfix">
			<?php the_title( sprintf( '<h3 class="h2 pull-left m-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>

			<?php if ( 'post' == get_post_type() ) : ?>
			<span class="date pull-right">
				<?php youplay_posted_on(); ?>
			</span>
			<?php endif; ?>
		</div>
		<div class="tags">
			<?php youplay_post_tags(); ?>
		</div>
		<div class="description">
			<?php
				the_excerpt();
			?>

			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'youplay' ),
					'after'  => '</div>',
				) );
			?>
		</div>
		<?php youplay_read_more(); ?>

		<?php youplay_entry_footer(); ?>
	</div>
</div>