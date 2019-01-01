<?php
/*
*Plugin Name: Smart Task
Plugin URI: http://your-website.com
*Description: Learning Wordpress
*Version: 1.0
*Author: us
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

if (!defined('ABSPATH')) exit;

// constant to plugin url (use to load assets)
define('SMARTTASK_PLUGIN_URL', plugin_dir_url(__FILE__));

/*loading assets (css, js etc...)*/
add_action('wp_enqueue_scripts', 'addSmartTaskAssets', 1);
function addSmartTaskAssets()
{

    wp_enqueue_script('smarttask-script', SMARTTASK_PLUGIN_URL . 'assets/js/scripts.js', array('jquery'), null, true);

    wp_localize_script('smarttask-script', 'SmartObj', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('countryListNonce'),
    ));

}

/*generate the html*/
add_shortcode('smartTaskForm', 'smartTaskForm');
function smartTaskForm()
{
    ob_start();
    global $wpdb;
    $countries = [];

    $sql = 'SELECT * FROM ' . $wpdb->prefix . 'smarttask_countries';
    $result = $wpdb->get_results($sql);

    if ($result) {
        $countries = $result;
    }

    include_once plugin_dir_path(__FILE__) . 'smartTaskForm.php';
    return ob_get_clean();
}

/*execute where plugin activated*/
register_activation_hook(__FILE__, 'smartTaskStart');
function smartTaskStart()
{

    global $wpdb;
    global $jal_db_version;

    $table_name = $wpdb->prefix . 'smarttask_countries';
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		country_code varchar(4) NOT NULL,
		country varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    dbDelta($sql);
    /****************/
    $table_name = $wpdb->prefix . 'smarttask_countries_info';
    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		country_id mediumint(9) NOT NULL,
		place varchar(255) NOT NULL,
		latitude varchar(255) NOT NULL,
		`name` varchar(255) NOT NULL,
		longitude varchar(255) NOT NULL,
		zip varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    dbDelta($sql);

    add_option('jal_db_version', $jal_db_version);
}

/*execute where plugin activated - insert country's right after database create*/
register_activation_hook(__FILE__, 'addSupportedCountries');
function addSupportedCountries()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'smarttask_countries';

    $countries = [
        'AD' => 'Andorra',
        'AR' => 'Argentina',
        'AS' => 'American Samoa',
        'AT' => 'Austria',
        'AU' => 'Australia',
        'BD' => 'Bangladesh',
        'BE' => 'Belgium',
        'BG' => 'Bulgaria',
        'BR' => 'Brazil',
        'CA' => 'Canada',
    ];

    //clear table to prevent duplicates
    $wpdb->query('TRUNCATE TABLE ' . $table_name);
    foreach ($countries as $key => $value) {
        $wpdb->insert(
            $table_name,
            array(
                'id' => '',
                'country_code' => $key,
                'country' => $value,
            )
        );
    }

}

/*wordpress ajax api*/
add_action('wp_ajax_searchCountry', 'searchCountry');
/*allow everyone to access this function threw ajax*/
add_action('wp_ajax_nopriv_searchCountry', 'searchCountry');
function searchCountry()
{
    // validate its not robot
    check_ajax_referer('countryListNonce', 'countryListNonce');

    global $wpdb;
    $json = [];

    if (isset($_POST['zipCode']) && !empty($_POST['zipCode'])) {
        $data['zipCode'] = esc_attr($_POST['zipCode']);
        if (isset($_POST['countryChosen']) &&
            !empty($_POST['countryChosen']) &&
            is_numeric($_POST['countryChosen'])) {
            $countryChosen = esc_attr($_POST['countryChosen']);
            if (validateCountry($countryChosen)) {

                $data['countryChosen'] = $countryChosen;
                $sql = 'SELECT * FROM ' . $wpdb->prefix . 'smarttask_countries_info WHERE ';
                $sql .= 'country_id = %s AND ';
                $sql .= 'zip = %s';

                $result = $wpdb->get_results($wpdb->prepare($sql, $data['countryChosen'], $data['zipCode']));
                if ($result) {
                    $json['success'] = $result;
                } else {
                    $json['not_found'] = true;
                }
            } else {
                $json['error'] = 'Country Not Found.';
            }
        } else {
            $json['error'] = 'Country Error Found.';
        }
    } else {
        $json['error'] = 'Zip Code Error Found.';
    }

    header('Content-type: application/json');
    echo json_encode($json);
    die;
}

/*execute if country not found on database*/
add_action('wp_ajax_add_new_country', 'add_new_country');
add_action('wp_ajax_nopriv_add_new_country', 'add_new_country');
function add_new_country()
{

    $json = [];
    global $wpdb;
    if (isset($_POST['places']) && is_array($_POST['places']) && count($_POST['places']) > 0) {

        if (isset($_POST['zipCode']) && !empty($_POST['zipCode'])) {
            $data['zipCode'] = esc_attr($_POST['zipCode']);
        } else {
            $data['zipCode'] = '';
        }

        if (isset($_POST['countryID']) && !empty($_POST['countryID']) && is_numeric($_POST['countryID'])) {
            $data['countryID'] = esc_attr($_POST['countryID']);
        } else {
            $data['countryID'] = '';
        }

        $places = $_POST['places']['places'];
        $table = $wpdb->prefix . 'smarttask_countries_info';

        foreach ($places as $place) {

            $data = array(
                'id' => '',
                'country_id' => esc_attr($data['countryID']),
                'place' => esc_attr($place['place name']),
                'latitude' => esc_attr($place['latitude']),
                'longitude' => esc_attr($place['longitude']),
                'name' => esc_attr($place['place name']),
                'zip' => $data['zipCode'],
            );

            $format = array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            );

            $wpdb->insert($table, $data, $format);
            $my_id = $wpdb->insert_id;
            if (!$my_id) {
                $json['error'][] = $place['place name'];
            }
        }

        if (!isset($json['error'])) {
            $json['success'] = 'Info saved Successfully.';
        }

    }

    header('Content-type: application/json');
    echo json_encode($json);
    die;
}

/*check if the country id exist on my database*/
function validateCountry($countryID)
{
    global $wpdb;
    $return = false;

    $sql = 'SELECT id FROM ' . $wpdb->prefix . 'smarttask_countries WHERE ';
    $sql .= 'id = %s';
    if ($result = $wpdb->get_var($wpdb->prepare($sql, $countryID)))
        $return = $result;

    return $return;
}