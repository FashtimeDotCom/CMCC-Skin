<?php
get_header();

if(empty($_GET)){
	require get_template_directory() . '/requirement.php';
}
elseif(isset($_GET['region-result'])){
	require get_template_directory() . '/region-result.php';
}
elseif(isset($_GET['total-result'])){
	require get_template_directory() . '/total-result.php';
}

get_footer();
