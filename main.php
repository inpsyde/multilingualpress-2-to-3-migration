<?php
/**
 * MultilingualPress 2 to 3 Migration.
 *
 * @package MultilingualPress2to3
 * @wordpress-plugin
 *
 * Plugin Name: MultilingualPress 2 to 3 Migration
 * Description: A WP plugin that allows migrating data from MultilingualPress version 2 to version 3.
 * Version: [*next-version*]
 * Author: Inpsyde
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: mlp2to3
 * Domain Path: /languages
 */

namespace Inpsyde\MultilingualPress2to3;

define( 'MLP2TO3_BASE_PATH', __FILE__ );
define( 'MLP2TO3_BASE_DIR', dirname( MLP2TO3_BASE_PATH ) );


/**
 * Retrieves the plugin singleton.
 *
 * @return null|HandlerInterface
 */
function handler() {
	static $instance = null;

	if ( is_null( $instance ) ) {
		$bootstrap = require MLP2TO3_BASE_DIR . '/bootstrap.php';

        $instance = $bootstrap(MLP2TO3_BASE_PATH, plugins_url('', MLP2TO3_BASE_PATH), WP_DEBUG);
	}

	return $instance;
}

handler()->run();
