#!/usr/bin/php
<?php
define ('DW_INC', '/var/www/html/devel/inc/');
require_once(DW_INC . 'init.php');

if (!function_exists('p_get_metadata')) {
   die("p_get_metadata not found\n");
}

$id =  $argc  >  1? $argv[1] : ':playground:playground';

$url = DOKU_URL ."doku.php?$id=";
define ("TOC_URL", $url);
echo $url ."\n";

$toc = p_get_metadata($id,'description tableofcontents');
//print_r($toc);

$current=0;
$start_level = 0;
echo  "<UL>\n";
foreach ($toc as $head) {
  $level =  p_our_item($head, $current,$id);
  if($start_level==0) $start_level = $level;
    
}
if($start_level != $level) echo "</UL>\n";
echo  "</UL>\n";

function p_our_item($h, &$n,$id){
    if($n==0) $n=$h['level'];
  
    if($n < $h['level'] ) {
      echo "<UL>\n";             
    } 
    else if ($n != $h['level']) echo "</UL>\n";
    
    echo    '<li>' . format_link($h['title'], $h['hid'],$id) . '(', $h['level'] .")</li>\n";
    $n = $h['level'];
    return $n;
}

function format_link($title,$anchor,$id) {   
   $link = "<a href ='". TOC_URL . $id. '#' . $anchor."'>" . "$title</a>";
    return $link;
}