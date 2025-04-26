<?php
/**
 * Template per la dashboard amministrativa
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/admin/partials
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Recupera statistiche dal database
global $wpdb;
$table_sections = $wpdb->prefix . 'erm_sections';
$table_items = $wpdb->prefix . 'erm_items';

$total_sections = $wpdb->get_var("SELECT COUNT(*) FROM $table_sections");
$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_items");
$published_sections = $wpdb->get_var("SELECT COUNT(*) FROM $table_sections WHERE status = 'publish'");
$published_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_items WHERE status = 'publish'");

// Sezioni più recenti
$recent_sections = $wpdb->get_results(
    "SELECT * FROM $table_sections ORDER BY data_creazione DESC LIMIT 5",
    ARRAY_A
);

// Elementi più recenti
$recent_items = $wpdb->get_results(
    "SELECT i.*, s.nome as section_name 
    FROM $table_items i 
    LEFT JOIN $table_sections s ON i.section_id = s.id 
    ORDER BY i.data_creazione DESC LIMIT 5",
    ARRAY_A
);
?>

<div class="wrap erm-admin erm-dashboard">
    <h1><?php echo esc_html__('Easy Restaurant Menu - Dashboard', 'easy-restaurant-menu'); ?></h1>
    
    <div class="erm-dashboard-header">
        <div class="erm-welcome-panel">
            <div class="erm-welcome-panel-content">
                <h2><?php echo esc_html__('Benvenuto in Easy Restaurant Menu!', 'easy-restaurant-menu'); ?></h2>
                <p class="about-description"><?php echo esc_html__('Gestisci il menu del tuo ristorante con facilità e mostralo sul tuo sito.', 'easy-restaurant-menu'); ?></p>
                
                <div class="erm-welcome-panel-column-container">
                    <div class="erm-welcome-panel-column">
                        <h3><?php echo esc_html__('Per iniziare:', 'easy-restaurant-menu'); ?></h3>
                        <ul>
                            <li><?php echo esc_html__('1. Crea sezioni del menu (es. Antipasti, Primi, Secondi)', 'easy-restaurant-menu'); ?></li>
                            <li><?php echo esc_html__('2. Aggiungi piatti alle sezioni con prezzi e descrizioni', 'easy-restaurant-menu'); ?></li>
                            <li><?php echo esc_html__('3. Usa il blocco Gutenberg per mostrare il menu', 'easy-restaurant-menu'); ?></li>
                        </ul>
                    </div>
                    <div class="erm-welcome-panel-column">
                        <h3><?php echo esc_html__('Azioni rapide:', 'easy-restaurant-menu'); ?></h3>
                        <ul>
                            <li><a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=erm-sections')); ?>"><?php echo esc_html__('Gestisci Sezioni', 'easy-restaurant-menu'); ?></a></li>
                            <li><a class="button" href="<?php echo esc_url(admin_url('admin.php?page=erm-items')); ?>"><?php echo esc_html__('Gestisci Elementi Menu', 'easy-restaurant-menu'); ?></a></li>
                            <li><a class="button" href="<?php echo esc_url(admin_url('admin.php?page=erm-options')); ?>"><?php echo esc_html__('Impostazioni', 'easy-restaurant-menu'); ?></a></li>
                        </ul>
                    </div>
                    <div class="erm-welcome-panel-column erm-welcome-panel-last">
                        <h3><?php echo esc_html__('Aiuto:', 'easy-restaurant-menu'); ?></h3>
                        <ul>
                            <li><?php echo esc_html__('Per aggiungere il menu a una pagina, usa il blocco "Restaurant Menu" nell\'editor.', 'easy-restaurant-menu'); ?></li>
                            <li><?php echo esc_html__('Puoi ordinare le sezioni e i piatti trascinandoli nelle rispettive pagine di gestione.', 'easy-restaurant-menu'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="erm-dashboard-widgets">
        <div class="erm-dashboard-column">
            <div class="erm-card erm-stats-card">
                <h2 class="erm-card-title"><?php echo esc_html__('Statistiche Menu', 'easy-restaurant-menu'); ?></h2>
                <div class="erm-stats-grid">
                    <div class="erm-stat-item">
                        <span class="erm-stat-value"><?php echo esc_html($total_sections); ?></span>
                        <span class="erm-stat-label"><?php echo esc_html__('Sezioni totali', 'easy-restaurant-menu'); ?></span>
                    </div>
                    <div class="erm-stat-item">
                        <span class="erm-stat-value"><?php echo esc_html($published_sections); ?></span>
                        <span class="erm-stat-label"><?php echo esc_html__('Sezioni pubblicate', 'easy-restaurant-menu'); ?></span>
                    </div>
                    <div class="erm-stat-item">
                        <span class="erm-stat-value"><?php echo esc_html($total_items); ?></span>
                        <span class="erm-stat-label"><?php echo esc_html__('Elementi totali', 'easy-restaurant-menu'); ?></span>
                    </div>
                    <div class="erm-stat-item">
                        <span class="erm-stat-value"><?php echo esc_html($published_items); ?></span>
                        <span class="erm-stat-label"><?php echo esc_html__('Elementi pubblicati', 'easy-restaurant-menu'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="erm-card">
                <h2 class="erm-card-title"><?php echo esc_html__('Sezioni recenti', 'easy-restaurant-menu'); ?></h2>
                <?php if (empty($recent_sections)) : ?>
                    <p class="erm-no-items"><?php echo esc_html__('Nessuna sezione trovata. Crea la tua prima sezione!', 'easy-restaurant-menu'); ?></p>
                <?php else : ?>
                    <table class="erm-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Nome', 'easy-restaurant-menu'); ?></th>
                                <th><?php echo esc_html__('Stato', 'easy-restaurant-menu'); ?></th>
                                <th><?php echo esc_html__('Data', 'easy-restaurant-menu'); ?></th>
                                <th><?php echo esc_html__('Azioni', 'easy-restaurant-menu'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_sections as $section) : ?>
                                <tr>
                                    <td><?php echo esc_html($section['nome']); ?></td>
                                    <td>
                                        <span class="erm-status-indicator <?php echo esc_attr($section['status']); ?>">
                                            <?php echo esc_html($section['status'] === 'publish' ? __('Pubblicato', 'easy-restaurant-menu') : __('Bozza', 'easy-restaurant-menu')); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($section['data_creazione']))); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=erm-sections')); ?>" class="button button-small">
                                            <?php echo esc_html__('Modifica', 'easy-restaurant-menu'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=erm-items&section=' . $section['id'])); ?>" class="button button-small">
                                            <?php echo esc_html__('Elementi', 'easy-restaurant-menu'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <div class="erm-card-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=erm-sections')); ?>" class="button">
                        <?php echo esc_html__('Vedi tutte le sezioni', 'easy-restaurant-menu'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="erm-dashboard-column">
            <div class="erm-card">
                <h2 class="erm-card-title"><?php echo esc_html__('Elementi menu recenti', 'easy-restaurant-menu'); ?></h2>
                <?php if (empty($recent_items)) : ?>
                    <p class="erm-no-items"><?php echo esc_html__('Nessun elemento trovato. Aggiungi il tuo primo piatto!', 'easy-restaurant-menu'); ?></p>
                <?php else : ?>
                    <table class="erm-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Nome', 'easy-restaurant-menu'); ?></th>
                                <th><?php echo esc_html__('Sezione', 'easy-restaurant-menu'); ?></th>
                                <th><?php echo esc_html__('Prezzo', 'easy-restaurant-menu'); ?></th>
                                <th><?php echo esc_html__('Stato', 'easy-restaurant-menu'); ?></th>
                                <th><?php echo esc_html__('Azioni', 'easy-restaurant-menu'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_items as $item) : ?>
                                <tr>
                                    <td><?php echo esc_html($item['titolo']); ?></td>
                                    <td><?php echo esc_html($item['section_name']); ?></td>
                                    <td><?php echo esc_html(number_format((float)$item['prezzo'], 2, ',', ' ')); ?> €</td>
                                    <td>
                                        <span class="erm-status-indicator <?php echo esc_attr($item['status']); ?>">
                                            <?php echo esc_html($item['status'] === 'publish' ? __('Pubblicato', 'easy-restaurant-menu') : __('Bozza', 'easy-restaurant-menu')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=erm-items&section=' . $item['section_id'])); ?>" class="button button-small">
                                            <?php echo esc_html__('Modifica', 'easy-restaurant-menu'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <div class="erm-card-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=erm-items')); ?>" class="button">
                        <?php echo esc_html__('Vedi tutti gli elementi', 'easy-restaurant-menu'); ?>
                    </a>
                </div>
            </div>
            
            <div class="erm-card">
                <h2 class="erm-card-title"><?php echo esc_html__('Come usare il blocco', 'easy-restaurant-menu'); ?></h2>
                <div class="erm-card-content">
                    <ol>
                        <li><?php echo esc_html__('Apri l\'editor di pagine o articoli', 'easy-restaurant-menu'); ?></li>
                        <li><?php echo esc_html__('Cerca il blocco "Restaurant Menu" e inseriscilo', 'easy-restaurant-menu'); ?></li>
                        <li><?php echo esc_html__('Configura le opzioni per scegliere quali sezioni mostrare', 'easy-restaurant-menu'); ?></li>
                        <li><?php echo esc_html__('Personalizza lo stile direttamente nell\'editor', 'easy-restaurant-menu'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.erm-admin {
    max-width: 1200px;
}

.erm-dashboard-header {
    margin-bottom: 20px;
}

.erm-welcome-panel {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin: 20px 0;
    padding: 25px;
    position: relative;
}

.erm-welcome-panel h2 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 24px;
}

.erm-welcome-panel .about-description {
    font-size: 16px;
    margin: 0 0 20px;
}

.erm-welcome-panel-column-container {
    display: flex;
    flex-wrap: wrap;
}

.erm-welcome-panel-column {
    flex: 1;
    min-width: 200px;
    padding-right: 30px;
}

.erm-welcome-panel-column h3 {
    margin-top: 5px;
    font-size: 16px;
}

.erm-welcome-panel-column ul {
    margin: 15px 0;
}

.erm-welcome-panel-column li {
    margin-bottom: 12px;
}

.erm-welcome-panel-column .button {
    margin: 5px 0;
}

.erm-dashboard-widgets {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
}

.erm-dashboard-column {
    flex: 1;
    min-width: 300px;
    padding: 0 10px;
}

.erm-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin-bottom: 20px;
}

.erm-card-title {
    border-bottom: 1px solid #f0f0f1;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    padding: 15px;
}

.erm-card-content {
    padding: 15px;
}

.erm-stats-card {
    background-color: #f0f6fc;
}

.erm-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    padding: 15px;
}

.erm-stat-item {
    background: #fff;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 15px;
    text-align: center;
}

.erm-stat-value {
    display: block;
    font-size: 24px;
    font-weight: 600;
    color: #2271b1;
}

.erm-stat-label {
    display: block;
    margin-top: 5px;
    color: #646970;
}

.erm-table {
    width: 100%;
    border-collapse: collapse;
}

.erm-table th,
.erm-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #f0f0f1;
}

.erm-status-indicator {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.erm-status-indicator.publish {
    background: #edf7ed;
    color: #388e3c;
}

.erm-status-indicator.draft {
    background: #fff3e0;
    color: #f57c00;
}

.erm-no-items {
    padding: 20px 15px;
    text-align: center;
    color: #646970;
}

.erm-card-actions {
    border-top: 1px solid #f0f0f1;
    padding: 15px;
    text-align: right;
}

@media screen and (max-width: 782px) {
    .erm-welcome-panel-column-container {
        flex-direction: column;
    }
    
    .erm-welcome-panel-column {
        padding-right: 0;
        padding-bottom: 20px;
    }
    
    .erm-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style> 