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

		$this->version     = '1.0.2';
		$this->requires = array(
			'MantisCore' => '2.0.0',
		);

		$this->author      = 'fulgor';
		$this->contact     = 'fulgor@posteo.eu';
		$this->url         = 'https://github.com/fulgor';
		$this->page        = '';
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

		# Raw text, exactly as stored: "0001234: Summary" + blank line + description
		$t_text = bug_format_id( $t_bug->id ) . ': ' . $t_bug->summary
			. "\n\n"
			. $t_bug->description;

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
