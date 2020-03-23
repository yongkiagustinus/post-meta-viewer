<?php
/**
 * Plugin Name: Post Meta Viewer
 * Plugin URI: https://88digital.co/stuff/plugins/post-meta-viewer/
 * Description: View all post meta that saved in a post, page or custom post type. No settings needed just plug and play.
 * Version: 1.0
 * Author: 88digital
 * Author URI: https://88digital.co
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.en.html
 * Domain Path: /languages
 * Text Domain: post-meta-viewer
 */

/*
 	Copyright (C) 2020 88digital

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

define( 'POSTMETAVIEWER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

class PostMetaViewer {
	function __construct(){
		if( is_admin() ){
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		}
	}

	/**
	 * Load plugin text domain
	 */
	public static function load_textdomain(){
		load_plugin_textdomain( 'post-meta-viewer', false, basename( POSTMETAVIEWER_PLUGIN_DIR ) . '/languages' );
	}

	/**
	 * Add meta box
	 */
	public static function add_meta_boxes(){
		$post_type = get_post_type();
		add_meta_box( 'post-meta-viewer', __( 'Post Meta Viewer', 'post-meta-viewer' ), array( 'PostMetaViewer', 'meta_box_content' ), $post_type, 'normal', 'low' );

	}

	/**
	 * Meta box content
	 */
	public static function meta_box_content( $post ){

		if ( ! isset( $post->ID ) ) {
			return;
		}

		$post_metas = get_post_meta( $post->ID ); 
		ksort( $post_metas ); ?>
			<div style="font-weight: bold; padding: 10px 0;"><?php echo sprintf( _n( '%s Post meta found', '%s Post metas found', count( $post_metas ), 'post-meta-viewer' ), number_format_i18n( count( $post_metas ) ) ); ?></div>
			<table class="postmetaviewer-table widefat fixed striped">
				<tbody>
					<?php foreach( $post_metas as $key => $value ){ ?>
						<?php $val = self::is_json( $value[0] ) ? array( json_decode( $value[0] ) ) : array( maybe_unserialize( $value[0] ) ); ?>
						<tr>
							<th width="30%"><?php echo esc_html( $key ); ?></th>
							<td><pre><?php esc_html( var_export( maybe_unserialize( $val ) ) ); ?></pre>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<style>
			.postmetaviewer-table tr:hover {
				background: #d6f4ff;
			}
			.postmetaviewer-table th {
				border-right: 1px dotted #ccd0d4;
				word-break: break-all;
			}
			.postmetaviewer-table pre {
				white-space: pre-wrap;
				tab-size: 4;
				word-break: break-all;
			}
			</style>
		<?php 
	}

	/**
	 * Check if json
	 */
	static function is_json( $string ){
		json_decode( $string );
		return ( json_last_error() === JSON_ERROR_NONE );
	}
}

$PostMetaViewer = new PostMetaViewer();