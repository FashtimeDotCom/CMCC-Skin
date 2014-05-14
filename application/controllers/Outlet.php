<?php

class Outlet extends LB_Controller {
	
	function __construct() {
		parent::__construct();
	}
	
	function requirement () {
		$this->load->view('outlet/requirement');
	}
	
	function frameReceiptConfirmation () {
		$this->load->view('outlet/frame_receipt_confirmation');
	}
	
	function pictureReceiptConfirmation () {
		$this->load->view('outlet/picture_receipt_confirmation');
	}
	
	function setupSample () {
		$this->load->view('outlet/setup_sample');
	}
	
	function resultupload () {
		$this->load->view('outlet/result_upload');
	}
	
}