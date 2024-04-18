<?php
/*
Plugin Name: Rest api form plugin
Description: Adds a verification form to your WordPress site.
Version: 2.0
Author: Abhyan Morkal
*/

// Start session
session_start();

// Create custom table on plugin activation
function create_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_register';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        city varchar(255) NOT NULL,
        phone varchar(20) NOT NULL,
        otp varchar(10) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'create_custom_table' );

// Add shortcode to display the form
function verification_form_shortcode() {
    if (isset($_SESSION['verification_form_submitted']) && $_SESSION['verification_form_submitted'] === true) {
        // Display "Thank you" message
        $output = '<section class="wrapper">';
        $output .= '<p>Thank you for your submission!</p>';
        $output .= '</section>';
        // Unset session variable
        unset($_SESSION['verification_form_submitted']);
    } else {
        ob_start();
        ?>
<section class="wrapper">
  <form id="verification-form" action="" class="form" method="post">

    <div class="input-box form-group">
      <input type="text" id="name" name="name" placeholder="Name" required />
    </div>
    <div class="input-box">
      <input type="email" id="email" name="email" placeholder="Email" required />
    </div>
    <div class="input-box">
      <input type="text" id="city" name="city" placeholder="City" required />
    </div>
    <div class="input-box phone">
      <input type="tel" name="phone" id="phone" placeholder="Phone" required />
      <button type="button" id="verify-otp-btn">SEND OTP</button>
    </div>
    <div id="otp-section" style="display: none">
      <div id="otp-con">
        <div class="input-box otp">
          <input type="text" id="otp1" name="otp1" maxlength="1" required />
        </div>
        <div class="input-box otp">
          <input type="text" id="otp2" name="otp2" maxlength="1" required />
        </div>
        <div class="input-box otp">
          <input type="text" id="otp3" name="otp3" maxlength="1" required />
        </div>
        <div class="input-box otp">
          <input type="text" id="otp4" name="otp4" maxlength="1" required />
        </div>
        <button type="submit" id="submit-form-btn">SUBMIT</button>
      </div>
    </div>
    <div id="error-message"></div> <!-- Error message container -->
  </form>
</section>
<?php
        $output = ob_get_clean();
    }
    return $output;
}
add_shortcode('verification_form2', 'verification_form_shortcode');

// Enqueue scripts and styles
function verification_form_scripts_styles() {
    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Enqueue verification form script
    wp_enqueue_script('verification-form-script', plugins_url('verification-form.js', __FILE__), array('jquery'), '1.0', true);

    // Enqueue custom CSS
    wp_enqueue_style('verification-form-style', plugins_url('style.css', __FILE__));
    // Define admin-ajax.php URL

}
add_action('wp_enqueue_scripts', 'verification_form_scripts_styles');

// Add admin menu page to display form data
function verification_data_menu() {
    add_menu_page(
        'Verification Data',
        'Verification Data',
        'manage_options',
        'verification-data',
        'display_verification_data',
        'dashicons-admin-generic'
    );
}
add_action('admin_menu', 'verification_data_menu');

// Display form data on admin menu page
function display_verification_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_register';
    $data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    echo '<div class="wrap">';
    echo '<h1>Verification Data</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>Email</th><th>City</th><th>Phone</th><th>OTP</th><th>Created At</th></tr></thead>';
    echo '<tbody>';
    foreach ($data as $row) {
        echo '<tr>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td>' . $row['city'] . '</td>';
        echo '<td>' . $row['phone'] . '</td>';
        echo '<td>' . $row['otp'] . '</td>';
        echo '<td>' . $row['created_at'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

// Check if Elementor is active
if ( class_exists( 'Elementor\Widget_Base' ) ) {
    // Register shortcode with Elementor
    function add_elementor_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'custom-shortcodes',
            [
                'title' => 'Custom Shortcodes',
                'icon' => 'fa fa-plug',
            ]
        );
    }
    add_action('elementor/elements/categories_registered', 'add_elementor_widget_categories');

    function add_elementor_shortcode($widget_manager) {
        $widget_manager->register_widget_type(new \Elementor\Widget_Custom_Shortcode());
    }

    add_action('elementor/widgets/widgets_registered', 'add_elementor_shortcode');

    class Widget_Custom_Shortcode extends \Elementor\Widget_Base {
        public function get_name() {
            return 'custom_shortcode';
        }

        public function get_title() {
            return __('Custom Shortcode', 'elementor-custom-widget');
        }

        public function get_icon() {
            return 'fa fa-code';
        }

        public function get_categories() {
            return ['custom-shortcodes'];
        }

        protected function _register_controls() {}

        protected function render() {
            echo do_shortcode('[verification_form]');
        }
    }
}
?>