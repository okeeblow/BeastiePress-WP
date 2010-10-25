<?php
/*
Plugin Name: BeastiePress
Plugin URI: http://cooltrainer.org/software/beastiepress/
Description: Adds some FreeBSD-specific shortcodes to WordPress
Version: 1.0
Author: Nicole Reid
Author URI: http://cooltrainer.org
License: X11
*/

define("PR_URL", "http://www.freebsd.org/cgi/query-pr.cgi?pr=");
define("MAN_URL", "http://www.freebsd.org/cgi/man.cgi?query=");

if(!class_exists('BeastiePress'))
{
	class BeastiePress
	{
		var $port_sites;
		var $beastiepress_port_enable;
		var $beastiepress_port_tag;
		var $beastiepress_port_site;
		var $beastiepress_pr_enable;
		var $beastiepress_pr_tag;
		var $beastiepress_file_enable;
		var $beastiepress_file_tag;
		var $beastiepress_man_enable;
		var $beastiepress_man_tag;

		function beastiepress()
		{
			if(version_compare(get_bloginfo("version"), "2.8", "<"))
			{
				add_action('admin_notices', array(&$this, "version_check"));
			}

			// Set up known Ports index sites
			$this->port_sites = array(
				"freebsd" => array("url" => "http://cvsweb.freebsd.org/ports/", "name" => "FreeBSD.org CVSWeb", "postfix" => "/"),
				"freshports" => array("url" => "http://freshports.org/", "name" => "Freshports.org", "postfix" => "/"),
				"freebsdsoftware" => array("url" => "http://www.freebsdsoftware.org/", "name" => "FreeBSDSoftware.org", "postfix" => ".html")
			);

			// Load translations
			load_plugin_textdomain("beastiepress", null, basename(dirname(__FILE__)));

			// What to do when our plugin is activated and uninstalled
			register_activation_hook(__FILE__, array(&$this, "install"));
			register_uninstall_hook(__FILE__, array(&$this, "uninstall"));

			// Get options from DB
			$this->get_options();

			// Attach to WordPress
			if($this->beastiepress_port_enable == true)
			{
				add_shortcode($this->beastiepress_port_tag, array(&$this, "port"));
			}
			if($this->beastiepress_pr_enable == true)
			{
				add_shortcode($this->beastiepress_pr_tag, array(&$this, "pr"));
			}
			if($this->beastiepress_file_enable == true)
			{
				add_shortcode($this->beastiepress_file_tag, array(&$this, "file"));
			}
			if($this->beastiepress_man_enable == true)
			{
				add_shortcode($this->beastiepress_man_tag, array(&$this, "man"));
			}

			add_action("admin_menu", array(&$this, "admin_menu"));
			add_action("admin_init", array(&$this, "register_options"));

		}

		function install()
		{
			add_option("beastiepress_port_enable", true);
			add_option("beastiepress_port_tag", "port");
			add_option("beastiepress_port_site", "freshports");
			add_option("beastiepress_pr_enable", true);
			add_option("beastiepress_pr_tag", "pr");
			add_option("beastiepress_file_enable", true);
			add_option("beastiepress_file_tag", "file");
			add_option("beastiepress_man_enable", true);
			add_option("beastiepress_man_tag", "man");
		}

		function uninstall()
		{
			delete_option("beastiepress_port_enable");
			delete_option("beastiepress_port_tag");
			delete_option("beastiepress_port_site");
			delete_option("beastiepress_pr_enable");
			delete_option("beastiepress_pr_tag");
			delete_option("beastiepress_file_enable");
			delete_option("beastiepress_file_tag");
			delete_option("beastiepress_man_enable");
			delete_option("beastiepress_man_enable");
		}

		function register_options()
		{
			register_setting("beastiepress", "beastiepress_port_enable");
			register_setting("beastiepress", "beastiepress_port_tag");
			register_setting("beastiepress", "beastiepress_port_site");
			register_setting("beastiepress", "beastiepress_pr_enable");
			register_setting("beastiepress", "beastiepress_pr_tag");
			register_setting("beastiepress", "beastiepress_file_enable");
			register_setting("beastiepress", "beastiepress_file_tag");
			register_setting("beastiepress", "beastiepress_man_enable");
			register_setting("beastiepress", "beastiepress_man_enable");
		}

		function get_options()
		{
			$this->beastiepress_port_enable = get_option("beastiepress_port_enable");
			$this->beastiepress_port_tag = get_option("beastiepress_port_tag");
			$this->beastiepress_port_site = get_option("beastiepress_port_site");
			$this->beastiepress_pr_enable = get_option("beastiepress_pr_enable");
			$this->beastiepress_pr_tag = get_option("beastiepress_pr_tag");
			$this->beastiepress_file_enable = get_option("beastiepress_file_enable");
			$this->beastiepress_file_tag = get_option("beastiepress_file_tag");
			$this->beastiepress_man_enable = get_option("beastiepress_man_enable");
			$this->beastiepress_man_tag = get_option("beastiepress_man_tag");
		}

		function version_check()
		{
			$this->show_error(__("BeastiePress requires at least WordPress 2.8.", "beastiepress"));
		}

		function show_error($error_message)
		{
			echo <<<END
<div id="message" class="error"><p>{$error_message}</p></div>
END;
		}

		function port($attributes, $content = null)
		{
			extract(shortcode_atts(array(
				"site" => $this->beastiepress_port_site
			), $attributes));

			return <<<END
<a href="{$this->port_sites[$site]["url"]}{$content}{$this->port_sites[$site]["postfix"]}">{$content}</a>
END;
		}

		function pr($attributes, $content = null)
		{
			$url = PR_URL;
			return <<<END
<a href="{$url}{$content}">{$content}</a>
END;
		}

		function file($attributes, $content = null)
		{
			return <<<END
<span class="{$this->beastiepress_file_tag}">{$content}</span>
END;
		}

		function man($attributes, $content = null)
		{
			extract(shortcode_atts(array(
				"section" => 0,
				"release" => "Latest"
			), $attributes));


			$url = MAN_URL;
			$suffix = ($attributes["section"] == 0) ? "" : "({$attributes["section"]})";

			return <<<END
<a href="{$url}{$content}&sektion={$attributes["section"]}&manpath={$attributes["release"]}">{$content}{$suffix}</a>
END;
		}


		function admin_menu()
		{
			add_options_page("BeastiePress Options", "BeastiePress", "manage_options", "beastiepress", array(&$this, "admin_options"));
		}
		

		function admin_options()
		{
			if (!current_user_can('manage_options'))
			{
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}


?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php echo __("BeastiePress Settings", "beastiepress"); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields("beastiepress"); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><?php echo __("Port Linking", "beastiepress");?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo __("Port Linking", "beastiepress");?></span></legend>

						<label for="beastiepress_port_enable">
							<input type="checkbox" id="beastiepress_port_enable" name="beastiepress_port_enable" <?php echo ($this->beastiepress_port_enable == true) ? "checked=\"checked\"" : "" ?> />
							<?php echo __("Enable", "beastiepress");?>
						</label>

						<br />

						<?php
						$select = <<<END
						<label for="beastiepress_port_site">
							<select id="beastiepress_port_site" name="beastiepress_port_site">
END;
						foreach($this->port_sites as $key => $site)
						{
							$selected = ($this->beastiepress_port_site == $key) ? "selected=\"selected\"" : "";
							$select .= <<<END
								<option value="{$key}" {$selected}>{$site["name"]}</option>
END;
						}
						$select .= "</select></label>";
						printf(__("Use the %s Ports index.", "beastiepress"), $select);
						?>

						<br />

						<label for="beastiepress_port_tag">
							<?php
							$input = <<<END
							<input type="text" id="beastiepress_port_tag" name="beastiepress_port_tag" value="{$this->beastiepress_port_tag}"/>
END;
							printf(__("Match the %s tag.", "beastiepress"), $input);
							?>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php echo __("Problem Report Linking", "beastiepress");?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo __("Problem Report Linking", "beastiepress");?></span></legend>

						<label for="beastiepress_pr_enable">
							<input type="checkbox" id="beastiepress_pr_enable" name="beastiepress_pr_enable" <?php echo ($this->beastiepress_pr_enable == true) ? "checked=\"checked\"" : "" ?> />
							<?php echo __("Enable", "beastiepress");?>
						</label>

						<br />

						<label for="beastiepress_pr_tag">
							<?php
							$input = <<<END
							<input type="text" id="beastiepress_pr_tag" name="beastiepress_pr_tag" value="{$this->beastiepress_pr_tag}"/>
END;
							printf(__("Match the %s tag.", "beastiepress"), $input);
							?>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php echo __("File Tagging", "beastiepress");?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo __("File Tagging", "beastiepress");?></span></legend>

						<label for="beastiepress_file_enable">
							<input type="checkbox" id="beastiepress_file_enable" name="beastiepress_file_enable" <?php echo ($this->beastiepress_file_enable == true) ? "checked=\"checked\"" : "" ?> />
							<?php echo __("Enable", "beastiepress");?>
						</label>

						<br />

						<label for="beastiepress_file_tag">
							<?php
							$input = <<<END
							<input type="text" id="beastiepress_file_tag" name="beastiepress_file_tag" value="{$this->beastiepress_file_tag}"/>
END;
							printf(__("Match the %s tag.", "beastiepress"), $input);
							?>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php echo __("Manual Page Linking", "beastiepress");?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo __("Manual Page Linking", "beastiepress");?></span></legend>

						<label for="beastiepress_man_enable">
							<input type="checkbox" id="beastiepress_man_enable" name="beastiepress_man_enable" <?php echo ($this->beastiepress_man_enable == true) ? "checked=\"checked\"" : "" ?> />
							<?php echo __("Enable", "beastiepress");?>
						</label>

						<br />

						<label for="beastiepress_man_tag">
							<?php
							$input = <<<END
							<input type="text" id="beastiepress_man_tag" name="beastiepress_man_tag" value="{$this->beastiepress_man_tag}"/>
END;
							printf(__("Match the %s tag.", "beastiepress"), $input);
							?>
						</label>
					</fieldset>
				</td>
			</tr>

		</table>

		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
</div>

<?php
		} //admin_options()
	} // class definition

	$beastiepress = new BeastiePress();

} // if class exists
?>