<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<footer id="colophon" role="contentinfo">
		<div class="site-info">
			<?php do_action( 'twentytwelve_credits' ); ?>
            <?php $url = esc_url(home_url()); ?>
            <?php $purl = parse_url($url); ?> 
			<a href="<?php echo $url ?>" title="<?php echo $purl[host] ?>" class="site-name"><?php printf( 'consked.com' ); ?></a> &copy; 2016
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
