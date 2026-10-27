<?php
/**
 * CopyDescription - MantisBT 2.x plugin
 *
 * Adds a button to the "View Issue" page that copies the issue number,
 * the summary and the (raw) description to the clipboard:
 *
 *   0001234: Summary of the issue
 *
 *   Description text exactly as stored in the database
 *
 * Copyright (C) 2026 fulgor <fulgor@posteo.eu>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

class CopyDescriptionPlugin extends MantisPlugin {

	function register() {
		$this->name        = lang_get( 'plugin_format_title' );
		$this->description = lang_get( 'plugin_format_description' );
		$this->version     = '1.1.0';
		$this->requires    = array(
			'MantisCore' => '2.0.0',
		);
		$this->author      = 'fulgor';
		$this->contact     = 'fulgor@posteo.eu';
		$this->url         = 'https://github.com/fulgor/mantisbt-copy-description/';
		$this->page        = '';
	}

	/**
	 * Default settings. ON = field is copied, OFF = field is not copied.
	 *
	 * @return array
	 */
	function config() {
		return array(
			'copy_id'                     => ON,
			'copy_summary'                => ON,
			'copy_description'            => ON,
			'copy_steps_to_reproduce'     => OFF,
			'copy_additional_information' => OFF,
		);
	}

	function hooks() {
		return array(
			'EVENT_VIEW_BUG_DETAILS' => 'view_bug_details',
		);
	}

	/**
	 * Output the (hidden-until-moved) button and load the script.
	 * The text is passed in a data attribute, because MantisBT's
	 * Content-Security-Policy blocks inline scripts.
	 *
	 * @param string  $p_event  Event name
	 * @param integer $p_bug_id Issue id
	 * @return void
	 */
	function view_bug_details( $p_event, $p_bug_id ) {
		$t_bug = bug_get( (int)$p_bug_id, true );

		$t_copy_id          = ( plugin_config_get( 'copy_id' ) == ON );
		$t_copy_summary     = ( plugin_config_get( 'copy_summary' ) == ON );
		$t_copy_description = ( plugin_config_get( 'copy_description' ) == ON );
		$t_copy_steps       = ( plugin_config_get( 'copy_steps_to_reproduce' ) == ON );
		$t_copy_additional  = ( plugin_config_get( 'copy_additional_information' ) == ON );

		# Head line: "0001234: Summary". The colon only appears if both
		# parts are active.
		$t_head = array();
		if( $t_copy_id ) {
			$t_head[] = bug_format_id( $t_bug->id );
		}
		if( $t_copy_summary ) {
			$t_head[] = $t_bug->summary;
		}

		# Collect all active parts; they are joined by a blank line below.
		$t_parts = array( implode( ': ', $t_head ) );
		if( $t_copy_description ) {
			$t_parts[] = $t_bug->description;
		}
		if( $t_copy_steps ) {
			$t_parts[] = $t_bug->steps_to_reproduce;
		}
		if( $t_copy_additional ) {
			$t_parts[] = $t_bug->additional_information;
		}

		# Drop empty parts, so no superfluous blank lines appear.
		$t_parts = array_filter( $t_parts, function( $p_part ) {
			return trim( $p_part ) !== '';
		} );

		# Nothing to copy (all fields off or empty): show no button.
		if( empty( $t_parts ) ) {
			return;
		}

		# Raw text, exactly as stored, parts separated by a blank line.
		$t_text = implode( "\n\n", $t_parts );

		$t_script = plugin_file( 'copydescription.js' ) . '&v=' . $this->version;

		echo '<div class="widget-toolbar" id="copydescription-wrapper">';
		echo '<button type="button" id="copydescription-btn"'
			. ' class="btn btn-primary btn-white btn-round btn-sm"'
			. ' data-text="' . string_attribute( $t_text ) . '"'
			. ' data-label="' . string_attribute( plugin_lang_get( 'button' ) ) . '"'
			. ' data-copied="' . string_attribute( plugin_lang_get( 'copied' ) ) . '"'
			. ' data-failed="' . string_attribute( plugin_lang_get( 'failed' ) ) . '">'
			. '<i class="fa fa-clipboard ace-icon"></i> '
			. string_display_line( plugin_lang_get( 'button' ) )
			. '</button>';
		echo '</div>';
		echo '<script src="' . string_attribute( $t_script ) . '"></script>';
	}
}
