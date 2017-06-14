<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class cbTaxType extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_cbtaxtype';
	var $table_index= 'cbtaxtypeid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_cbtaxtypecf', 'cbtaxtypeid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_cbtaxtype', 'vtiger_cbtaxtypecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbtaxtype'   => 'cbtaxtypeid',
		'vtiger_cbtaxtypecf' => 'cbtaxtypeid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'taxtypename'=> Array('cbtaxtype' => 'taxtypename'),
		'Assigned To' => Array('crmentity' => 'smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'taxtypename'=> 'taxtypename',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'taxtypename';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'taxtypename'=> Array('cbtaxtype' => 'taxtypename')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'taxtypename'=> 'taxtypename'
	);

	// For Popup window record selection
	var $popup_fields = Array('taxtypename');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'taxtypename';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'taxtypename';

	// Required Information for enabling Import feature
	var $required_fields = Array('taxtypename'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'taxtypename';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'taxtypename');

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'taxtype-', '0000001');
			$mods = array('Accounts'=>'LBL_ACCOUNT_INFORMATION', 'Contacts'=>'LBL_CONTACT_INFORMATION',
				'Vendors'=>'LBL_VENDOR_INFORMATION', 'Products'=>'LBL_PRICING_INFORMATION',
				'Services'=>'LBL_PRICING_INFORMATION');
			foreach ($mods as $modname => $block) {
				$module = Vtiger_Module::getInstance($modname);
				$blockInstance = VTiger_Block::getInstance($block,$module);
				if ($blockInstance) {
					$field = new Vtiger_Field();
					$field->name = 'taxtypeid';
					$field->label= 'Tax Type';
					$field->table = $module->basetable;
					$field->column = 'taxtypeid';
					$field->columntype = 'INT(11)';
					$field->uitype = 10;
					$field->displaytype = 1;
					$field->typeofdata = 'I~O';
					$field->presence = 0;
					$blockInstance->addField($field);
					$field->setRelatedModules(Array('cbTaxType'));
					echo "<br><b>Added Field to $modname module.</b><br>";
				}
			}
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>
