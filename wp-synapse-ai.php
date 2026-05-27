<?php
/**
 * Plugin Name: Synapse Pro – AI Code Editor
 * Plugin URI: https://synapse.com
 * Description: The ultimate professional IDE for WordPress. Features advanced global code search (grep), visual side-by-side Diff mode, Monaco Editor (VS Code engine), and enterprise file management.
 * Version: 1.0.1
 * Author: Synapse Team
 * Author URI: https://synapse.com
 * Text Domain: wp-synapse-ai
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( function_exists( 'wsatuwifm_fs' ) ) {
    wsatuwifm_fs()->set_basename( true, __FILE__ );
} else {
    // Create a helper function for easy SDK access.
    function wsatuwifm_fs() {
        global $wsatuwifm_fs;

        if ( ! isset( $wsatuwifm_fs ) ) {
            // Include Freemius SDK.
            if ( file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) ) {
                require_once dirname( __FILE__ ) . '/freemius/start.php';
            }

            $wsatuwifm_fs = fs_dynamic_init( array(
                'id'                  => '30668',
                'slug'                => 'wp-synapse-ai-the-ultimate-wordpress-ide-file-manager',
                'type'                => 'plugin',
                'public_key'          => 'pk_e2f8ac2c0b98a875974472dbf0966',
                'is_premium'          => true,
                'premium_suffix'      => 'Paid',
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'is_org_compliant'    => true,
                'wp_org_gatekeeper'   => 'OA7#BoRiBNqdf52FvzEf!!074aRLPs8fspif$7K1#4u4Csys1fQlCecVcUTOs2mcpeVHi#C2j9d09fOTvbC0HloPT7fFee5WdS3G',
                'trial'               => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                'menu'                => array(
                    'slug'           => 'wp-synapse-ai',
                    'first-path'     => 'admin.php?page=wp-synapse-ai',
                    'support'        => false,
                    'account'        => true,
                ),
            ) );
        }

        return $wsatuwifm_fs;
    }

    // Init Freemius.
    wsatuwifm_fs();
    // Signal that SDK was initiated.
    do_action( 'wsatuwifm_fs_loaded' );
}

// Define Constants
define( 'WP_SYNAPSE_AI_VERSION', '1.0.1' );
define( 'WP_SYNAPSE_AI_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_SYNAPSE_AI_URL', plugin_dir_url( __FILE__ ) );

// Autoloader (Simple PSR-4-like for includes)
spl_autoload_register( function ( $class ) {
	$prefix = 'WP_Synapse_';
	$base_dir = WP_SYNAPSE_AI_PATH . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . 'class-' . strtolower( str_replace( '_', '-', $relative_class ) ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

// Initialize the plugin
function wp_synapse_ai_init() {
	if ( class_exists( 'WP_Synapse_Core' ) ) {
		\WP_Synapse_Core::get_instance();
	}
}
add_action( 'plugins_loaded', 'wp_synapse_ai_init' );

// Activation
register_activation_hook( __FILE__, function() {
    if ( class_exists( 'WP_Synapse_Core' ) ) {
        \WP_Synapse_Core::create_tables();
    }
});

// Load Premium admin / upsell components unconditionally (they gate themselves internally)
if ( file_exists( WP_SYNAPSE_AI_PATH . 'premium/premium-admin.php' ) ) {
    require_once WP_SYNAPSE_AI_PATH . 'premium/premium-admin.php';
}

// Load Premium logic conditionally
if ( wsatuwifm_fs()->can_use_premium_code() ) {
    if ( file_exists( WP_SYNAPSE_AI_PATH . 'premium/premium-loader.php' ) ) {
        require_once WP_SYNAPSE_AI_PATH . 'premium/premium-loader.php';
    }
}
