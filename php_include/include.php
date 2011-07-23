<?php
function outbound_link($url, $title) {
  echo <<<LINK
<a href="/r/$url" onClick="javascript:pageTracker._trackPageview('outbound/$url');">$title</a>
LINK;
}

function html_header($title) {
  $domain = $_SERVER['HTTP_HOST'];
  $urchin = "";

  if ($domain == "dreadpiratekermit.org") {
    $urchin = <<<KERMIT
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-24459302-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.a
sync = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://w
ww') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefor
e(ga, s);
  })();

</script>
KERMIT;
  }


  echo <<<HEAD
<html>
<head>
<title>$title</title>
 <style type="text/css"><!--
  .title { text-align: left; font-family: verdana,arial,helvetica,sans-serif; color: #800000; font-size: larger; font-weight: bold; padding-bottom: 1em }
  .paragraph { text-align: left; font-family: verdana,arial,helvetica,sans-serif; padding-bottom: 1em }
  BODY { text-align: left; color: #000000 }
  DIV { padding-bottom: 1em }
  .quote { color: #000000; padding-left: 5em; padding-top: 1em; }
  .caption { color: #000000; font-size: smaller; text-align: center; font-weight: bold; }
  .pageflow_td { text-align: left; vertical-align: top }
  .images TD { text-align: center; vertical-align: top; width: 33% }
  .thumbnail { float: left; margin: 0 15px 15px 0; padding: 5px }
  UL { margin-top: 0; padding-top: 0 }

 --></style>
$urchin
</head>
<body bgcolor="#FFFFFF">

<div class="title">$title</div>

<table border=0 width="100%"><tr><td class="pageflow_td">
<!-- intro text -->
HEAD;

}

function html_footer_simple() {
  $domain = $_SERVER['HTTP_HOST'];
  echo <<<FOOT
</td></tr></table>

<br clear="all">
&nbsp;
<p>
<hr size=1>
<a href="/">$domain home</a><br />

</body>
</html>
FOOT;

}
function html_footer($urchin_code = "UA-135804-1", $showads = 1) {
  $domain = $_SERVER['HTTP_HOST'];
  $urchin = "";

  if ($domain == "dreadpiratekermit.org") {
    $showads = 0;
  } else {
    $urchin = <<<URCHIN
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("$urchin_code");
pageTracker._initData();
pageTracker._trackPageview();
</script>
URCHIN;
  }


  echo <<<FOOT
</td><td>&nbsp;</td>

<td valign="top" align="right" width="160">
FOOT;

  if ($showads) {
echo <<<FOOT_ADS
<div><script type="text/javascript"><!--
google_ad_client = "pub-7794600838135275";
google_alternate_ad_url = "http://rcm.amazon.com/e/cm?t=perljamnet-20&o=1&p=14&l=bn1&mode=books&browse=27&fc1=&lc1=&lt1=_blank&nou=1&f=ifr&bg1=&f=ifr";
google_ad_width = 160;
google_ad_height = 600;
google_ad_format = "160x600_as";
google_ad_type = "text_image";
google_ad_channel ="6855741522";
google_color_border = "FFFFFF";
google_color_bg = "FFFFFF";
google_color_link = "000000";
google_color_url = "336699";
google_color_text = "333333";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">

</script></div>
FOOT_ADS;
  }

echo <<<FOOT2


</td></tr></table>


<br clear="all">
&nbsp;
<p>
<hr size=1>
<a href="/">$domain home</a><br />

<br />
$urchin

</body>
</html>
FOOT2;

}

function show_images($file, $size = 'med', $match = '//') {
  $table_head = 0;
  $show_footer = 0;
  $fh = fopen($file,'r');
  $colCount = 0;
  $inRow = 0;

  if(strlen($_REQUEST[image])) {
    showImagePage($_REQUEST[image]);
    return;
  }

  if (! $fh) { die("fopen failed for $file\n"); }

  while (! feof($fh)) {
    $s = rtrim(fgets($fh, 1024));
    list($file,$caption,$extra) = explode("\t", $s);

    if ($file == 'PAGETITLE') {
      html_header($caption);
      continue;
    } elseif ($file == 'FOOTER') {
      ++$show_footer;
    } elseif (! preg_match($match, $file)) {
      continue;
    }

    if (!$table_head++) {
      echo "<table border=0 width=\"100%\" align=\"center\" class=\"images\">\n";
    }

    if ($file == 'PARAGRAPH' || $file == 'SECTION' || $file == 'TEXT' || $extra == 'PANO' || $file == 'PARENT' || $file == 'ANCHOR') {
      if ($inRow) {
        echo " </tr>\n";
      }
      #$align = "";
      #if ($file == 'PARENT') { $align = 'align="left"'; }

      echo " <tr><td colspan=3>";
      if ($file == 'PARAGRAPH') {
        echo "<div class=\"paragraph\">$caption</div>";
      } elseif($file == 'ANCHOR') {
        echo "<a name=\"$caption\"></a>";
      } elseif($file == 'PARENT') {
        echo "<div align=\"left\"><a href=\"$caption\">Go back</a></div>";
      } elseif($extra == 'PANO') {
        echo build_image2($file, $caption, 'small', 'med');
      } else {
        echo "<div class=\"title\">$caption</div>";
      }
      echo "</td></tr>\n";

      $colCount = 0;
      $inRow = 0;
      continue;
    } elseif ($colCount == 0 || $inRow == 0) {
      echo " <tr>\n";
      ++$inRow;
    }

    $thumb = 'thumb3/' . $file;
    $med = $size . '/' . $file;

    $str = build_image2($file, $caption);
    echo "<td>$str</td>\n";
    ++$colCount;
echo "<!-- cc: $colCount -->\n";

    if ($inRow > 0 && $colCount > 2) {
      echo " </tr>\n";
      $inRow = 0;
      $colCount = 0;
    }
  }

  echo "</table>\n";
  if ($show_footer) {
    html_footer();
  }
}

function build_image2($image, $caption, $extra = 'thumb3') {
  $str = "";

  $thumb = "$extra/$image";
  if (file_exists($thumb) && is_file($thumb)) {
    list(,,, $size) = getimagesize($thumb);

      $str .= "<a href=\"?image=$image\"><img src=\"$thumb\" border=0 $size alt=\"$caption\" title=\"$caption\"></a>";

    if ($caption) {
      $str .= " <div class=\"caption\">$caption</div>\n";
    }

  }

  return $str;
}

function build_image($thumb, $med, $caption) {

  #return undef unless (strlen $thumb);

  $str = "";
  #$thumb .= 'asdf';

  if (file_exists($thumb) && is_file($thumb)) {
    list(,,, $size) = getimagesize($thumb); # || die "couldn't read $thumb";

    #$str = "<div class=\"thumbnail\">\n ";
    $str = "  <td>";

    if (file_exists($med)) {
      $str .= "<a href=\"$med\">";
    }

    $str .= "<img src=\"$thumb\" border=0 $size alt=\"$caption\" title=\"$caption\">";
    if (file_exists($med)) {
      $str .= "</a>";
    }

    if ($caption) {
      $str .= " <div class=\"caption\">$caption</div>\n";
      #$str .= " <caption align=\"bottom\">$caption</caption>\n";
    }

    #$str .= "</div>\n";
    $str .= "</td>\n";
  }

  return $str;
}

function getCaptions($imageFile) {
  $captionFile = $_SERVER['SCRIPT_FILENAME'];
  $captionFile = preg_replace('/.php$/', '', $captionFile);
  $captionFile .= '.txt';

  if (!file_exists($captionFile)) {
    $captionFile = preg_replace('/(.*\/).*?\.txt$/', '\\1images.txt', $captionFile);
  }

  $fh = fopen($captionFile,'r');
  $ret['prev'] = null;
  $ret['curr'] = null;
  $ret['next'] = null;

  if (! $fh) { die("fopen failed for $captionFile\n"); }
  $ret['_PARENT'] = null;
  $ret['_PAGETITLE'] = null;

  while (! feof($fh)) {
    $s = rtrim(fgets($fh, 1024));
    list($this['file'],$this['caption'],$this[extra]) = explode("\t", $s);

    if ($this[file] == 'PARAGRAPH' || $this[file] == 'FOOTER') { continue; }
    if (! strcmp($this['file'],'PARENT')) {
      $ret['_PARENT'] = $this['caption'];
      continue;
    } elseif (! strcmp($this['file'],'PAGETITLE')) {
      $ret['_PAGETITLE'] = $this['caption'];
      continue;
    } elseif (! strcmp($imageFile,$this['file'])) {
      $ret['curr'] = $this;
    } elseif ($ret['curr']['file']) {
      $ret['next'] = $this;
      break;
    } else {
      $ret['prev'] = $this;
    }

  }

  #output_iptc_data($file);
  return $ret;
}

function backURL($back = '') {
  $lastPage = $back;
  if (!strlen($lastPage)) {
    $lastPage = $_REQUEST['back'] ? $_REQUEST['back'] : '.';
  }

  return $lastPage;
}

function showImagePage($file) {
  $c = getCaptions($file);

  $caption = $c['curr']['caption'];
  $size = "small";
  if ($c[curr][extra] == 'PANO') { $size = 'med'; }
  $img = preg_replace('/(.*\/)?(.*)$/', "\\1$size/\\2", $file, 1);
  if(! file_exists($img)) {
    $img = $file;
  }

  $exif = readExif($img);

  $title = $c['_PAGETITLE'];
  if ($caption) { $title .= ': ' . $caption; }
  html_header($title);

  $date = $exif["EXIF"]["DateTimeOriginal"];
  list($y, $m, $d, $H, $M, $S) = split('[: ]', $date);
  if ($m && $d && $y) {
    $pretty_date = "$m/$d/$y $H:$M";
    #$pretty_date = "$date";

  }

  list($lat, $lon) = exifLocation($exif);
  $back = backURL();

  $h = $exif['COMPUTED']['Height'];
  $w = $exif['COMPUTED']['Width'];

  if($c[next][file]) {
    $next = '<a href="' . $_SERVER[SCRIPT_URI] . '?image=' . $c[next][file] . '">See next picture</a>';
  }

  if($c[prev][file]) {
    $prev = '<a href="' . $_SERVER[SCRIPT_URI] . '?image=' . $c[prev][file] . '">See previous picture</a>';
  }

  if($lat && $lon) {
    $loc = 'Location: <a href="http://maps.google.com/maps?q=' . $lat . ',' . $lon . '&um=1&ie=UTF-8&sa=N&tab=wl&z=11">See location on map</a>';
  }

  if ($pretty_date) {
    $print_date = "Date Taken: $pretty_date";
  }

  if (strlen($back)) {
    $print_back = "<a href=\"$back\">Back to index</a>";
  }

  foreach (array($prev, $print_back, $loc, $print_date, $next) as $e) {
    if (strlen($e)) {
      if (strlen($legend)) {
        $legend .= ' | ';
      }
      $legend .= $e;
    }
  }
  #$legend = implode(' | ', array($prev, $loc, $print_date, $next));
  $camera = "";
  $details = "";
  $make = $exif["IFD0"]["Make"];
  $model = $exif["IFD0"]["Model"];
  if ($make || $model) {
    if ( strpos($model, $make) === false ) {
      $camera = "Camera: " . $exif["IFD0"]["Make"] . " " . $exif["IFD0"]["Model"] . "<br />\n";
    } else {
      $camera = "Camera: " . $exif["IFD0"]["Model"] . "<br />\n";
    }
  }
  if ($exif["EXIF"]["ExposureTime"]) {
    $details .= "Exposure: " . $exif["EXIF"]["ExposureTime"] . "<br />\n";
  }
  if ($exif["EXIF"]["ISOSpeedRatings"]) {
    $details .= "ISO: " . $exif["EXIF"]["ISOSpeedRatings"] . "<br />\n";
  }

  $tag = "UndefinedTag:0x0095";
  if ($exif["MAKERNOTE"] && $exif["MAKERNOTE"][$tag]) {
    $details .= "Lens: " . $exif["MAKERNOTE"][$tag] . "<br />\n";
  }

echo <<<IMG
<div><nobr>$legend</nobr></div> <!-- </td></tr></table> -->
<table border=0 width="$w"><tr><td><a href="$back"><img src="$img" border=0 width="$w" height="$h" alt="$caption" title="$caption" /></a></td></tr>
<tr><td><div class="caption">$caption</div>
<div><nobr>$legend</nobr><br /><br /><br />
$camera
$details</div></td></tr></table>
IMG;



  // Display all exif data if it is requested
  if ($_REQUEST['debug']) {
    foreach ($exif as $key => $section) {
      foreach ($section as $name => $val) {
        echo "$key.$name: $val<br />\n";
      }
    }
  }

  html_footer();

}

function readExif($file) {
  if (! file_exists($file)) {
    error_log("file not found: $file");
  }
  $exif = exif_read_data($file, 0, true);
  return $exif;
}


function exifLocation($exif) {
  $lat = degree2decimal(
    $exif["GPS"]["GPSLatitude"][0],
    $exif["GPS"]["GPSLatitude"][1],
    $exif["GPS"]["GPSLatitude"][2],
    $exif["GPS"]["GPSLatitudeRef"]
  );
  $lon = degree2decimal(
    $exif["GPS"]["GPSLongitude"][0],
    $exif["GPS"]["GPSLongitude"][1],
    $exif["GPS"]["GPSLongitude"][2],
    $exif["GPS"]["GPSLongitudeRef"]
  );

  return array($lat, $lon);
}

# exif/GPS/geo code from here: http://www.happygolucky.no/main/2008/01/31/automatic-geo-tagging-in-wordpress-from-nokia-n95/
function degree2decimal($degrees, $minutes, $seconds, $direction) {
  if (! $degrees) { return; }
  $slashpos=strpos($seconds,'/');
  $sec1 = substr($seconds,0,$slashpos);
  $sec2 = substr($seconds,$slashpos+1,strlen($seconds));

  $seconds = $sec1 / $sec2;

  //echo “DIR: “.$direction;

  $seconds=($seconds/60);
  $minutes=($minutes+$seconds);
  $minutes=($minutes/60);
  $decimal=($degrees+$minutes);
  //South latitudes and West longitudes need to return a negative result
  if (($direction=="S") or ($direction=="W")) { $decimal=$decimal*(-1); }
  return $decimal;
}

function iptcCaption($file) {
  $caption = '';
  $size = getimagesize ( $file, $info);       
  if(is_array($info)) {   
    $iptc = iptcparse($info["APP13"]);
    $c = split('/\0/', $iptc['2#120']['0']);
    $caption = $c[0];
#echo $caption;
  }

  return $caption;
}

function output_iptc_data( $file ) {   
  $size = getimagesize ( $file, $info);       
  if(is_array($info)) {   
    $iptc = iptcparse($info["APP13"]);
    foreach (array_keys($iptc) as $s) {             
      $c = count ($iptc[$s]);
      for ($i=0; $i <$c; $i++) {
        echo $s.' = '.$iptc[$s][$i].'<br>';
      }
    }                 
  }            
}

?>
