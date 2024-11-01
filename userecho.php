<?php
/*
Plugin Name: UserEcho for Wordpress - collect feedback for your blog
Version: 1.0.26
Plugin URI: https://userecho.com
Author: UserEcho
Author URI: https://userecho.com
Description: UserEcho - feedback widget to collect and manage user feedback for your blog.
License: GPLv2
*/

/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'UE_URL', plugins_url() . '/userecho' );

if(!function_exists('wp_get_current_user')) {
    require_once ABSPATH . "wp-includes/pluggable.php";
}

class UserEcho {
	public $current_user = 0;
	public function __construct() {
		$this->check_if_sso_login();
		load_plugin_textdomain( 'UserEcho', false, basename( dirname( __FILE__ ) ) . '/lang' );

		add_action( 'admin_menu', array( $this, 'add_menu' ), 20 );
		add_action( 'wp_footer', array( $this, 'show_tab_widget' ) );

	}
	public function get_current_user(){
		if (empty( $this->current_user->ID ) && function_exists('wp_get_current_user')){
			$this->current_user = wp_get_current_user();
		}
		return $this->current_user;
	}

	public function add_menu() {
		$options = $this->get_options();
		add_menu_page( 'UserEcho' . __( 'Options' ), 'UserEcho', 'manage_options', 'userecho', array( $this, 'set_options' ), UE_URL . '/icon.png' );
	}

	public function set_options() {
		if ( !current_user_can( 'manage_options' ) ) {
			return false;
		}

		$_REQUEST += array( 'ue_action' => '' );
		$action = $_REQUEST['ue_action'];

		if ( $action === 'delete' && check_admin_referer( 'ue-delete-options' ) ) {
			delete_option( 'UserEcho_options' );
		}

		if ( $action === 'edit' && check_admin_referer( 'userecho' ) ) {
			$options = $this->get_options();
			$orig_options = $options;

			if ( !empty( $_POST['api_key'] ) ) { $options['api_key'] = $_POST['api_key']; }
			if ( !empty( $_POST['domain'] ) ) { $options['domain'] = $_POST['domain']; }
			if ( !empty( $_POST['language'] ) ) { $options['language'] = $_POST['language']; }
			$options['auth_only'] = !empty( $_POST['auth_only'] );
			$options['show_tab'] = !empty( $_POST['show_tab'] );
			$options['tab_icon_show'] = !empty( $_POST['tab_icon_show'] );
			$options['verified_email'] = !empty( $_POST['verified_email'] );

			if ( !empty( $_POST['forum'] ) ) { $options['forum'] = $_POST['forum']; }
			if ( !empty( $_POST['tab_corner_radius'] ) ) { $options['tab_corner_radius'] = $_POST['tab_corner_radius']; }
			if ( !empty( $_POST['tab_font_size'] ) ) { $options['tab_font_size'] = $_POST['tab_font_size']; }
			if ( !empty( $_POST['tab_alignment'] ) ) { $options['tab_alignment'] = $_POST['tab_alignment']; }
			if ( !empty( $_POST['tab_text'] ) ) { $options['tab_text'] = $_POST['tab_text']; }
			if ( !empty( $_POST['tab_text_color'] ) ) { $options['tab_text_color'] = $_POST['tab_text_color']; }
			if ( !empty( $_POST['tab_bg_color'] ) ) { $options['tab_bg_color'] = $_POST['tab_bg_color']; }
			if ( !empty( $_POST['tab_hover_color'] ) ) { $options['tab_hover_color'] = $_POST['tab_hover_color']; }

			if ( $orig_options !== $options ) {
				update_option( 'UserEcho_options', $options );
			}
		}

		echo $this->meta_configuration_content();
	}


	function meta_configuration_content() {
		$options = $this->get_options();

		$language_options = array(
			'en' => __( 'English' ) . ' (EN)',
			'ru' => __( 'Russian' ) . ' (RU)',
			'es' => __( 'Spanish' ) . ' (ES)',
			'fr' => __( 'French' ) . ' (FR)',
			'de' => __( 'German' ) . ' (DE)',
			'nl' => __( 'Dutch' ) . ' (NL)',
			'is' => __( 'Icelandic' ) . ' (IS)',
			'et' => __( 'Estonian' ) . ' (ET)',
			'kk' => __( 'Kazakh' ) . ' (KK)',
			'pt-br' => __( 'Portugal (Brasil)' ) . ' (PT-BR)',
			'uk' => __( 'Ukrainian' ) . ' (UK)',
			'cs' => __( 'Czech' ) . ' (CS)',
			'da' => __( 'Danish' ) . ' (DA)',
			'fi' => __( 'Finnish' ) . ' (FI)',
			'hu' => __( 'Hungarian' ) . ' (HU)',
			'it' => __( 'Italian' ) . ' (IT)',
			'ja' => __( 'Japanese' ) . ' (JA)',
			'ko' => __( 'Korean' ) . ' (KO)',
			'sv' => __( 'Swedish' ) . ' (SV)',
			'tr' => __( 'Turkish' ) . ' (TR)',
			'zh-hans' => __( 'Chinese' ) . ' (ZH-HANS)',
			'ca' => __( 'Catalan' ) . ' (CA)',
			'pl' => __( 'Polish' ) . ' (PL)',
			'nb' => __( 'Norwegian Bokmal' ) . ' (NB)',
			'he' => __( 'Hebrew' ) . ' (HE)',
			'ar' => __( 'Arabic' ) . ' (AR)',
		);

		$tab_alignment_options = array(
			'left' => __( 'Left' ),
			'right' => __( 'Right' ),
			'top' => __( 'Top' ),
			'bottom' => __( 'Bottom' ),
		);

		if ( !empty( $options['language'] ) ) {
			$language_options += array( $options['language'] => 'Custom ('.$options['language'].')' );
		}

?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br></div>
<h2>UserEcho integration Settings</h2>
		<form method="post" action="">
			<?php wp_nonce_field( 'userecho' ); ?>
			<input type="hidden" name="ue_action" value="edit" />
			<div class="table">
				<h3>Common:</h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th><?php _e( 'Domain', 'UserEcho' ); ?></th>
							<td>
								<input id="domain" name="domain" type="text" class="regular-text" value="<?php echo esc_attr( $options['domain'] ); ?>" />
								<span class="description"><?php _e( 'Your UserEcho community url (UE subdomain or custom domain, depends on project settings).', 'UserEcho' ); ?></span>
							</td>
						</tr>
					</tbody>
				</table>
				<h3>Tab widget:</h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th><?php _e( 'Show tab', 'UserEcho' ); ?></th>
							<td>
								<input id="show_tab" name="show_tab" type="checkbox" value="1"<?php checked( $options['show_tab'], '1' ); ?> />
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Forum id', 'UserEcho' ); ?></th>
							<td>
								<input id="forum" name="forum" type="text" class="regular-text" value="<?php echo esc_attr( $options['forum'] ); ?>" />
								<span class="description"><?php _e( 'Add a forum ID you would like to see in the widget.', 'UserEcho' ); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Language', 'UserEcho' ); ?></th>
							<td>
								<select id="language" name="language">
								<?php foreach ( $language_options as $locale => $label ) {
									echo '<option value="' . esc_attr( $locale ) . '"' . selected( $locale, $options['language'], false ) . '>' . esc_attr( $label ) . '</option>';
								} ?>
								</select>
							</td>
						</tr>

					</tbody>
				</table>
				<h3>Tab visual style:</h3>
				<table class="form-table"><tbody>
					<tr valign="top">
							<th><?php _e( 'Font size', 'UserEcho' ); ?></th>
							<td>
								<input id="tab_font_size" name="tab_font_size" type="text" class="regular-text" value="<?php echo esc_attr( $options['tab_font_size'] ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Text on tab', 'UserEcho' ); ?></th>
							<td>
								<input id="tab_text" name="tab_text" type="text" class="regular-text" value="<?php echo esc_attr( $options['tab_text'] ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Text color', 'UserEcho' ); ?></th>
							<td>
								<input id="tab_text_color" name="tab_text_color" type="text" class="regular-text" style="background-color: <?php echo $options['tab_text_color']; ?>; color: <?php echo $this->get_text_color( $options['tab_text_color'] ); ?>;" value="<?php echo esc_attr( $options['tab_text_color'] ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Background Color', 'UserEcho' ); ?></th>
							<td>
								<input id="tab_bg_color" name="tab_bg_color" type="text" class="regular-text" style="background-color: <?php echo $options['tab_bg_color']; ?>; color: <?php echo $this->get_text_color( $options['tab_bg_color'] ); ?>;" value="<?php echo esc_attr( $options['tab_bg_color'] ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Hover Color', 'UserEcho' ); ?></th>
							<td>
								<input id="tab_hover_color" name="tab_hover_color" type="text" class="regular-text" style="background-color: <?php echo $options['tab_hover_color']; ?>; color: <?php echo $this->get_text_color( $options['tab_hover_color'] ); ?>;" value="<?php echo esc_attr( $options['tab_hover_color'] ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Tab alignment', 'UserEcho' ); ?></th>
							<td>
								<select id="tab_alignment" name="tab_alignment">
								<?php foreach ( $tab_alignment_options as $tab_alignment_option => $tab_alignment_label ) {
									echo '<option value="' . esc_attr( $tab_alignment_option ) . '"' .  selected( $tab_alignment_option, $options['tab_alignment'], FALSE ) . '>' . esc_attr( $tab_alignment_label ) . '</option>';
								}?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Show icon', 'UserEcho' ); ?></th>
							<td>
								<input id="tab_icon_show" name="tab_icon_show" type="checkbox" value="1"<?php checked( $options['tab_icon_show'], '1' ); ?> />
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'Tab corner Radius', 'UserEcho' ); ?></th>
							<td>
								<input id="tab_corner_radius" name="tab_corner_radius" type="text" class="regular-text" value="<?php echo esc_attr( $options['tab_corner_radius'] ); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
				<h3>Single Sign On (SSO):</h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th><?php _e( 'SSO Key', 'UserEcho' ); ?></th>
							<td>
								<input id="api_key" name="api_key" type="text" class="regular-text" value="<?php echo esc_attr( $options['api_key'] ); ?>" />
								<span class="description"><?php _e( 'Available in your SSO settings.', 'UserEcho' ); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th><?php _e( 'User email verified?', 'UserEcho' ); ?></th>
							<td>
								<input id="verified_email" name="verified_email" type="checkbox" value="1"<?php checked( $options['verified_email'], '1' ); ?> />
								<span class="description"><?php _e( 'If checked user email will be marked as verified.', 'UserEcho' ); ?></span>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>
				<p class="submit"><input type="button" onclick="if(confirm('<?php _e( 'Are you sure you want to reset options to default values?' ); ?>')) location.href='<?php echo wp_nonce_url( admin_url( 'admin.php?page=userecho&ue_action=delete' ), 'ue-delete-options' ); ?>';" class="button-primary" value="Reset Configuration" /></p>
				<div class="clear"></div>
			</div>
		</form>
		</div>
<?php
}

	// This function returns the current options
	public function get_options() {
		static $default = null;
		if ( !isset( $default ) ) {
			$default = array(
				'api_key' => '',
				'domain' => '[YOUR_ALIAS].userecho.com',
				'show_tab' => 1,
				'language' => 'en',
				'forum' => '1',
				'tab_corner_radius' => 10,
				'tab_font_size' => 20,
				'tab_alignment' => 'right',
				'tab_text' => 'Feedback',
				'tab_text_color' => '#ffffff',
				'tab_bg_color' => '#ee105a',
				'tab_hover_color' => '#f45c5c',
				'tab_icon_show' => 1,
				'verified_email' => 1,
			);
		}

		$saved = get_option( 'UserEcho_options' );

		$options = array();
		if ( !empty( $saved ) ) {
			$options = $saved;
		}

		$options += $default;

		if ( $saved != $options ) {
			update_option( 'UserEcho_options', $options );
		}

		return $options;
	}

	private function get_sso_token() {
		$current_user = $this->get_current_user();
		$options = $this->get_options();

    #echo get_avatar_url( $current_user->ID, array('size' => 64, 'scheme' => 'https'));

		if ( !empty( $current_user->ID ) ) {
			$params = array(
				'guid' => $current_user->user_login, // User ID in your system - used to identify user (first time auto-registration)
				'display_name' => $current_user->display_name, // User display name in your system
				'email' => $current_user->user_email, // User email - used for notification about changes on feedback
				'locale' => $options['language'], // (Optional) User language override
				'verified_email' => $options['verified_email'], // (Optional) Is user's email verified?
        'avatar_url' => get_avatar_url( $current_user->ID, array('size' => 64, 'scheme' => 'https'))
			);
		}
		else
			return "";

		try {
			$ue_sso_cipher = new UeSsoCipher();
			return $ue_sso_cipher->encrypt($options['api_key'], $params);
		} catch (Exception $e) {
    		return 'exception_in_wp_encrypt';
		}
	}

	private function get_tab_text_hash($text) {
		//creates hash for custom text on widget
		$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
		return strtr(rawurlencode(base64_encode($text)), $revert);
		}

	private function get_text_color( $background_color ) {
		preg_match( '/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i', $background_color, $matches );
		if ( !empty( $matches[1] ) ) {
			$luminosity = 0;
			$color_length = strlen( $matches[1] ) / 3;
			$colors = str_split( $matches[1], $color_length );
			$coefficients = array( .2126, .7152, .0722 );
			foreach ( $colors as $index => $color ) {
				if ( $color_length === 1 ) {
					$color .= $color;
				}
				$color_percent = hexdec( $color ) / 255;
				$luminosity += $color_percent * $coefficients[$index];
			}
		}
		else {
			$luminosity = 1;
		}
		return ( $luminosity > 0.5 ? '#000' : '#fff' );
	}

	public function show_tab_widget()
		{
		$options = $this->get_options();

		if ( $options['show_tab'] ) {
			$_ues = array(
				'host' => $options['domain'],
				'forum' => $options['forum'],
				'lang' => $options['language'],
				'tab_icon_show' => (bool) $options['tab_icon_show'],
				'tab_corner_radius' => (int) $options['tab_corner_radius'],
				'tab_font_size' => (int) $options['tab_font_size'],
				'tab_image_hash' => $this->get_tab_text_hash($options['tab_text']),
				'tab_alignment' => $options['tab_alignment'],
				'tab_text_color' => $options['tab_text_color'],
				'tab_bg_color' => $options['tab_bg_color'],
				'tab_hover_color' => $options['tab_hover_color'],
			);

			$sso_token = $this->get_sso_token();

      if ($sso_token){
				$_ues = $_ues + array(
					'params' => array('sso_token' => $sso_token),
				);
			}

			echo "<script type='text/javascript'>
			var _ues = " . json_encode( $_ues ) . ";

			(function() {
				var _ue = document.createElement('script'); _ue.type = 'text/javascript'; _ue.async = true;
				_ue.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.userecho.com/js/widget-1.4.gz.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(_ue, s);
			})();
			</script>";
		}
	}


	public function check_if_sso_login()
		{
		#http://127.0.0.1/wordpress/wp-login.php
		# http://127.0.0.1/wordpress/wp-login.php?host_url=https://userecho.com&action=ue-login
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'ue-login' ) {
			if ( ! isset( $_REQUEST['return']) || ! $_REQUEST['return'] ) {
				return;
			}

			try {
				$return_url = $_REQUEST['return'];
				$current_user = $this->get_current_user();
				if ( is_a( $current_user, 'WP_User' ) && !empty( $current_user->ID ) ) {
					// If user is logged into WP, generate SSO and redirect him back to UE
					header( 'Location: '.$this->get_sso_redirect_url($return_url) ) ;
					die();
				}
				else{
					if (isset($return_url)){
						header( "Location: " .wp_login_url()."?redirect_to=ue_sso_redirect_to=".$return_url );
						die();
					}
			 	}
			} catch (Exception $e) {
				header( "Location: " .wp_login_url()."?redirect_to=exception_in_check_if_sso_login" );
				die();
			}

		}
	}
	public function get_sso_redirect_url($return_url){
		$options = $this->get_options();
		$redirect_host = parse_url($return_url, PHP_URL_HOST);
		$settings_host = $options['domain'];
		try {
			if ($redirect_host == $settings_host){
				if (strpos($return_url, '?') == FALSE){
					$return_url .= '?';
				} else {
					$return_url .= '&';
				}
				return $return_url . 'sso_token=' . $this->get_sso_token();
			} else {
				return "http://" . $settings_host . "/?host1=".$settings_host."&host2=".$redirect_host;
			}
		} catch (Exception $e) {
    		return "http://" . $settings_host . "/?exception=get_sso_redirect_url";
		}

	}
}

add_action( 'widgets_init', 'userecho_sso_widgets' );

function userecho_sso_widgets() {
	register_widget( 'UserEcho_SSO_Widget' );
}

class UserEcho_SSO_Widget extends WP_Widget {
	//public function __construct() {
	public function UserEcho_SSO_Widget() {
		$widget_ops = array( 'classname' => 'userecho_sso_widget', 'description' => __( 'Adds a customizable link to UserEcho community with SSO support', 'UserEcho' ) );
		parent::__construct( 'userecho_sso_widget', __( 'UserEcho link', 'UserEcho' ), $widget_ops );
	}

	//Because widget_name::__construct() calls WP_Widget::WP_Widget(), which in turn calls widget_name::__construct() etc.

	//A simple solution would be to make widget_name::__construct() call WP_Widget::__construct() directly.

	public function form( $instance ) {

		// outputs the options form on admin
		if ( $instance ) {
			$title = esc_attr( $instance['title'] );
			$link_text = esc_attr( $instance['link_text'] );
		}
		else {
			$title = __( 'UserEcho Login', 'UserEcho' );
			$link_text = __( 'Go to UserEcho', 'UserEcho' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Content:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo $link_text; ?>" />
		</p>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['link_text'] = strip_tags( $new_instance['link_text'], '<i><b><em><strong><span><img><br>' );
		return $instance;
	}

	public function widget( $args, $instance ) {

			// outputs the content of the widget
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			?>
			<a href="?userecho_sso_login=1"><?php echo $instance['link_text']; ?></a>
			<?php
			echo $after_widget;

	}
}

class UeSsoCipher
{
    const CIPHER = "AES-256-CBC";
    /*
        Generates sso_token
        @param  $key - your sso_key
        @param  $data_json - prepared data in json format
        @returns string
     */
    public function encrypt($key, $data_json)
    {
        // add expires if does not exist
        if (!array_key_exists('expires',$data_json)){
            # add 1 hour
            $data_json['expires'] = time()+3600;
        }
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher=self::CIPHER));
		$ciphertext_raw = openssl_encrypt(json_encode($data_json), self::CIPHER, $key, $options=OPENSSL_RAW_DATA, $iv);
        return urlencode(base64_encode($iv . $ciphertext_raw));
    }
}

$ue = new UserEcho();

# Redirects back to UE after login if it's login via SSO
add_filter( 'login_redirect', 'ue_sso_login_redirect', 10, 3 );
function ue_sso_login_redirect($url, $request, $user) {
	global $ue;
	parse_str( $request, $params );
	if ( ! isset($params['ue_sso_redirect_to']) ) {
		return $url;
	}
	$return_url = $params['ue_sso_redirect_to'];
	if ( ! $return_url ) {
		return $url;
	}
	#if (isset( $_REQUEST['wp-submit'] ) && $_REQUEST['wp-submit'] == "Log In" && is_a( $user, 'WP_User' )) {
	if (is_a( $user, 'WP_User' ) && !empty($user->ID)) {
		$ue->current_user = $user;
		header("Location: ".$ue->get_sso_redirect_url($return_url));
		die();
	}
	return  $request;
}
