<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();


//drop a custom db table
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}fcb_einstellungen" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}lic_key_tbl" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}lic_reg_domain_tbl" );

//note in multisite looping through blogs to delete options on each blog does not scale. You'll just have to leave them.

?>