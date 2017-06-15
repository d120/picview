<?php
# # # # # # # # # # # # # # # # # # # # # # # # #
#           _    __     ___                     #
#     _ __ (_) __\ \   / (_) _____      __      #
#    | '_ \| |/ __\ \ / /| |/ _ \ \ /\ / /      #
#    | |_) | | (__ \ V / | |  __/\ V  V /       #
#    | .__/|_|\___| \_/  |_|\___| \_/\_/        #
#    |_| picView Version 0.4.20061213           #
#                                               #
# This is GPL.                                  #
# Visit http://scripte.arnep.de                 #
# # # # # # # # # # # # # # # # # # # # # # # # #

error_reporting(E_ALL);

include('./config.php');
include('./functions.php');

/**
  Actions:
   t = thumbnail size
   m = medium size
   i = original size
   c = comment
   s = save comment
   r = rotate
   d = delete image cache
   p = show path (default)

  Parameters:
   n = startindex of thumbnails
*/

if (isset($_SERVER['PATH_INFO'])) {
	list(, $galleryId, $action, $path)=explode('/',$_SERVER['PATH_INFO'],4);
} else {
	$path='';
  $action = '';
  $galleryId = 'picview';
}
$galleryConfig = $galleries[$galleryId];
$pictures_path = $galleryConfig['pictures_path'];
if (!$pictures_path) { header("HTTP/1.1 404 Not found"); die("<h2>Nicht gefunden</h2>"); }
$BASE_URI = $_SERVER['SCRIPT_NAME'].'/'.$galleryId;

if (!isset($galleryConfig['title'])) $galleryConfig['title'] = $galleryId;

require_basicauth();

if (strlen($action) != 1) {
	$path = $action . '/' . $path;
	$action = '';
}
$path = '/'.$path;

if ($path == '') $path = '/';

$path = preg_replace("!/+!", "/", $path);

if ($action === 't') {

	if (!legal_image($pictures_path.$path, $pictures_path)) die ('Fehler');

	// Filename for thumbnail to save to
	$save_name = $thumbs_path.'/'.str_replace('/','_',$pictures_path.$path);

	// Thumbnail exists? Load it!
	if (file_exists($save_name)) {
		$new_image = @ImageCreateFromJPEG($save_name);
	} else {
		if(preg_match("/\.(png)$/i", $pictures_path.$path))
			$im = @ImageCreateFromPNG($pictures_path.$path) or die ("Kann keinen neuen GD-Bild-Stream erzeugen (PNG)");
		elseif(preg_match("/\.(jpg)$/i", $pictures_path.$path))
			$im = @ImageCreateFromJPEG($pictures_path.$path) or die ("Kann keinen neuen GD-Bild-Stream erzeugen (JPEG)");

		$size = GetImageSize ($pictures_path.$path);
		$background_color = ImageColorAllocate ($im, 255, 255, 255);

		$h = $thumb_size;
		if ($size[0] > $size[1]) {
			$width  = $size[0]*$h/$size[1];
			$height = $h;
		} else {
	        $width  = $h;
	        $height = $size[1]*$h/$size[0];
		}

		if (function_exists('imagecreatetruecolor')) {
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $im, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		} else {
			$new_image = imagecreate($width, $height);
			imagecopyresized($new_image, $im, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		}

		// Save thumbnail to file
		if (is_writable($save_name) || !file_exists($save_name)) {
			ImageJPEG($new_image, $save_name, 90);
		} else {
		   print "Die Datei $save_name ist nicht schreibbar";
		}

	}

	// Die Anzahl der Kommentare ins Bild einfuegen
	$comments = $_GET['comments'];
	if ($comments > 0) {
		$comments .= $comments == 1 ? ' Kommentar' : ' Kommentare';
		$tc_white = imagecolorallocate($new_image, 255, 255, 255);
		$tc_black = imagecolorallocate($new_image, 0, 0, 0);
		imagestring($new_image, 3, 6, 6, $comments, $tc_black);
		imagestring($new_image, 3, 6, 8, $comments, $tc_black);
		imagestring($new_image, 3, 8, 6, $comments, $tc_black);
		imagestring($new_image, 3, 8, 8, $comments, $tc_black);
		imagestring($new_image, 3, 7, 7, $comments, $tc_white);
	}

	// Show image
	Header('Content-type: image/jpeg');
	ImageJPEG($new_image);

} elseif ($action === 'm') {

	if (!legal_image($pictures_path.$path, $pictures_path)) die ('Fehler');

	// Filename for thumbnail to save to
	$save_name = $thumbs_path.'/m'.str_replace('/','_',$pictures_path.$path);

	// Thumbnail exists? Load it!
	if (file_exists($save_name)) {
		$new_image = @ImageCreateFromJPEG($save_name);
	} else {
		if(preg_match("/\.(png)/i", $pictures_path.$path))
			$im = @ImageCreateFromPNG($pictures_path.$path) or die ("Kann keinen neuen GD-Bild-Stream erzeugen (PNG)");
		elseif(preg_match("/\.(jpg)$/i", $pictures_path.$path))
			$im = @ImageCreateFromJPEG($pictures_path.$path) or die ("Kann keinen neuen GD-Bild-Stream erzeugen (JPEG)");

		$size = GetImageSize ($pictures_path.$path);
		$background_color = ImageColorAllocate ($im, 255, 255, 255);

		$h = $medium_size;
		if ($size[0] < $size[1]) {
			$width  = $size[0]*$h/$size[1];
			$height = $h;
		} else {
	        $width  = $h;
	        $height = $size[1]*$h/$size[0];
		}

		if (function_exists('imagecreatetruecolor')) {
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $im, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		} else {
			$new_image = imagecreate($width, $height);
			imagecopyresized($new_image, $im, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		}

		// Save thumbnail to file
		if (is_writable($save_name) || !file_exists($save_name)) {
			ImageJPEG($new_image, $save_name, 90);
		} else {
		   print "Die Datei $save_name ist nicht schreibbar";
		}
	}

	$copyright_file = dirname($pictures_path.$path).'/'.$copyright_file;
	if (file_exists($copyright_file)) $text = join('',file($copyright_file));
	$new_image = image_write($new_image, $text, 6, 5, 5);

	Header('Content-type: image/jpeg');

	ImageJPEG($new_image);

} elseif ($action === 'i') {

	if (!legal_image($pictures_path.$path, $pictures_path)) die ('Fehler');

    $copyright_file = dirname($pictures_path.$path).'/'.$copyright_file;
    if (file_exists($copyright_file)) $text = join('',file($copyright_file));
    if(preg_match("/\.(png)$/i", $pictures_path.$path)) {
        $im = @ImageCreateFromPNG($pictures_path.$path) or die ("Kann keinen neuen GD-Bild-Stream erzeugen");
        $im = image_write($im, $text, 6, 5, 5);
        Header('Content-type: image/png');
        ImagePNG($im);
    }
    elseif(preg_match("/\.(jpg)$/i", $pictures_path.$path)) {
        $im = @ImageCreateFromJPEG($pictures_path.$path) or die ("Kann keinen neuen GD-Bild-Stream erzeugen");
        $im = image_write($im, $text, 6, 5, 5);
        Header('Content-type: image/jpeg');
        ImageJPEG($im);
    }
} elseif ($action === 'c') {
	$start = (int)$_GET['n'];
	if (!legal_image($pictures_path.$path, $pictures_path)) die('Bild nicht gefunden.');

	$no_cache_rand = isset($_GET['r']) ? '?'.rand(0, 86400) : ''; 
    $arr = Array();
    $arr['pagetitle'] = $path;
    //$arr['navigation'] = '<a href="'.$BASE_URI.'/p'.preg_replace('!^(.*?/)[^/]+$!', '$1', $path).'?n='.$start.'">[&Uuml;bersicht]</a><br>';

    // Filename for thumbnail to save to
    $save_name_m = $thumbs_path.'/m'.str_replace('/','_',$pictures_path.$path);

    // Get image size for shadow
    $size = @getimagesize($save_name_m);
    $h = $medium_size;
    if ($size[0] < $size[1]) {
        if ($size[1] != 0) $width  = $size[0]*$h/$size[1];
        $height = $h;
    } else {
        $width  = $h;
        if ($size[0] != 0) $height = $size[1]*$h/$size[0];
    }

    // previous and next picture
    $curr_dir = preg_replace('!^(.*/)[^/]+$!', '\1', $pictures_path.$path);
    $curr_pic = preg_replace('!^.*/([^/]+)$!', '\1', $pictures_path.$path);
    $dir = get_dir($curr_dir, '', 'f');
    $key = array_search($curr_pic, $dir);
    $prev_pic = $dir[$key - 1] != '' ? str_replace($pictures_path, '', $curr_dir.$dir[$key - 1]) : '';
    $next_pic = $dir[$key + 1] != '' ? str_replace($pictures_path, '', $curr_dir.$dir[$key + 1]) : '';

    $arr['content'] = '';
    $arr['content'] .= '<style> @import url(/picview/css/picview_picture.css); </style>';
    $arr['content'] .= <<<script
<script src="/picview/css/jquery-2.1.4.min.js"></script>
<script>
var list = localStorage.highlightlist||"";
$("body").keydown(function(e) {
//console.log(e.which);
if (e.which==37) location= $(".arrow.left").attr("href");
else if (e.which==39) location= $(".arrow.right").attr("href");
else if (e.which==32){
  addName(location.pathname);
  e.preventDefault();
  return false;
}
});
function addName(str) {
list+=str+"\\n";
localStorage.highlightlist=list;
console.log(list);
}
</script>
script;
    $arr['content'] .= '<div>';
    
    // Previous Picture
    if ($prev_pic) {
        $t_size = @getimagesize($thumbs_path.'/'.str_replace('/','_',$pictures_path.$prev_pic));
        $t_size[0] = (int)($t_size[0] / 2);
        $t_size[1] = (int)($t_size[1] / 2);
        $arr['content'] .= '<a href="'.$BASE_URI.'/c'.$prev_pic.'?n='.$start.'#img" class="arrow left">';
        $arr['content'] .= '&lt;</a>';
    }

    // Next Picture
    if ($next_pic) {
        $arr['content'] .= '<a href="'.$BASE_URI.'/c'.$next_pic.'#img" class="arrow right">';
        $arr['content'] .= '&gt;</a>';
        // Preload next picture to browser cache
        $arr['content'] .= '<img src="'.$BASE_URI.'/m'.$next_pic.'" border="0" width="1" height="1" hspace="20" alt="" style="position:absolute" />';
    }
    //$arr['content'] .= '<br clear="all"><br>';

    // Show picture
    $arr['content'] .= '<div style="height:'.(($height ? $height : $medium_size)+15).'px;text-align:center;width:100%" class="mainImage">';
    $arr['content'] .= '<a href="'.$BASE_URI.'/i'.$path.'">';
    $arr['content'] .= '<img src="'.$BASE_URI.'/m'.$path.$no_cache_rand.'" border="0"  style="max-width:90%" />';
    $arr['content'] .= '</a></div>';

    // Filename for comments to save to
    $save_name = $comments_path.'/'.str_replace('/','_',$path).'.txt';

	if (file_exists($save_name)) {
		$handle = fopen ($save_name,"r");
		while ($data = fgetcsv ($handle, 5000, ",")) {
			$arr['content'] .= '<div class="comment"><h4><b>'.stripslashes($data[0]).'</b> schrieb am '.date('d.m.Y', $data[2]).' um '.date('H:i', $data[2]).' folgendes:</h4>';
			$arr['content'] .= stripslashes(nl2br($data[1])) . "</div>";
		}
		fclose ($handle);
	}

    // Image functions
    $arr['breadcrumb'] .= '<span style="float:right"><b>';
    $arr['breadcrumb'] .= '<a href="'.$BASE_URI.'/r'.$path.'">[ ⟳  Drehen im mathematisch negativen Sinn ]</a> ';
    $arr['breadcrumb'] .= '<a href="'.$BASE_URI.'/d'.$path.'">[ Bildansicht neu erzeugen ]</a> ';
    $arr['breadcrumb'] .= '</b></span>';
    $arr['breadcrumb'] .= '<b style="float:left; padding: 0 10px;"><a href="'.$BASE_URI.'/p'.preg_replace('!^(.*?/)[^/]+$!', '$1', $path).'?n='.$start.'">[ Zur &Uuml;bersicht ]</a></b>';

    // Show comment form
    $arr['content'] .= '<hr><form action="'.$BASE_URI.'/s'.$path.'" method="post" class="comment-form comment">';
    if ($_SERVER["REMOTE_USER"] != '')
        $arr['content'] .= 'Name: '.$_SERVER["REMOTE_USER"].'<br>';
    else
        $arr['content'] .= 'Name: <input type="text" name="name" size="50"><br>';
    $arr['content'] .= 'Kommentar:<br>';
    $arr['content'] .= '<textarea name="comment"></textarea><br>';
    $arr['content'] .= '<input type="submit" value="Kommentar speichern">';
    $arr['content'] .= '</form>';

    $arr['content'] .= '</div>';

    $arr['breadcrumb'] .= show_breadcrumb($path);

    echo make_page($arr);
} elseif ($action === 's') {

	// Filename for comments to save to
    $save_name = $comments_path.'/'.str_replace('/','_',$path).'.txt';

	$name = $_SERVER['REMOTE_USER'] != '' ? $_SERVER['REMOTE_USER'] : htmlentities($_POST['name']);
	$comment = htmlentities($_POST['comment']);
	$line = '"'.$name.'","'.$comment.'",'.time()."\n";

	// Sichergehen, dass die Datei existiert und beschreibbar ist
	if (is_writable($save_name) || !file_exists($save_name)) {

		// Wir öffnen $filename im "Anhänge" - Modus.
		// Der Dateizeiger befindet sich am Ende der Datei, und
		// dort wird $somecontent später mit fwrite() geschrieben.
		if (!$handle = fopen($save_name, "a")) {
			print "Kann die Datei $save_name nicht öffnen";
			exit;
		}

		// Schreibe $somecontent in die geöffnete Datei.
		if (!fwrite($handle, $line)) {
			print "Kann in die Datei $save_name nicht schreiben";
			exit;
		}
		fclose($handle);
	} else {
		print "Die Datei $save_name ist nicht schreibbar";
		exit;
	}
	#echo $line;
	Header('Location: '.$BASE_URI.'/c'.$path.'?r');
} elseif ($action === 'r') {

	if (!legal_image($pictures_path.$path, $pictures_path)) die('Zugriffsfehler');

	// Filename for thumbnail to save to
	$save_name_m = $thumbs_path.'/m'.str_replace('/','_',$pictures_path.$path);
	$save_name_t = $thumbs_path.'/'.str_replace('/','_',$pictures_path.$path);

    ImageRotate_PV ( $save_name_m, 90 );
    ImageRotate_PV ( $save_name_t, 90 );
#	quickRotate ( $save_name_m, 90 );
#	quickRotate ( $save_name_t, 90 );

#	exit;
	Header('Location: '.$BASE_URI.'/c'.$path.'?r');
} elseif ($action === 'd') {

	if (!legal_image($pictures_path.$d, $pictures_path)) die('Zugriffsfehler');

	// Filename for thumbnail to save to
	$save_name_m = $thumbs_path.'/m'.str_replace('/','_',$pictures_path.$path);
	$save_name_t = $thumbs_path.'/'.str_replace('/','_',$pictures_path.$path);

	unlink ( $save_name_m );
	unlink ( $save_name_t );

#	exit;
  Header('Location: '.$BASE_URI.'/c'.$path.'?r');
} elseif ($action === 'j') {
  header("Content-Type: application/json");
  echo json_encode([
      "files" => get_dir($pictures_path, $path, 'f'),
      "dirs" => get_dir($pictures_path, $path, 'f')
    ]);

} else {
	$arr = Array();
	$arr['pagetitle'] = $path;
	$arr['navigation'] .= show_directory($pictures_path, $path);
	$arr['content'] .= show_pictures($pictures_path, $path);
	$arr['breadcrumb'] .= show_breadcrumb($path);
	echo make_page($arr);
}



?>
