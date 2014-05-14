<?php

class Admin extends LB_Controller {
	
	function __construct() {
		parent::__construct();
	}
	
	function outletResult () {
		$this->load->view('admin/outlet_result');
	}
	
	function regionResult () {
		$this->load->view('admin/region_result');
	}
	
	function totalResult () {
		$this->load->view('admin/total_result');
	}
	
}
