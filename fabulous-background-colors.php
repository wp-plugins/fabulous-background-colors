<?php
/**
 * Plugin Name: Fabulous Background Colors
 * Plugin URI: http://www.fabulous.digital
 * Description: This Plugin adds colored stripes width random colors and smooth color transitions to the background of your website
 * Version: 1.0.0
 * Author: Matthias Ulrich
 * Author URI: http://www.fabulous.digital
 * License: GPL2
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('FCB_SECRET_KEY', '553f5b4c785057.32787571'); 
define('LICENSE_SERVER_URL', 'http://www.fabulous.digital');
define('FCB_ITEM_REFERENCE', 'Fabulous Background Colors');

$erlaubt = false;
$feld = "";

function sichtbarmachen(){
	global $erlaubt;
    if ($erlaubt == true){
    	global $feld;
    	$feld = "";
    } else{
    	global $feld;
    	$feld = 'disabled="disabled"';			
    }
}

function standartwerte_setzen(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'fcb_einstellungen';
	
	$wert_var = '100';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 1) );

	$wert_var = '255';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 2) );
	
	$wert_var = '255';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 3) );
	
	$wert_var = '255';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 4) );
	
	$wert_var = '0';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 5) );
	
	$wert_var = '0';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 6) );
	
	$wert_var = '0';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 7) );
	
	$wert_var = '5';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 8) );
	
	$wert_var = '0';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 9) );
	
	$wert_var = 'body';
	$wpdb ->update($table_name, array('wert' => $wert_var), array('id' => 10) );	
}

function sample_license_management_page() {
    echo '<div class="wrap">';
    echo '<h2>Fabulous Background Colors | Options</h2>';
	echo '<h3>License Key</h3>';
	
    /*** License activate button was clicked ***/
    if (isset($_REQUEST['activate_license'])) {
 		$license_key = $_REQUEST['fcb_license_key'];
        
        // API query parameters
        $api_params = array(
            'slm_action' => 'slm_activate',
            'secret_key' => FCB_SECRET_KEY,
            'license_key' => $license_key,
            'registered_domain' => $_SERVER['SERVER_NAME'],
            'item_reference' => urlencode(FCB_ITEM_REFERENCE),
        );

        // Send query to the license manager server
        $response = wp_remote_get(add_query_arg($api_params, LICENSE_SERVER_URL), array('timeout' => 20, 'sslverify' => false));

        // Check for error in the response
        if (is_wp_error($response)){
            echo "Unexpected Error! The query returned with an error.";
        }
        
        // License data.
        $license_data = json_decode(wp_remote_retrieve_body($response));
        
        // TODO - Do something with it.
        
        if($license_data->result == 'success'){
            global $erlaubt;
			$erlaubt = true;
            echo '<br />'.$license_data->message;
            //Save the license key in the options table
            update_option('fcb_license_key', $license_key); 
        }else{
            echo '<br />'.$license_data->message;
        }
    }
    /*** End of license activation ***/
    
    /*** License deactivate button was clicked ***/
    if (isset($_REQUEST['deactivate_license'])) {
        $license_key = $_REQUEST['fcb_license_key'];

        // API query parameters
        $api_params = array(
            'slm_action' => 'slm_deactivate',
            'secret_key' => FCB_SECRET_KEY,
            'license_key' => $license_key,
            'registered_domain' => $_SERVER['SERVER_NAME'],
            'item_reference' => urlencode(FCB_ITEM_REFERENCE),
        );

        // Send query to the license manager server
        $response = wp_remote_get(add_query_arg($api_params, LICENSE_SERVER_URL), array('timeout' => 20, 'sslverify' => false));

        // Check for error in the response
        if (is_wp_error($response)){
            echo "Unexpected Error! The query returned with an error.";
        }
        
        // License data.
        $license_data = json_decode(wp_remote_retrieve_body($response));
        
        // TODO - Do something with it.
        
        if($license_data->result == 'success'){//Success was returned for the license activation
            
            echo '<br />'.$license_data->message;
            $erlaubt = false;
			sichtbarmachen();
			standartwerte_setzen();
            update_option('fcb_license_key', '');
		}else{
            echo '<br />'.$license_data->message;
        }      
    }
    
    global $wpdb;
	$standart = $wpdb->get_row("SELECT * FROM wp_options WHERE option_name Like '%fcb_license_key%'", ARRAY_A);
	$akt = $standart['option_value'];
	if($akt==""){
		echo ("<p>Please enter the license key for this product to activate the options. <br />You were given a license key when you purchased this item.</br>You can purchase the plugin <a href='http://www.fabulous.digital' target='_blank'>here</a>.</p>");
	}
 ?>	
    
    <form action="" method="post">
        <table class="form-table">
            <tr>
            <th scope="row" style="width:150px;"><label for="fcb_license_key">License Key</label></th>
            <td><input class="regular-text" type="text" id="fcb_license_key" name="fcb_license_key"  value="<?php echo get_option('fcb_license_key'); ?>" ></td>
            
        	<td></tr></table>
        		<div class="submit">
            <input type="submit" name="activate_license" value="Activate" class="button-primary" />
            <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
        </div>
             
    </form>
<?php    
    echo '</div>';
	
	global $wpdb;
	$standart = $wpdb->get_row("SELECT * FROM wp_options WHERE option_name Like '%fcb_license_key%'", ARRAY_A);
	//$standart['option_value'];
	$akt = $standart['option_value'];
	if($akt==""){
		//echo ("deaktiviert");
		sichtbarmachen();
	}	
}

function jal_install() {
	global $wpdb;
	global $jal_db_version; // muss ich noch machen

	$table_name = $wpdb->prefix . 'fcb_einstellungen';
	$charset_collate = $wpdb->get_charset_collate();

	if ($wpdb -> get_var("SHOW TABLES LIKE '$table_name'")!=$table_name){
		$sql = "CREATE TABLE $table_name (
  			id mediumint(9) NOT NULL AUTO_INCREMENT,
			einstellung tinytext NOT NULL,
			wert int NULL,
			text text NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
	jal_install_data();
	}	
}

/*Einstellungen das erste mal in die Datenbank füllen*/
function jal_install_data() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'fcb_einstellungen';

	$einstellung_var = 'QuadratBreite';
	$wert_var = '100';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );

	$einstellung_var = 'MaxRot';
	$wert_var = '255';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );
	
	$einstellung_var = 'MaxGruen';
	$wert_var = '255';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );
	
	$einstellung_var = 'MaxBlau';
	$wert_var = '255';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );
	
	
	$einstellung_var = 'MinRot';
	$wert_var = '0';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );
	
	$einstellung_var = 'MinGruen';
	$wert_var = '0';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );
	
	$einstellung_var = 'MinBlau';
	$wert_var = '0';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );
	
	$einstellung_var = 'Fade';
	$wert_var = '5';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );
	
	$einstellung_var = 'Ausrichtung';
	$wert_var = '0';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'wert' => $wert_var) );	
	
	$einstellung_var = 'Dom_Abfrage';
	$wert_var = 'body';
	$wpdb->insert($table_name, array( 'einstellung' => $einstellung_var,'text' => $wert_var) );		
}
register_activation_hook( __FILE__, 'jal_install' );

/*Skript einbinden und Wert in Option-Page übergeben*/
function fcb_load_scripts() {
	global $wpdb;
	global $dbQB;
	global $dbMaxRot;
	global $dbMaxGruen;
	global $dbMaxBlau;
	global $dbMinRot;
	global $dbMinGruen;
	global $dbMinBlau;
	global $dbFade;
	global $dbAusrichtung;
	global $dbDomAbfrage;
	
	$table_name = $wpdb->prefix . 'fcb_einstellungen';
	$dbQB = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='QuadratBreite'", ARRAY_A);
	$dbMaxRot = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MaxRot'", ARRAY_A);
	$dbMaxGruen = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MaxGruen'", ARRAY_A);
	$dbMaxBlau = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MaxBlau'", ARRAY_A);
	$dbMinRot = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MinRot'", ARRAY_A);
	$dbMinGruen = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MinGruen'", ARRAY_A);
	$dbMinBlau = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MinBlau'", ARRAY_A);
	$dbFade = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='Fade'", ARRAY_A);
	$dbAusrichtung = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='Ausrichtung'", ARRAY_A);
	$dbDomAbfrage = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='Dom_Abfrage'", ARRAY_A);
	
		
	$einstellungen_array = array(
			'quadrat_breite' => $dbQB['wert'],
			'rotMax' => $dbMaxRot['wert'],
			'gruenMax' => $dbMaxGruen['wert'],
			'blauMax' => $dbMaxBlau['wert'],
			'rotMin' => $dbMinRot['wert'],
			'gruenMin' => $dbMinGruen['wert'],
			'blauMin' => $dbMinBlau['wert'],
			'fade' => $dbFade['wert'],
			'ausrichtung' => $dbAusrichtung['wert'],
			'dom_abfrage' => $dbDomAbfrage['text']
			);
	
	wp_enqueue_script('fcb-script', '/wp-content/plugins/fabulous-background-colors/js/fabulous-background-colors.min.js');
 	wp_localize_script('fcb-script', 'fcb_script_vars', $einstellungen_array);
}
add_action('wp_enqueue_scripts', 'fcb_load_scripts');

/*OptionenSeite*/
function fcb_plugin_menu() {
	//Browserkarteikarte, Titel im Menü, Berechtigung, interner Name, Funktion welche die Optionenleiste ausgibt
	add_menu_page( 'Fabulous Background Colors Options', 'Fabulous Background Colors', 'manage_options', 'fcb_menu_slug', 'fcb_plugin_options' );
		}
add_action( 'admin_menu', 'fcb_plugin_menu' );

/*Optionenseite und Datenbank update*/
function fcb_plugin_options() {
	sample_license_management_page();
			
	if ( !current_user_can( 'manage_options' ) )  {wp_die( __( 'You do not have sufficient permissions to access this page.' ) );}
	
	if ($_REQUEST['page'] == isset($_POST['submit_einstellungen'])){
		$neue_breite = ($_POST['neue_qb']);
		if (!empty($neue_breite)){
			global $wpdb; global $table_name;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$wpdb ->update($table_name, array('wert' => $neue_breite), array('id' => 1) );
		}

		global $wpdb; global $table_name;
		$table_name = $wpdb->prefix . 'fcb_einstellungen';
			
		$neues_max_rot = ($_POST['neues_max_rot']);
		$wpdb ->update($table_name, array('wert' => $neues_max_rot), array('id' => 2) );
		
		$neues_max_gruen = ($_POST['neues_max_gruen']);
		$wpdb ->update($table_name, array('wert' => $neues_max_gruen), array('id' => 3) );
	
		$neues_max_blau = ($_POST['neues_max_blau']);
		$wpdb ->update($table_name, array('wert' => $neues_max_blau), array('id' => 4) );
		
		$neues_min_rot = ($_POST['neues_min_rot']);
		$wpdb ->update($table_name, array('wert' => $neues_min_rot), array('id' => 5) );
		
		$neues_min_gruen = ($_POST['neues_min_gruen']);
		$wpdb ->update($table_name, array('wert' => $neues_min_gruen), array('id' => 6) );
		
		$neues_min_blau = ($_POST['neues_min_blau']);
		$wpdb ->update($table_name, array('wert' => $neues_min_blau), array('id' => 7) );
				
		
		$neuer_fade = ($_POST['neuer_fade']);
		if (!empty($neuer_fade)){
			global $wpdb; global $table_name;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$wpdb ->update($table_name, array('wert' => $neuer_fade), array('id' => 8) );
		}		

		$neue_domabfrage = ($_POST['neue_domabfrage']);
		if (!empty($neue_domabfrage)){
			global $wpdb; global $table_name;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$wpdb ->update($table_name, array('text' => $neue_domabfrage), array('id' => 10) );
		}				
		
		$neue_ausrichtung = ($_POST['neue_ausrichtung']);
			global $wpdb; global $table_name;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$answer = $_POST['neue_ausrichtung'];  
			if ($answer == "vertical") {
				$ausrichtung2 = 1;          
				$wpdb ->update($table_name, array('wert' => $ausrichtung2), array('id' => 9) );
			} else {
				$ausrichtung2 = 0;
				$wpdb ->update($table_name, array('wert' => $ausrichtung2), array('id' => 9) );
			}          
		}
	?>	
	<div class="wrap"><h3>Basic Options</h3>
	<form method="post" action="">
		<table class="form-table">
			<tr>
			<th scope="row"><label>Fade Duration:</label></th>
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbFade = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='Fade'", ARRAY_A);
/*			echo"<input name='neuer_fade' type='number' value='".$dbFade['wert']."'  min='0' max='4000'";print $feld; echo"> Seconds";?>*/
			echo"<input name='neuer_fade' type='number' value='".$dbFade['wert']."'  min='0' max='4000'> Seconds";?>
			<p class="description">Default 5 Seconds</p></td></tr>
			<?php
			
			global $wpdb;
		$standart = $wpdb->get_row("SELECT * FROM wp_options WHERE option_name Like '%fcb_license_key%'", ARRAY_A);
	$akt = $standart['option_value'];
	if($akt==""){
			
			echo "<tr><th scope='row' colspan='2'>The following options are available in the Pro Version</th></tr>";
	}
			?>	
			<tr>
			<th scope="row"><label>Orientation:</label></th>
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbAusrichtung = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='Ausrichtung'", ARRAY_A);
			echo"<fieldset>
			<input type='radio' id='vertical' name='neue_ausrichtung' value='vertical' "; print $feld;
			if ($dbAusrichtung['wert']==1){
				echo 'checked';} 
			echo "><label for='neue_ausrichtung'> Vertical</label>
			<input type='radio' id='horizontal' name='neue_ausrichtung' value='horizontal'"; print $feld;
			if ($dbAusrichtung['wert']==0) {
				echo 'checked';} 
			echo "><label for='neue_ausrichtung'> Horizontal</label>
			</fieldset>";
			?>
			<p class="description"> </p></td></tr>
			
			<tr>
			<th scope="row"><label>Stripe width:</label></th>	
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbQB = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='QuadratBreite'", ARRAY_A);
			echo"<input name='neue_qb' type='number' value='".$dbQB['wert']."'  min='1' max='4000'"; print $feld; echo"> Pixel";?>
			<p class="description">Default 100 Pixel</p></td></tr>

			<tr>
			<th scope="row"><label>Where in the DOM:</label></th>
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbDomAbfrage = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='Dom_Abfrage'", ARRAY_A);
			echo"<input name='neue_domabfrage' type='text' value='".$dbDomAbfrage['text']."'  min='0' max='40'"; print $feld;echo">";?>
			<p class="description">Default body</p></td></tr>
		</table>
	</div>
	<div class="wrap">
		<h3>Advanced Color Options</h3>
		<table class="form-table">
			<tr>
			<th scope="row"><label>Maximum Red:</label></th>	
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbMaxRot = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MaxRot'", ARRAY_A);
			echo"<input name='neues_max_rot' type='number' value='".$dbMaxRot['wert']."'  min='0' max='255'"; print $feld; echo">";?>
			<p class="description">Default 255</p></td></tr>

			<tr>
			<th scope="row"><label>Maximum Green:</label></th>	
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbMaxGruen = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MaxGruen'", ARRAY_A);
			echo"<input name='neues_max_gruen' type='number' value='".$dbMaxGruen['wert']."'  min='0' max='255'"; print $feld; echo">";?>
			<p class="description">Default 255</p></td></tr>

			<tr>
			<th scope="row"><label>Maximum Blue:</label></th>	
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbMaxBlau = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MaxBlau'", ARRAY_A);
			echo"<input name='neues_max_blau' type='number' value='".$dbMaxBlau['wert']."'  min='0' max='255'"; print $feld; echo">";?>
			<p class="description">Default 255</p></td></tr>
	
			<tr>
			<th scope="row"><label>Minimum Red:</label></th>	
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbMinRot = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MinRot'", ARRAY_A);
			echo"<input name='neues_min_rot' type='number' value='".$dbMinRot['wert']."'  min='0' max='255'"; print $feld; echo">";?>
			<p class="description">Default 0</p></td></tr>

			<tr>
			<th scope="row"><label>Minimum Green:</label></th>	
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbMinGruen = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MinGruen'", ARRAY_A);
			echo"<input name='neues_min_gruen' type='number' value='".$dbMinGruen['wert']."'  min='0' max='255'"; print $feld; echo">";?>
			<p class="description">Default 0</p></td></tr>

			<tr>
			<th scope="row"><label>Minimum Blue:</label></th>	
			<td><?php
			global $wpdb;
			global $table_name;
			global $feld;
			$table_name = $wpdb->prefix . 'fcb_einstellungen';
			$dbMinBlau = $wpdb->get_row("SELECT * FROM $table_name WHERE einstellung='MinBlau'", ARRAY_A);
			echo"<input name='neues_min_blau' type='number' value='".$dbMinBlau['wert']."'  min='0' max='255'"; print $feld; echo">";?>
			<p class="description">Default 0</p></td></tr>
			</table>
			<p class="submit"><input name="submit_einstellungen" type="submit" class="button-primary" value="Save Changes"> </p>
	</form>
</div>	

<?php
}

function make_style(){
	$url = home_url('/wp-content/plugins/fabulous-background-colors/css/style.css');
    echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}  
add_action('wp_head','make_style');

function customAdmin() {
    $url = home_url('/wp-content/plugins/fabulous-background-colors/css/admin_style.css');
    echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}
add_action('admin_head', 'customAdmin');
?>