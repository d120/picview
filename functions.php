<?php
# # # # # # # # # # # # # # # # # # # # # # # # #
#           _    __     ___                     #
#     _ __ (_) __\ \   / (_) _____      __      #
#    | '_ \| |/ __\ \ / /| |/ _ \ \ /\ / /      #
#    | |_) | | (__ \ V / | |  __/\ V  V /       #
#    | .__/|_|\___| \_/  |_|\___| \_/\_/        #
#    |_| picView Version 0.4.20061212           #
#                                               #
# This is GPL.                                  #
# Visit http://scripte.arnep.de                 #
# # # # # # # # # # # # # # # # # # # # # # # # #


function bind_user($user,$pw){
global $ds;
  open_ldap_connection();
  $user = strtolower(trim($user));
  $userDN = get_user_dn($user);
  $ok = ldap_bind($ds, $userDN, $pw);
  if ($ok) $GLOBALS["boundUserDN"] = $userDN; else $GLOBALS["boundUserDN"] = FALSE;
  return $ok;
}

function get_user_dn($username) {
global $peopleBase;
  return "uid=$username,$peopleBase";
}

function open_ldap_connection() {
  global $ds, $ldapHost;
  if ($ds) return;
  $ds=ldap_connect($ldapHost);
  define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
}
function send_basicauth_header($realm) {
  header("WWW-Authenticate: Basic realm=\"$realm\"");
  header("HTTP/1.1 401 Unauthorized");
}
function check_basicauth() {
  global $galleryConfig;
  switch($galleryConfig['auth_required']) {
    case 'ldap':
      if ($_SERVER["PHP_AUTH_USER"] && $_SERVER["PHP_AUTH_PW"]) {
        if (bind_user($_SERVER["PHP_AUTH_USER"],  $_SERVER["PHP_AUTH_PW"])) return true;
      }
      send_basicauth_header("Please authenticate for $galleryConfig[title] with your LDAP account");
      return FALSE;
    case 'password':
      if ($_SERVER["PHP_AUTH_USER"] && $_SERVER["PHP_AUTH_PW"]) {
        //$correctPwHash = $galleryConfig['password'][$_SERVER["PHP_AUTH_USER"]];
        //if (crypt($_SERVER["PHP_AUTH_PW"], $correctPwHash) === $correctPwHash) return true;
        $correctPw = $galleryConfig['password'][$_SERVER["PHP_AUTH_USER"]];
        if ($correctPw === $_SERVER["PHP_AUTH_PW"]) return true;
      }
      send_basicauth_header("Please authenticate for $galleryConfig[title] with the given username and password");
      return FALSE;
    case '':
      return TRUE;
    default:
      die("Invalid Configuration");
  }
}
function require_basicauth() {
  if (!check_basicauth()) {
    echo "<div class='alert alert-danger'><h4>Please authenticate </h4>\n</div>";
    exit;
  }
}

function show_directory($path, $currpath) {
  global $pictures_path, $BASE_URI;
  $r = '';

	$currpath = str_replace('//','/',$currpath);
	$currpath = str_replace('\\\\','/',$currpath);

	$dir = get_dir($path, $currpath, 'd');

	// Backlink to parent directory
	if ($currpath != '/')
		$r .= '<li><a href="'.$BASE_URI.'/p'.$currpath.'../">[up]</a></li>'."\n";

	// Display directory list
	foreach($dir as $d) {
		if (!legal_image($path.$currpath.'/'.$d, $pictures_path)) {
			$r .= "#1 Illegaler Pfad! ($d)";
			return $r;
		}
		$r .= '<li><a href="'.$BASE_URI.'/p'.$currpath.$d.'/'.'">'.$d.'</a></li>'."\n";
	}
	return $r;
} // show_directory()

function show_pictures($path, $currpath) {
	global $pictures_path, $comments_path, $thumbs_path, $thumbs_per_page, $BASE_URI;

  $start = (int)$_GET['n'];
  $r = '';

	$dir = get_dir($path, $currpath, 'f');

	if (count($dir) == 0) {
		$r .= '<p>Keine Bilder in diesem Verzeichnis gefunden.</p>';
		$sub_dir = get_dir($path, $currpath, 'd');

		// Show pictures in subdirectories, if any
		$count1 = 0;
		foreach ($sub_dir as $d) {
			$s_d = get_dir($path, $currpath.'/'.$d, 'f');

			if (count($s_d) != 0) {
				if ($count1++ > 5) break;
				$_d = $d;
				$count2 = 0;
				$r .= '<a href="'.$BASE_URI.'/p'.$currpath.$_d.'/'.'">';
				$r .= "<h2>{$currpath}{$d}</h2>";
				$r .= "</a>\n";
        $r .= '<div class="img-gallery" style="width:100%"">';
				foreach($s_d as $s) {
					if ($count2++ > 5) break;
					$d = $_d.'/'.$s;
					if (!legal_image($path.$currpath.$d, $pictures_path)) {
						$r .= "<p>#2 Illegaler Pfad! ($s)</p>";
						return $r;
					}

					// Filename for comments to save to
					$comment_file = $comments_path.'/'.str_replace('/','_',$currpath.$d).'.txt';
					$number_comments = file_exists($comment_file) ?
					preg_match_all('/[0-9]{10}/', join('', file($comment_file)), $__t) : 0;

					// Get image size
					$save_name = $thumbs_path.'/'.str_replace('/','_',$path.$currpath.$d);
					$size = @getimagesize($save_name);
					$size[3] = $size[3];
			#		print_r($size);

					$r .= '<a href="'.$BASE_URI.'/c'.$currpath.$d.'">';
					$r .= '<img title="'.$number_comments.' Kommentare" src="'.$BASE_URI.'/t'.$currpath.$d.'?comments='.$number_comments.'" '.$size[3].' class="img'. ($size[0] < $size[1] ? 'h' : 'v') .'" />';
					$r .= "</a>\n";


				}
        $r .= '</div>';
        $r .= '<a href="'.$BASE_URI.'/p'.$currpath.$_d.'/'.'">';
				$r .= 'Mehr...';
				$r .= "</a>\n";
			}
		}
	}

	// Display images list

	$r .= '<p>';
	$max = count($dir);
	for($i = 0; $i < $max; $i += $thumbs_per_page) {
		$show_to = $i + $thumbs_per_page;
		if ($show_to > $max) $show_to = $max;
		if ($start >= $i && $start < $show_to)
			$r .= '<b>['. ($i + 1) .'-'. ($show_to) .']</b> ';
		else
			$r .= '[<a href="'.$BASE_URI.'/p'.$currpath.'?n='.($i).'">'. ($i + 1) .'-'. ($show_to) .'</a>] ';
	}
	$r .= '</p><div class="img-gallery">';

	$count = -1;
	foreach($dir as $d) {
		$count++;
		if ($count < $start || $count > $start + $thumbs_per_page) continue;
		if (!legal_image($path.$currpath.$d, $pictures_path)) {
			$r .= "#2 Illegaler Pfad! ($d)";
			return $r;
		}

		// Filename for comments to save to
		$comment_file = $comments_path.'/'.str_replace('/','_',$currpath.$d).'.txt';
		$number_comments = file_exists($comment_file) ?
		preg_match_all('/[0-9]{10}/', join('', file($comment_file)), $__t) : 0;

		// Get image size
		$save_name = $thumbs_path.'/'.str_replace('/','_',$path.$currpath.$d);
		$size = @getimagesize($save_name);
		$size[3] = $size[3];
#		print_r($size);

		$r .= '<a href="'.$BASE_URI.'/c'.$currpath.$d.'?n='.$start.'">';
		$r .= '<img title="'.$number_comments.' Kommentare" src="'.$BASE_URI.'/t'.$currpath.$d.'?comments='.$number_comments.'" '.$size[3].' class="img'. ($size[0] < $size[1] ? 'h' : 'v') .'" />';
		$r .= "</a>";
	}

	$r .= '</div><p>';
	$max = count($dir);
	for($i = 0; $i < $max; $i += $thumbs_per_page) {
		$show_to = $i + $thumbs_per_page;
		if ($show_to > $max) $show_to = $max;
		if ($start >= $i && $start < $show_to)
			$r .= '<b>['. ($i + 1) .'-'. ($show_to) .']</b> ';
		else
			$r .= '[<a href="'.$BASE_URI.'/p'.$currpath.'?n='.($i).'">'. ($i + 1) .'-'. ($show_to) .'</a>] ';
	}
	$r .= '</p>';

	$count = -1;

	return $r;
} // show_pictures()

function show_breadcrumb($path) {
  global $BASE_URI;
	$url = $BASE_URI.'/p/';
	$parts = explode("/", $path);
	$r = "<li><a href='$url'>PicView</a></li>";

	for($i = 1; $i < count($parts) - 1; $i++) {
		$url .= $parts[$i] . '/';
		$r .= "<li><a href='$url'>".$parts[$i]."</a></li>";
	}

	return $r;
}

function get_next_picture($path, $currpath, $img) {
	global $pictures_path, $comments_path, $thumbs_path, $BASE_URI;
	$dir = get_dir($path, $currpath, 'f');
	print_r($dir);

} // get_next_picture

function legal_image($img, $path) {
	// Convert paths to full path & quote for regex
	$realpath = str_replace('\\','/',realpath($img));
	$path = preg_quote(str_replace('\\','/',realpath($path)));

	// Picture not in path
	if (!preg_match("!^".$path."!", $realpath)) return FALSE;
	// Picture does not exist
	if (!file_exists($realpath)) return FALSE;

    // default
	return TRUE;
} // legal_image()

function image_write($im, $text, $size, $x, $y) {
	$white = ImageColorAllocate ($im, 0, 0, 0);
	$black = ImageColorAllocate ($im, 255, 255, 255);
	ImageString ($im, $size, $x+0, $y+0, $text, $black);
	ImageString ($im, $size, $x+2, $y+0, $text, $black);
	ImageString ($im, $size, $x+0, $y+2, $text, $black);
	ImageString ($im, $size, $x+2, $y+2, $text, $black);
	ImageString ($im, $size, $x+1, $y+1, $text, $white);
	return $im;
} // image_write()

function make_page_from_template($vars, $template) {
  foreach($vars as $x => $y)
		$template = str_replace('%' . $x . '%', $y, $template);
	return $template;
}

function make_page($vars) {
	global $template_file;
  $template = join('', file($template_file));
	return make_page_from_template($vars, $template);
}

function make_lightbox($vars) {
  global $lightbox_file;
  $template = join('', file($lightbox_file));
	return make_page_from_template($vars, $template);
}


// $imgSrc - GD image handle of source image
// $angle - angle of rotation. Needs to be positive integer
// angle shall be 0,90,180,270, but if you give other it
// will be rouned to nearest right angle (i.e. 52->90 degs,
// 96->90 degs)
// returns GD image handle of rotated image.
function ImageRotate_PV( $imagePath, $angle )
{

	$imgSrc=ImageCreateFromPNG($imagePath);

   // ensuring we got really RightAngle (if not we choose the closest one)
   $angle = min( ( (int)(($angle+45) / 90) * 90), 270 );

   // no need to fight
   if( $angle == 0 )
       return;

   // dimenstion of source image
   $srcX = imagesx( $imgSrc );
   $srcY = imagesy( $imgSrc );

   switch( $angle )
       {
       case 90:
           $imgDest = imagecreatetruecolor( $srcY, $srcX );
           for( $x=0; $x<$srcX; $x++ )
               for( $y=0; $y<$srcY; $y++ )
                   imagecopy($imgDest, $imgSrc, $srcY-$y-1, $x, $x, $y, 1, 1);
           break;

       case 180:
           $imgDest = ImageFlip( $imgSrc, IMAGE_FLIP_BOTH );
           break;

       case 270:
           $imgDest = imagecreatetruecolor( $srcY, $srcX );
           for( $x=0; $x<$srcX; $x++ )
               for( $y=0; $y<$srcY; $y++ )
                   imagecopy($imgDest, $imgSrc, $y, $srcX-$x-1, $x, $y, 1, 1);
           break;
       }

	ImageDestroy($imgSrc);
#	ImageInterlace($imgDest, 0);
	ImagePNG($imgDest, $imagePath);
	ImageDestroy($imgDest);
}

function quickRotate($imagePath,$angle){
exec('contert -rotate 90 /home/arnep/tmp/test.jpg /home/arnep/tmp/bla.jpg');

	if(!preg_match("/\.(png|jpg|jpeg|gif)$/i", $imagePath)) die('die h4X0r die!');
	exec("convert -rotate $angle $imagePath $imagePath");
	return;

	// Bilder werden immer als JPG abgespeichert
	$src_img=ImageCreateFromJPEG($imagePath);
#	if(preg_match("/\.(png)$/i", $imagePath)) $src_img=ImageCreateFromPNG($imagePath);
#	elseif(preg_match("/\.(jpg)$/i", $imagePath)) $src_img=ImageCreateFromJPEG($imagePath);
#	elseif(preg_match("/\.(bmp)$/i", $imagePath)) $src_img=ImageCreateFromWBMP($imagePath);
	$size=GetImageSize($imagePath);
	//note: to make it work on GD 2.xx properly change ImageCreate to ImageCreateTrueColor
	$dst_img = ImageRotate($src_img, $rtt, 0);
	$dst_img=ImageCreateTrueColor($size[1],$size[0]);
	if($rtt==90){
	 $t=0;
	 $b=$size[1]-1;
	 while($t<=$b){
	   $l=0;
	   $r=$size[0]-1;
	   while($l<=$r){
	       imagecopy($dst_img,$src_img,$t,$r,$r,$b,1,1);
	       imagecopy($dst_img,$src_img,$t,$l,$l,$b,1,1);
	       imagecopy($dst_img,$src_img,$b,$r,$r,$t,1,1);
	       imagecopy($dst_img,$src_img,$b,$l,$l,$t,1,1);
	       $l++;
	       $r--;
	   }
	   $t++;
	   $b--;
	 }
	}
	elseif($rtt==-90){
	 $t=0;
	 $b=$size[1]-1;
	 while($t<=$b){
	   $l=0;
	   $r=$size[0]-1;
	   while($l<=$r){
	       imagecopy($dst_img,$src_img,$t,$l,$r,$t,1,1);
	       imagecopy($dst_img,$src_img,$t,$r,$l,$t,1,1);
	       imagecopy($dst_img,$src_img,$b,$l,$r,$b,1,1);
	       imagecopy($dst_img,$src_img,$b,$r,$l,$b,1,1);
	       $l++;
	       $r--;
	   }
	   $t++;
	   $b--;
	 }
	}

	ImageDestroy($src_img);
	ImageInterlace($dst_img,0);
	ImageJPEG($dst_img,$imagePath);
	ImageDestroy($dst_img);
} // quickRotate()

function get_dir($path, $currpath, $type) {
	$cwd = getcwd();
	$handle=@opendir($path.$currpath);
	$dir = Array();
	@chdir($path.$currpath);
	while ($file = @readdir ($handle)) {
		if ($file != "." && $file != "..") {
			if ($type == 'f') {
				if (is_file(basename($file)) && preg_match("/\.(jpg|png)$/i",$file)) $dir[] = $file;
			} elseif ($type == 'd') {
				if (is_dir(basename($file))) $dir[] = $file;
			} else {
				$dir[] = $file;
			}

		}
	}
	@closedir($handle);
	sort($dir);
	chdir($cwd);
	return $dir;
} // get_dir()

?>
