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
    
    // Salva le nuove opzioni per il formato prezzo
    $price_decimal_separator = sanitize_text_field($_POST['erm_price_decimal_separator']);
    update_option('erm_price_decimal_separator', $price_decimal_separator);
    
    $price_thousand_separator = sanitize_text_field($_POST['erm_price_thousand_separator']);
    update_option('erm_price_thousand_separator', $price_thousand_separator);
    
    $price_decimals = absint($_POST['erm_price_decimals']);
    update_option('erm_price_decimals', $price_decimals);
    
    $price_format_template = sanitize_text_field($_POST['erm_price_format_template']);
    update_option('erm_price_format_template', $price_format_template);
    
    $default_layout = sanitize_key($_POST['erm_default_layout']);
    update_option('erm_default_layout', $default_layout);
    
    // Salva il preset di stile selezionato
    $style_preset = sanitize_key($_POST['erm_style_preset']);
    update_option('erm_style_preset', $style_preset);
    
    // Mostra messaggio di conferma
    $message = __('Impostazioni salvate con successo', 'easy-restaurant-menu');
    $message_class = 'notice-success';
}

// Recupera le opzioni correnti
$remove_data = get_option('erm_remove_data_on_uninstall', '0');
$currency_symbol = get_option('erm_currency_symbol', '€');
$currency_position = get_option('erm_currency_position', 'after');
$price_decimal_separator = get_option('erm_price_decimal_separator', ',');
$price_thousand_separator = get_option('erm_price_thousand_separator', '.');
$price_decimals = get_option('erm_price_decimals', 2);
$price_format_template = get_option('erm_price_format_template', '%s');
$default_layout = get_option('erm_default_layout', 'list');
$style_preset = get_option('erm_style_preset', 'elegante');

// Ottieni i preset di stile disponibili
if (!class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Options')) {
    Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-options.php');
}
$available_presets = EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Options::get_style_presets();

// Ottieni le opzioni per il tempo di scadenza della cache
$cache_expiration_options = EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Options::get_cache_expiration_options();

// Ottieni le statistiche della cache
$cache_stats = EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Cache::get_stats();

// Opzioni di caching
$enable_caching = get_option('erm_enable_caching', true);
$cache_expiration = get_option('erm_cache_expiration', 3600);
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
			
			<h2 class="nav-tab-wrapper">
				<a href="#tab-general" class="nav-tab nav-tab-active"><?php echo esc_html__('Generale', 'easy-restaurant-menu'); ?></a>
				<a href="#tab-currency" class="nav-tab"><?php echo esc_html__('Formato Prezzo', 'easy-restaurant-menu'); ?></a>
				<a href="#tab-style" class="nav-tab"><?php echo esc_html__('Stile', 'easy-restaurant-menu'); ?></a>
				<a href="#tab-cache" class="nav-tab"><?php echo esc_html__('Cache', 'easy-restaurant-menu'); ?></a>
				<a href="#tab-avanzate" class="nav-tab"><?php echo esc_html__('Avanzate', 'easy-restaurant-menu'); ?></a>
			</h2>
			
			<div id="tab-general" class="tab-content active">
				<table class="form-table">
					<tbody>
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
			</div>
			
			<div id="tab-currency" class="tab-content">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="erm_currency_symbol"><?php echo esc_html__('Simbolo Valuta', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<input type="text" id="erm_currency_symbol" name="erm_currency_symbol" value="<?php echo esc_attr($currency_symbol); ?>" class="regular-text price-format-field">
								<p class="description"><?php echo esc_html__('Il simbolo della valuta da utilizzare con i prezzi (es. €, $, £)', 'easy-restaurant-menu'); ?></p>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="erm_currency_position"><?php echo esc_html__('Posizione Valuta', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<select id="erm_currency_position" name="erm_currency_position" class="price-format-field">
									<option value="before" <?php selected($currency_position, 'before'); ?>><?php echo esc_html__('Prima del prezzo (es. € 15.00)', 'easy-restaurant-menu'); ?></option>
									<option value="after" <?php selected($currency_position, 'after'); ?>><?php echo esc_html__('Dopo il prezzo (es. 15.00 €)', 'easy-restaurant-menu'); ?></option>
								</select>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="erm_price_decimal_separator"><?php echo esc_html__('Separatore Decimale', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<select id="erm_price_decimal_separator" name="erm_price_decimal_separator" class="price-format-field">
									<option value="," <?php selected($price_decimal_separator, ','); ?>><?php echo esc_html__('Virgola (,)', 'easy-restaurant-menu'); ?></option>
									<option value="." <?php selected($price_decimal_separator, '.'); ?>><?php echo esc_html__('Punto (.)', 'easy-restaurant-menu'); ?></option>
								</select>
								<p class="description"><?php echo esc_html__('Il carattere che separa la parte decimale del prezzo', 'easy-restaurant-menu'); ?></p>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="erm_price_thousand_separator"><?php echo esc_html__('Separatore Migliaia', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<select id="erm_price_thousand_separator" name="erm_price_thousand_separator" class="price-format-field">
									<option value="." <?php selected($price_thousand_separator, '.'); ?>><?php echo esc_html__('Punto (.)', 'easy-restaurant-menu'); ?></option>
									<option value="," <?php selected($price_thousand_separator, ','); ?>><?php echo esc_html__('Virgola (,)', 'easy-restaurant-menu'); ?></option>
									<option value=" " <?php selected($price_thousand_separator, ' '); ?>><?php echo esc_html__('Spazio ( )', 'easy-restaurant-menu'); ?></option>
									<option value="" <?php selected($price_thousand_separator, ''); ?>><?php echo esc_html__('Nessuno', 'easy-restaurant-menu'); ?></option>
								</select>
								<p class="description"><?php echo esc_html__('Il carattere che separa i gruppi di migliaia nel prezzo', 'easy-restaurant-menu'); ?></p>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="erm_price_decimals"><?php echo esc_html__('Decimali', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<select id="erm_price_decimals" name="erm_price_decimals" class="price-format-field">
									<option value="0" <?php selected($price_decimals, 0); ?>><?php echo esc_html__('0 (es. 15)', 'easy-restaurant-menu'); ?></option>
									<option value="1" <?php selected($price_decimals, 1); ?>><?php echo esc_html__('1 (es. 15,5)', 'easy-restaurant-menu'); ?></option>
									<option value="2" <?php selected($price_decimals, 2); ?>><?php echo esc_html__('2 (es. 15,50)', 'easy-restaurant-menu'); ?></option>
								</select>
								<p class="description"><?php echo esc_html__('Il numero di cifre decimali da mostrare', 'easy-restaurant-menu'); ?></p>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="erm_price_format_template"><?php echo esc_html__('Formato HTML', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<input type="text" id="erm_price_format_template" name="erm_price_format_template" value="<?php echo esc_attr($price_format_template); ?>" class="regular-text price-format-field">
								<p class="description">
									<?php echo esc_html__('Template HTML per il prezzo. Usa %s come segnaposto per il prezzo. Es: <span class="price">%s</span>', 'easy-restaurant-menu'); ?>
								</p>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php echo esc_html__('Anteprima', 'easy-restaurant-menu'); ?>
							</th>
							<td>
								<div class="erm-price-preview">
									<p><?php echo esc_html__('Esempio di prezzo:', 'easy-restaurant-menu'); ?> <strong id="price-preview">€ 1.234,56</strong></p>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div id="tab-style" class="tab-content">
				<h3><?php echo esc_html__('Preset di Stile', 'easy-restaurant-menu'); ?></h3>
				<p class="description"><?php echo esc_html__('Seleziona un preset di stile predefinito per il tuo menu. Questi preset definiscono colori, spaziature e allineamenti del testo.', 'easy-restaurant-menu'); ?></p>
				
				<div class="erm-preset-container">
					<?php foreach ($available_presets as $preset_key => $preset) : ?>
						<div class="erm-preset-option">
							<input type="radio" id="erm_style_preset_<?php echo esc_attr($preset_key); ?>" name="erm_style_preset" value="<?php echo esc_attr($preset_key); ?>" <?php checked($style_preset, $preset_key); ?>>
							<label for="erm_style_preset_<?php echo esc_attr($preset_key); ?>">
								<div class="erm-preset-preview" style="
									background-color: <?php echo esc_attr($preset['background_color']); ?>;
									border: 1px solid <?php echo esc_attr($preset['border_color']); ?>;
									border-radius: <?php echo esc_attr($preset['border_radius']); ?>px;
									padding: 15px;
									text-align: <?php echo esc_attr($preset['text_alignment']); ?>;
								">
									<h4 style="
										color: <?php echo esc_attr($preset['section_title_color']); ?>;
										font-size: <?php echo esc_attr($preset['font_size_title']); ?>em;
										margin-bottom: 10px;
									"><?php echo esc_html($preset['name']); ?></h4>
									<div style="
										display: flex;
										justify-content: <?php echo $preset['text_alignment'] === 'center' ? 'center' : 'space-between'; ?>;
										align-items: baseline;
										margin-bottom: 5px;
									">
										<span style="
											color: <?php echo esc_attr($preset['menu_title_color']); ?>;
											font-weight: bold;
										"><?php echo esc_html__('Elemento Menu', 'easy-restaurant-menu'); ?></span>
										<span style="
											color: <?php echo esc_attr($preset['price_color']); ?>;
											font-weight: bold;
											margin-left: 15px;
										">15,00 €</span>
									</div>
									<p style="
										color: <?php echo esc_attr($preset['description_color']); ?>;
										font-size: <?php echo esc_attr($preset['font_size_description']); ?>em;
										margin-top: 5px;
									"><?php echo esc_html__('Descrizione dell\'elemento del menu con i colori e lo stile del preset.', 'easy-restaurant-menu'); ?></p>
								</div>
								<span class="preset-name"><?php echo esc_html($preset['name']); ?></span>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			
			<div id="tab-cache" class="tab-content">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="erm_enable_caching"><?php echo esc_html__('Abilita caching', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<input type="checkbox" id="erm_enable_caching" name="erm_enable_caching" value="1" <?php checked($enable_caching, true); ?>>
								<p class="description"><?php echo esc_html__('Il caching migliora le prestazioni memorizzando temporaneamente i dati del menu.', 'easy-restaurant-menu'); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="erm_cache_expiration"><?php echo esc_html__('Durata cache', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<select id="erm_cache_expiration" name="erm_cache_expiration" class="price-format-field">
									<?php foreach ($cache_expiration_options as $value => $label) : ?>
										<option value="<?php echo esc_attr($value); ?>" <?php selected($cache_expiration, $value); ?>><?php echo esc_html($label); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php echo esc_html__('Tempo prima che la cache venga aggiornata automaticamente.', 'easy-restaurant-menu'); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__('Statistiche cache', 'easy-restaurant-menu'); ?></th>
							<td>
								<div class="erm-cache-stats">
									<p>
										<strong><?php echo esc_html__('Hit ratio:', 'easy-restaurant-menu'); ?></strong> 
										<?php echo esc_html($cache_stats['hit_ratio']); ?>% 
										(<?php echo esc_html($cache_stats['hits']); ?> <?php echo esc_html__('hit', 'easy-restaurant-menu'); ?> / 
										<?php echo esc_html($cache_stats['total']); ?> <?php echo esc_html__('totale', 'easy-restaurant-menu'); ?>)
									</p>
									<p>
										<strong><?php echo esc_html__('Ultimo svuotamento:', 'easy-restaurant-menu'); ?></strong> 
										<?php echo esc_html($cache_stats['last_flush']); ?>
									</p>
									<button type="button" id="erm-flush-cache" class="button">
										<?php echo esc_html__('Svuota cache', 'easy-restaurant-menu'); ?>
									</button>
									<span id="erm-cache-result" class="erm-cache-result"></span>
									<script>
									jQuery(document).ready(function($) {
										$('#erm-flush-cache').on('click', function(e) {
											e.preventDefault();
											
											var $button = $(this);
											var $result = $('#erm-cache-result');
											
											$button.prop('disabled', true);
											$result.html('<?php echo esc_js(__('Svuotamento in corso...', 'easy-restaurant-menu')); ?>');
											
											$.ajax({
												url: ajaxurl,
												type: 'POST',
												data: {
													action: 'erm_flush_cache',
													nonce: '<?php echo wp_create_nonce('erm_admin_nonce'); ?>'
												},
												success: function(response) {
													if (response.success) {
														$result.html('<span class="success">' + response.data.message + '</span>');
													} else {
														$result.html('<span class="error">' + response.data.message + '</span>');
													}
												},
												error: function() {
													$result.html('<span class="error"><?php echo esc_js(__('Si è verificato un errore durante lo svuotamento della cache.', 'easy-restaurant-menu')); ?></span>');
												},
												complete: function() {
													$button.prop('disabled', false);
													
													// Aggiorna le statistiche dopo un breve ritardo
													setTimeout(function() {
														location.reload();
													}, 2000);
												}
											});
										});
									});
									</script>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div id="tab-avanzate" class="tab-content">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="erm_remove_data_on_uninstall"><?php echo esc_html__('Rimuovi dati alla disinstallazione', 'easy-restaurant-menu'); ?></label>
							</th>
							<td>
								<input type="checkbox" id="erm_remove_data_on_uninstall" name="erm_remove_data_on_uninstall" value="1" <?php checked($remove_data, '1'); ?>>
								<p class="description"><?php echo esc_html__('Se selezionato, tutti i dati (tabelle, opzioni) saranno rimossi quando il plugin viene disinstallato.', 'easy-restaurant-menu'); ?></p>
								<p class="warning"><?php echo esc_html__('Attenzione: questa operazione è irreversibile!', 'easy-restaurant-menu'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
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

/* Stili per i tab */
.nav-tab-wrapper {
	margin-bottom: 20px;
}

.tab-content {
	display: none;
}

.tab-content.active {
	display: block;
}

/* Stili per l'anteprima dei preset */
.erm-preset-container {
	display: flex;
	flex-wrap: wrap;
	gap: 20px;
	margin-top: 20px;
}

.erm-preset-option {
	width: calc(33.333% - 20px);
	min-width: 200px;
}

.erm-preset-option input[type="radio"] {
	display: none;
}

.erm-preset-option label {
	display: block;
	cursor: pointer;
}

.erm-preset-preview {
	border: 1px solid #ddd;
	margin-bottom: 8px;
	transition: all 0.3s ease;
}

.erm-preset-option input[type="radio"]:checked + label .erm-preset-preview {
	box-shadow: 0 0 0 2px #007cba;
}

.preset-name {
	display: block;
	text-align: center;
	font-weight: bold;
	margin-top: 5px;
}

.erm-price-preview {
	background: #f9f9f9;
	padding: 15px;
	border: 1px solid #ddd;
	border-radius: 4px;
}

.erm-cache-stats {
	background: #f8f8f8;
	padding: 10px 15px;
	border-radius: 4px;
	border: 1px solid #e0e0e0;
}

.erm-cache-result {
	display: inline-block;
	margin-left: 10px;
	font-style: italic;
}

.erm-cache-result .success {
	color: green;
}

.erm-cache-result .error {
	color: red;
}

.warning {
	color: #d63638;
	font-weight: 500;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Gestione dei tab
	$('.nav-tab').on('click', function(e) {
		e.preventDefault();
		
		// Rimuovi la classe active da tutti i tab e dai contenuti
		$('.nav-tab').removeClass('nav-tab-active');
		$('.tab-content').removeClass('active');
		
		// Aggiungi la classe active al tab cliccato e al contenuto corrispondente
		$(this).addClass('nav-tab-active');
		$($(this).attr('href')).addClass('active');
	});
	
	// Aggiorna l'anteprima del prezzo quando cambiano i campi
	function updatePricePreview() {
		var price = 1234.56;
		var symbol = $('#erm_currency_symbol').val();
		var position = $('#erm_currency_position').val();
		var decimalSeparator = $('#erm_price_decimal_separator').val();
		var thousandSeparator = $('#erm_price_thousand_separator').val();
		var decimals = parseInt($('#erm_price_decimals').val());
		var template = $('#erm_price_format_template').val();
		
		// Formatta il prezzo
		var formattedPrice = price.toFixed(decimals);
		var parts = formattedPrice.split('.');
		var integerPart = parts[0];
		var decimalPart = parts.length > 1 ? parts[1] : '';
		
		// Aggiungi separatore migliaia
		if (thousandSeparator !== '') {
			integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
		}
		
		// Ricomponi il prezzo con il separatore decimale
		var finalPrice = integerPart;
		if (decimals > 0) {
			finalPrice += decimalSeparator + decimalPart;
		}
		
		// Applica posizione simbolo valuta
		if (position === 'before') {
			finalPrice = symbol + ' ' + finalPrice;
		} else {
			finalPrice = finalPrice + ' ' + symbol;
		}
		
		// Applica template se presente
		if (template !== '' && template !== '%s') {
			finalPrice = template.replace('%s', finalPrice);
		}
		
		// Aggiorna l'anteprima
		$('#price-preview').html(finalPrice);
	}
	
	// Aggiorna l'anteprima quando cambiano i campi
	$('.price-format-field').on('change keyup', updatePricePreview);
	
	// Aggiorna l'anteprima all'avvio
	updatePricePreview();
});
</script>


