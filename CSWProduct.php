<?php
/*
Plugin Name: CSWProduct
Description: Check stock product
Version: 1.0
Author: jsstoni
License: GPLv2 or later
*/
class CSWProduct
{
	public function __construct()
	{
		add_action('wp_head', array($this, 'css_head'));
		add_action('woocommerce_loop_add_to_cart_link', array($this, 'loop_productStockStatus'), 10, 3);
		add_action('woocommerce_single_product_summary', array($this, 'checkStockStatus'), 30);
		add_action('woocommerce_process_product_meta', array($this, 'save_product_manage_stock'));
		add_filter( 'woocommerce_get_availability', array($this, 'get_availability_custom'), 1, 2);
	}

	public function css_head()
	{
		echo "<style>
		.cswproduct {
			clear: both;
			margin: 10px 0;
		}
		.box {
			width: 22px; height: 22px;
			display: inline-block;
			vertical-align: middle;
		}
		.on {
			background: url('".plugins_url('css_sprites.png', __FILE__ )."') -0 -0;
		}
		.off {
			background: url('".plugins_url('css_sprites.png', __FILE__ )."') -22px -0;
		}
		</style>";
	}

	public function get_quantity()
	{
		$ID = get_the_ID();
		$quantity = get_post_meta( $ID, '_stock', true );
		return $quantity;
	}

	public function noStock($check)
	{
		if ($check) {
			$messagge = "<div class=\"cswproduct\"><div class=\"box on\"></div> ".__('Disponible', 'woocommerce')."</div>";
		}else {
			$messagge = "<div class=\"cswproduct\"><div class=\"box off\"></div> ".__('No hay stock', 'woocommerce')."</div>";
		}
		return $messagge;
	}

	public function checkStockStatus()
	{
		$quantity = $this->get_quantity();
		if (is_single()) {
			if ($quantity > 0) {
				echo $this->noStock(true);
			}else {
				echo $this->noStock(false);
			}
		}
	}

	public function loop_productStockStatus($add_to_cart_html)
	{
		$quantity = $this->get_quantity();
		if ($quantity <= 0) $html = $this->noStock(false) . $add_to_cart_html;
		else $html = $this->noStock(true) . $add_to_cart_html;
		return $html;
	}

	public function save_product_manage_stock($post_id) {
		$product = wc_get_product( $post_id );
		$product->update_meta_data('_manage_stock', 'yes');
		$product->save();
	}

	public function get_availability_custom( $availability, $_product ) {
		$availability['availability'] = '';
		return $availability;
	}
}

new CSWProduct();
?>