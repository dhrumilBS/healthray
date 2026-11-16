<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (version_compare(PHP_VERSION, '5.6.1', '>=')) {
	updraft_try_include_file('methods/dropbox2.php', 'include_once');
} else {
	updraft_try_include_file('methods/insufficient.php', 'include_once');
	class UpdraftPlus_BackupModule_dropbox extends UpdraftPlus_BackupModule_insufficientphp {
		public function __construct() {
			parent::__construct('dropbox', 'Dropbox', '5.6.1');
		}
	}
}
