<?php
get_header();

if(empty($_GET['action'])){
	require get_template_directory() . '/requirement.php';
}
elseif($_GET['action'] === 'region-result'){
	require get_template_directory() . '/region-result.php';
}
elseif($_GET['action'] === 'total-result'){
	require get_template_directory() . '/total-result.php';
}

get_footer();
