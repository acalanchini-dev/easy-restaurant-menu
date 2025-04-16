<?php
/**
 * Template per la pagina delle opzioni
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/admin/partials
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Gestione del salvataggio delle impostazioni
if (isset($_POST['erm_save_options']) && check_admin_referer('erm_settings_group', 'erm_settings_nonce')) {
    // Recupera e salva le opzioni
    $remove_data = isset($_POST['erm_remove_data_on_uninstall']) ? '1' : '0';
    update_option('erm_remove_data_on_uninstall', $remove_data);
    
    $currency_symbol = sanitize_text_field($_POST['erm_currency_symbol']);
    update_option('erm_currency_symbol', $currency_symbol);
    
    $currency_position = sanitize_key($_POST['erm_currency_position']);
    update_option('erm_currency_position', $currency_position);
    
    $default_layout = sanitize_key($_POST['erm_default_layout']);
    update_option('erm_default_layout', $default_layout);
    
    // Mostra messaggio di conferma
    $message = __('Impostazioni salvate con successo', 'easy-restaurant-menu');
    $message_class = 'notice-success';
}

// Recupera le opzioni correnti
$remove_data = get_option('erm_remove_data_on_uninstall', '0');
$currency_symbol = get_option('erm_currency_symbol', '€');
$currency_position = get_option('erm_currency_position', 'after');
$default_layout = get_option('erm_default_layout', 'list');
?>

<div class="wrap erm-admin">
	<h1><?php echo esc_html__('Easy Restaurant Menu - Impostazioni', 'easy-restaurant-menu'); ?></h1>
	
	<?php if (isset($message)) : ?>
		<div class="notice <?php echo esc_attr($message_class); ?> is-dismissible">
			<p><?php echo esc_html($message); ?></p>
		</div>
	<?php endif; ?>
	
	<div class="erm-options-container">
		<form method="post" action="">
			<?php wp_nonce_field('erm_settings_group', 'erm_settings_nonce'); ?>
			
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="erm_currency_symbol"><?php echo esc_html__('Simbolo Valuta', 'easy-restaurant-menu'); ?></label>
						</th>
						<td>
							<input type="text" id="erm_currency_symbol" name="erm_currency_symbol" value="<?php echo esc_attr($currency_symbol); ?>" class="regular-text">
							<p class="description"><?php echo esc_html__('Il simbolo della valuta da utilizzare con i prezzi (es. €, $)', 'easy-restaurant-menu'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="erm_currency_position"><?php echo esc_html__('Posizione Valuta', 'easy-restaurant-menu'); ?></label>
						</th>
						<td>
							<select id="erm_currency_position" name="erm_currency_position">
								<option value="before" <?php selected($currency_position, 'before'); ?>><?php echo esc_html__('Prima del prezzo (es. € 15.00)', 'easy-restaurant-menu'); ?></option>
								<option value="after" <?php selected($currency_position, 'after'); ?>><?php echo esc_html__('Dopo il prezzo (es. 15.00 €)', 'easy-restaurant-menu'); ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="erm_default_layout"><?php echo esc_html__('Layout Predefinito', 'easy-restaurant-menu'); ?></label>
						</th>
						<td>
							<select id="erm_default_layout" name="erm_default_layout">
								<option value="list" <?php selected($default_layout, 'list'); ?>><?php echo esc_html__('Lista', 'easy-restaurant-menu'); ?></option>
								<option value="grid" <?php selected($default_layout, 'grid'); ?>><?php echo esc_html__('Griglia', 'easy-restaurant-menu'); ?></option>
								<option value="compact" <?php selected($default_layout, 'compact'); ?>><?php echo esc_html__('Compatto', 'easy-restaurant-menu'); ?></option>
							</select>
							<p class="description"><?php echo esc_html__('Il layout predefinito per la visualizzazione del menu', 'easy-restaurant-menu'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<?php echo esc_html__('Pulizia Dati', 'easy-restaurant-menu'); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php echo esc_html__('Pulizia Dati', 'easy-restaurant-menu'); ?></span>
								</legend>
								<label for="erm_remove_data_on_uninstall">
									<input type="checkbox" id="erm_remove_data_on_uninstall" name="erm_remove_data_on_uninstall" value="1" <?php checked($remove_data, '1'); ?>>
									<?php echo esc_html__('Rimuovi tutti i dati quando il plugin viene disinstallato', 'easy-restaurant-menu'); ?>
								</label>
								<p class="description">
									<?php echo esc_html__('Se selezionato, tutte le sezioni e gli elementi del menu verranno eliminati quando il plugin viene disinstallato.', 'easy-restaurant-menu'); ?>
								</p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
			
			<p class="submit">
				<input type="submit" name="erm_save_options" class="button button-primary" value="<?php echo esc_attr__('Salva Impostazioni', 'easy-restaurant-menu'); ?>">
			</p>
		</form>
	</div>
	
	<div class="erm-info-box">
		<h3><?php echo esc_html__('Come usare il shortcode', 'easy-restaurant-menu'); ?></h3>
		<p><?php echo esc_html__('Puoi usare lo shortcode seguente per visualizzare il menu nel tuo sito:', 'easy-restaurant-menu'); ?></p>
		<pre>[restaurant_menu sections="all" layout="list"]</pre>
		
		<h4><?php echo esc_html__('Parametri disponibili:', 'easy-restaurant-menu'); ?></h4>
		<ul>
			<li><code>sections</code>: <?php echo esc_html__('IDs delle sezioni da visualizzare, separati da virgola, o "all" per tutte', 'easy-restaurant-menu'); ?></li>
			<li><code>layout</code>: <?php echo esc_html__('Il layout del menu (list, grid, compact)', 'easy-restaurant-menu'); ?></li>
			<li><code>show_images</code>: <?php echo esc_html__('Mostra o nascondi le immagini (yes/no, default: yes)', 'easy-restaurant-menu'); ?></li>
			<li><code>show_description</code>: <?php echo esc_html__('Mostra o nascondi le descrizioni (yes/no, default: yes)', 'easy-restaurant-menu'); ?></li>
		</ul>
		
		<h3><?php echo esc_html__('Esempi', 'easy-restaurant-menu'); ?></h3>
		<p><?php echo esc_html__('Visualizza solo le sezioni con ID 2 e 4 in layout griglia:', 'easy-restaurant-menu'); ?></p>
		<pre>[restaurant_menu sections="2,4" layout="grid"]</pre>
		
		<p><?php echo esc_html__('Visualizza tutte le sezioni senza descrizioni:', 'easy-restaurant-menu'); ?></p>
		<pre>[restaurant_menu sections="all" show_description="no"]</pre>
	</div>
</div>

<style>
.erm-admin {
	max-width: 1000px;
}

.erm-options-container {
	background: #fff;
	border: 1px solid #c3c4c7;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
	margin: 20px 0;
	padding: 20px;
}

.erm-info-box {
	background: #f0f6fc;
	border: 1px solid #c3c4c7;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
	margin: 20px 0;
	padding: 20px;
}

.erm-info-box h3 {
	margin-top: 0;
	border-bottom: 1px solid #dcdcde;
	padding-bottom: 10px;
}

.erm-info-box h4 {
	margin-bottom: 5px;
}

.erm-info-box pre {
	background: #f6f7f7;
	padding: 10px;
	border: 1px solid #dcdcde;
	overflow: auto;
}

.erm-info-box ul {
	list-style-type: disc;
	padding-left: 20px;
}

.erm-info-box li {
	margin-bottom: 8px;
}

.erm-info-box code {
	background: #f6f7f7;
	padding: 2px 5px;
}
</style>


