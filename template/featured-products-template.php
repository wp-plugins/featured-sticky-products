<?php
global $wpsc_query, $wpdb;
$image_width = get_option('product_image_width');
$image_height = get_option('product_image_height');
?>
<div class="wpsc_container wpsc_featured">
	<?php if(wpsc_display_products()): ?>
	
		<div class="product_grid_display">
		<?php while (wpsc_have_products()) :  wpsc_the_product(); ?>
			<div class="product_grid_item product_view_<?php echo wpsc_the_product_id(); ?>">
			
				<div class="item_text">
						<h3>
							<a href='<?php echo wpsc_the_product_permalink(); ?>'><?php echo wpsc_the_product_title(); ?></a>
						</h3> 
						<div class="pricedisplay"><?php echo wpsc_the_product_price(true); ?></div> 
						<div class='wpsc_description'>
							<?php echo wpsc_the_product_additional_description(); ?>
							<a href='<?php echo wpsc_the_product_permalink(); ?>'>
							  More Information&hellip;
							</a>
						</div>
				</div>
			
				<?php if(wpsc_the_product_thumbnail()) :?> 	   
					<div class="item_image">
						<a href="<?php echo wpsc_the_product_permalink(); ?>" style='background-image: url(<?php echo wpsc_the_sticky_image(wpsc_the_product_id()); ?>);'>
						</a>
					</div>
				<?php else: ?> 
					<div class="item_no_image">
						<a href="<?php echo wpsc_the_product_permalink(); ?>">
						<span>No Image Available</span>
						</a>
					</div>
				<?php endif; ?>
				<div class="wpsc_clear"></div>
			</div>
			
		<?php endwhile; ?>
	</div>
	<?php endif; ?>
</div>