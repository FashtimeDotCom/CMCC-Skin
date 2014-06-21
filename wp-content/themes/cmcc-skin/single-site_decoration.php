<?php
$site_id = get_post_meta(get_the_ID(), 'site_id', true);
$decoration_id = get_post_meta(get_the_ID(), 'decoration', true);
$frames = json_decode(get_post_meta(get_the_ID(), 'frames', true));
$frame_types = json_decode(get_option('frame_types'));

$unreceived = array('frames'=>0, 'pictures'=>0);

foreach($frames as $name => $frame){
	if(!$frame->received){
		$unreceived['frames']++;
	}
	foreach($frame->pictures as $picture){
		if(!$picture->received){
			$unreceived['pictures']++;
		}
	}
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET)){
	
	if(isset($_POST['frame_received'])){
		if(is_array($_POST['frame_received'])){
			foreach($_POST['frame_received'] as $name => $received){
				$received = json_decode($received);
				$frames->$name->received = $received;
				$received ? $unreceived['frames'] -- : $unreceived['frames'] ++;
			}
			update_post_meta(get_the_ID(), 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
		}
		else{
			update_post_meta(get_the_ID(), 'frames_received', json_decode($_POST['frame_received']));
		}
	}
	
	if(isset($_POST['picture_received'])){
		if(is_array($_POST['picture_received'])){
			foreach($_POST['picture_received'] as $frame_name => $received){
				$received = json_decode($received);
				$frames->$frame_name->pictures_received = $received;
				foreach($frames->$frame_name->pictures as &$picture){
					$picture->received = $received;
					$received ? $unreceived['pictures'] -- : $unreceived['pictures'] ++;
				}
			}
			update_post_meta(get_the_ID(), 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
		}
		else{
			update_post_meta(get_the_ID(), 'pictures_received', json_decode($_POST['picture_received']));
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($unreceived);
	
	exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['result-upload'])){
	
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/image.php';
	
	$result_photos = json_decode(get_post_meta(get_the_ID(), 'result_photos', true));
	!$result_photos && $result_photos = new stdClass();
	
	foreach($_FILES as $index => $file){
		$attachment_id = media_handle_upload($index, 0);
		if(is_integer($attachment_id)){
			$result_photos->$index = $attachment_id;
		}
	}
	
	update_post_meta(get_the_ID(), 'result_photos', json_encode($result_photos, JSON_UNESCAPED_UNICODE));
	
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit;
}

get_header();

if(empty($_GET)){
	require get_template_directory() . '/recept-confirmation.php';
}elseif(isset($_GET['result-upload'])){
	require get_template_directory() . '/result-upload.php';
}elseif(isset($_GET['result'])){
	require get_template_directory() . '/site-result.php';
}

get_footer(); ?>
