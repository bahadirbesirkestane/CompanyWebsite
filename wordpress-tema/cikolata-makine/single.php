<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
the_post();
?>

<div class="wrap page-content" style="max-width:800px;">
	<?php cm_breadcrumb( array(
		array( 'label' => cm__( 'breadcrumb_anasayfa' ), 'url' => home_url( '/' ) ),
		array( 'label' => cm__( 'blog_etiket' ), 'url' => get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ),
		array( 'label' => get_the_title() ),
	) ); ?>

	<h1 class="h-lg" style="margin-top:16px;"><?php the_title(); ?></h1>
	<p class="breadcrumb" style="margin-top:8px;"><?php echo esc_html( get_the_date() ); ?></p>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="ph" style="aspect-ratio:16/7; margin-top:28px;">
			<?php the_post_thumbnail( 'large' ); ?>
			<span class="corner c-tl"></span><span class="corner c-tr"></span><span class="corner c-bl"></span><span class="corner c-br"></span>
		</div>
	<?php endif; ?>

	<div class="body-p" style="max-width:none; margin-top:28px;">
		<?php the_content(); ?>
	</div>
</div>

<?php get_footer(); ?>
