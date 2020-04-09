<?php
/*
Plugin Name: RaSolo Helper
Plugin URI: http://ra-solo.ru
Description: The useful functions
Text Domain: rasolo-helper
Domain Path: /languages
Version: 1.1
Author: Andrew Galagan
Author URI: http://ra-solo.com.ua
License: GPL2
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : eastern@ukr.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// if ( !class_exists( 'Rasolo___' ) ) {
// class Rasolo___
//        {
//        } // // The end of RasoloAdminMessages
// }

if(!defined('OneYear'))define('OneYear', 31104000);
if(!defined('OneMonth'))define('OneMonth', 2592000);
if(!defined('OneWeek'))define('OneWeek', 604800);
if(!defined('OneDay'))define('OneDay', 86400);
if(!defined('OneHour'))define('OneHour', 3600);
if(!defined('OneMinute'))define('OneMinute', 60);

if(!function_exists('get_domain_core')){
function get_domain_core()
       {
$known_zones=array('ru','ua','in','com','net','kh','org','kharkov','kharkiv','center');
$srv_name=$_SERVER["SERVER_NAME"];
$srv_arr=array_reverse(explode('.',$srv_name));
$arr_for_ret=array();
foreach ($srv_arr as $prt){
    if(in_array($prt,$known_zones))continue;
    $arr_for_ret[]=str_replace('-','_',$prt);
};
if(empty($arr_for_ret)){
	return 'unknown_domain';
} else {
	$prt_for_ret=implode('_',$arr_for_ret);
	if(strlen($prt_for_ret)>20){
		$prt_for_ret=substr($prt_for_ret,0,20);
	};
	return $prt_for_ret;
};
return false;
       };  // The end of get_domain_core
};

if(!function_exists('is_img_url')){
function is_img_url($susp_img_url)
       {
if(substr($susp_img_url,-4)=='.jpg')return true;
if(substr($susp_img_url,-5)=='.jpeg')return true;
if(substr($susp_img_url,-4)=='.png')return true;
if(substr($susp_img_url,-4)=='.gif')return true;
return false;
       };  // The end of is_img_url
};

if(!function_exists('SecondsToTime')){
function SecondsToTime($seconds, $num_units=3)
       {
$time_descr = array(
            "years" => floor($seconds / OneYear),
            "months" => floor(($seconds%OneYear) / OneMonth),
            "weeks" => floor(($seconds%OneMonth) / OneWeek),
            "days" => floor(($seconds%OneWeek) / OneDay),
            "hours" => floor(($seconds%OneDay) / OneHour),
            "mins" => floor(($seconds%OneHour) / OneMinute),
            "secs" => floor($seconds%OneMinute),
            );

$russian_tax=array(
            "years" => 'лет',
            "months" => 'месяцев',
            "weeks" => 'недель',
            "days" => 'дней',
            "hours" => 'часов',
            "mins" => 'минут',
            "secs" => 'секунд',
            );

$res = "";
$counter = 0;

foreach ($time_descr as $k => $v) {
    if ($v) {
        $res.=$russian_tax[$k].' '.$v;
        $counter++;
        if($counter>=$num_units)
            break;
        elseif($counter)
            $res.=", ";
    };
};
$res=rtrim($res);
$res=rtrim($res,',');
return $res;
       };   // The end of SecondsToTime
};

if(!function_exists('verify_ip')){
function verify_ip($iplist=array())
       {
//           '176.102.32'
if(count($iplist)==0){
	$iplist=array(
           '80.77.*',
           '188.0.*',
           '46.164.*',
           '77.222.*',
           '195.114.*'
    );
};

$addr_parts=explode('.',$_SERVER['REMOTE_ADDR']);
// myvar_dump($addr_parts,'$addr_parts', true);
// myvar_dump($iplist,'$iplist', true);
if(!isset($addr_parts[2]))return false;
list($remote_part0,$remote_part1,$remote_part2)=$addr_parts;

foreach($iplist as $nth_ip) {
    $tmpl_parts=explode('.',$nth_ip);
    if(!isset($tmpl_parts[2]))return false;
    list($tmpl_part0,$tmpl_part1,$tmpl_part2)=$tmpl_parts;
    $log_expr0= ((intval($remote_part0)==intval($tmpl_part0)) || ($tmpl_part0=='*'));
    $log_expr1= ((intval($remote_part1)==intval($tmpl_part1)) || ($tmpl_part1=='*'));
    $log_expr2= ((intval($remote_part2)==intval($tmpl_part2)) || ($tmpl_part2=='*'));
    if( $log_expr0 && $log_expr1 && $log_expr2 )return true;
};
return false;
       }; // The end of verify_ip
};

if(!function_exists('get_dateselect')){
function get_dateselect($time_to_select=1430000000,$this_date_id='auto',
                        $day_lab='Выберите день',
                        $month_lab='Месяц',
                        $year_lab='Год',
                        $hour_lab='Часы',
                        $min_lab='Минуты',
                        $sec_lab='Секунды',
                        $show_time=true)
       {
    //  Процедура выдает HTML-код для выбора даты
//  $time_to_select - время, которое надо отметить в выходном тексте опцией 'selected'

global $full_mon;

if($this_date_id=='auto'){
    $this_date_id=uniqid();
};

$item_before='<div class="date_select_item">'.chr(10).'<label for="';
$item_after='</div>'.chr(10);
$endoflab='</label>'.chr(10);

$input_day=$item_before.'day_choice_'.$this_date_id.'">'.$day_lab.$endoflab.
            '<select name="day_choice_'.$this_date_id.
            '" id="day_choice_'.$this_date_id.'">'.chr(10);
for ($i=1;$i<=31;$i++){

    if (intval(date('j',$time_to_select))==$i){
        $input_day.='<option selected value="'.$i.'">'.$i.'</option>'.chr(10);
    } else {
        $input_day.='<option value="'.$i.'">'.$i.'</option>'.chr(10);
    };

};
$input_day.='</select>'.chr(10).$item_after;
//javascript:
$input_month=$item_before.'month_choice_'.$this_date_id.'">'.$month_lab.$endoflab.
    '<select onchange="adjust_datachoice(\''.$this_date_id.'\')"'.
    ' name="month_choice_'.$this_date_id.'" id="month_choice_'.$this_date_id.'">'.chr(10);
for ($i=1;$i<=12;$i++){
    if (intval(date('n',$time_to_select))==$i){
        $input_month.='<option selected value="'.$i.'">'.$full_mon[$i].'</option>'.chr(10);
    } else {
        $input_month.='<option value="'.$i.'">'.
            $full_mon[$i].'</option>'.chr(10);
    };
};
$input_month.='</select>'.chr(10).$item_after;

$input_year=$item_before.'year_choice_'.$this_date_id.'">'.
            $year_lab.$endoflab.'<select onchange="adjust_datachoice(\''.
            $this_date_id.'\')" name="year_choice_'.$this_date_id.'" id="year_choice_'.
            $this_date_id.'">'.chr(10);
$yearstart=intval(date('Y'))-5;
$yearend=$yearstart+17;
for ($i=$yearstart;$i<=$yearend;$i++){
    if (intval(date('Y',$time_to_select))==$i){
        $input_year.='<option selected value="'.$i.'">'.$i.'</option>'.chr(10);


    } else {
        $input_year.='<option value="'.$i.'">'.$i.'</option>'.chr(10);
    };

};
$input_year.='</select>'.chr(10).$item_after;

$date_select_txt=$input_day.$input_month.$input_year.chr(10);
//    myvar_dump($date_select_txt,'$date_select_txt');


if($show_time){

//    myvar_dump($time_to_select,'$time_to_select');
    $this_hours=intval(date('H',$time_to_select));

    $this_minutes=intval(date('i',$time_to_select));
//    myvar_dump($this_minutes,'$this_minutes__555_');
    $this_seconds=intval(date('s',$time_to_select));
//    myvar_dump($this_seconds,'$this_seconds__666_');

    $date_select_txt.=$item_before.'hours_choice_'.
                $this_date_id.'">'.$hour_lab.$endoflab.
        '<input id="hours_choice_'.$this_date_id.
            '" name="hours_choice_'.$this_date_id.'" type="number"'.
        ' min="0" max="23" value="'.$this_hours.'"> '.chr(10).$item_after.
        $item_before.'mins_choice_'.$this_date_id.'">'.$min_lab.$endoflab.
        '<input id="mins_choice_'.$this_date_id.'" name="mins_choice_'.$this_date_id.'" type="number"'.
        ' min="0" max="59" value="'.$this_minutes.'"> '.chr(10).$item_after.
        $item_before.'secs_choice_'.$this_date_id.'">'.$sec_lab.$endoflab.
        '<input id="secs_choice_'.$this_date_id.'" name="secs_choice_'.$this_date_id.'" type="number"'.
        ' min="0" max="59" value="'.$this_seconds.'">'.chr(10).$item_after;
};
return '<div class="date_selection">'.chr(10).$date_select_txt.'</div>'.chr(10);

//    $bbbbb=htmlspecialchars($input_year);
//    myvar_dump($bbbbb,'$bbbbb',true);
//    $aaaaa=intval(date('o',$time_to_select));
//    myvar_dump($aaaaa,'$aaaaa',true);
//    myvar_dump($i,'$i',true);

       };  // Окончание процедуры get_dateselect
};

if(!function_exists('catch_date_data')){
function catch_date_data($date_id='auto')
       {
// This function scans the $_POST array and returns the first appropriate date value
//$cur_time=intval(time());

if(empty($date_id))return false;
$this_year=false;
$this_mnth=false;
$this_day=false;
$this_hours=false;
$this_mins=false;
$this_secs=false;
foreach($_POST as $nth_key=>$nth_post){

//    $key_substr=substr($nth_key,0,11);
//    $is_equal=substr($nth_key,0,11)=='month_choice_';
//    $is_equal2=$key_substr=='month_choice_';
    if(empty($nth_post))continue;
    if(!is_numeric($nth_post))continue;
    $int_nth_post=intval($nth_post);
    if($date_id=='auto'){
        if(substr($nth_key,0,12)=='year_choice_'){
              if($int_nth_post>1900 || $int_nth_post<3000){
                  $this_year=$int_nth_post;
                  if(!$this_year)$this_year=false;
              };
        } else if(substr($nth_key,0,13)=='month_choice_') {
              if($int_nth_post>0 || $int_nth_post<32){
                   $this_mnth=$int_nth_post;
                   if(!$this_mnth)$this_mnth=false;
              };
        } else if(substr($nth_key,0,11)=='day_choice_') {
              if($int_nth_post>0 || $int_nth_post<32){
                   $this_day=$int_nth_post;
                   if(!$this_day)$this_day=false;
              };
        } else if(substr($nth_key,0,13)=='hours_choice_') {
              if($int_nth_post>=0 || $int_nth_post<24){
                   $this_hours=$int_nth_post;
                   if(!$this_day)$this_day=false;
              };
        } else if(substr($nth_key,0,12)=='mins_choice_') {
              if($int_nth_post>=0 || $int_nth_post<60){
                   $this_mins=$int_nth_post;
                   if(!$this_day)$this_day=false;
              };
        } else if(substr($nth_key,0,11)=='secs_choice_') {
              if($int_nth_post>=0 || $int_nth_post<60){
                   $this_secs=$int_nth_post;
                   if(!$this_day)$this_day=false;
              };
        };
//        myvar_dump($nth_key,'$nth_key',0,1);
//        myvar_dump($key_substr,'$key_substr',0,1);
//        myvar_dump($is_equal,'$is_equal');
//        myvar_dump($is_equal2,'$is_equal2');

    } else {
        $date_id_len=strlen($date_id);

//        myvar_dump($nth_key,'--------------$nth_key');
//        myvar_dump($nth_post,'$nth_post');
//        myvar_dump($date_id_len,'$date_id_len');
//        $rrr1=substr($nth_key,0,11+$date_id_len);
//        $rrr2='mins_choice_'.$date_id;
//        myvar_dump($rrr1,'$rrr1');
//        myvar_dump($rrr2,'$rrr2');

        if(substr($nth_key,0,12+$date_id_len)=='year_choice_'.$date_id){
              if($int_nth_post>1900 || $int_nth_post<3000){
                  $this_year=$int_nth_post;
              };
        } else if(substr($nth_key,0,13+$date_id_len)=='month_choice_'.$date_id) {
              if($int_nth_post>0 || $int_nth_post<32){
                   $this_mnth=$int_nth_post;
              };
        } else if(substr($nth_key,0,11+$date_id_len)=='day_choice_'.$date_id) {
              if($int_nth_post>0 || $int_nth_post<32){
                   $this_day=$int_nth_post;
              };
        } else if(substr($nth_key,0,13+$date_id_len)=='hours_choice_'.$date_id) {
              if($int_nth_post>0 || $int_nth_post<32){
                   $this_hours=$int_nth_post;
              };
        } else if(substr($nth_key,0,12+$date_id_len)=='mins_choice_'.$date_id) {
              if($int_nth_post>0 || $int_nth_post<32){
                   $this_mins=$int_nth_post;
              };
        } else if(substr($nth_key,0,12+$date_id_len)=='secs_choice_'.$date_id) {
              if($int_nth_post>0 || $int_nth_post<32){
                   $this_secs=$int_nth_post;
              };
        };

    };

};
//myvar_dump($date_id,'$date_id');
//myvar_dump($this_hours,'$this_hours');
//myvar_dump($this_mins,'$this_mins');
//myvar_dump($this_secs,'$this_secs');
//myvar_dump($this_year,'$this_year');
//myvar_dump($this_mnth,'$this_mnth');
//myvar_dump($this_day,'$this_day');
if( empty($this_year) || empty($this_mnth) || empty($this_day)) return false;
$this_time = mktime($this_hours, $this_mins, $this_secs, $this_mnth, $this_day, $this_year);
//myvar_dump($this_time ,'$this_time___555__');
if($this_time !== false && $this_time <>-1 ){
    return intval($this_time);
} else {
    return false;
};

       };   // Окончание процедуры catch_date_data
};

if(!function_exists('rasolo_debug_to_file')){
    function rasolo_debug_to_file($value_to_debug=false,$var_name=false,$file_version_nmb=null,$need_hex=false)
        {
//static $ncalls;
//$ncalls++;
//if(!verify_ip()){
//    return;
//}
//myvar_dump($ncalls,'$ncalls');
//myvar_dump($var_name,'$var_name');
//myvar_dump($value_to_debug,'$ncalls');
//die('test_352356245');


$begin_tmpl='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="ru-RU" lang="ru-RU" >
<head>
	<title>Rasolo debug file</title>
</head>
<body>
<style>
#this_creation_time {
    display: inline-block; float: right;
}
#this_creation_time span {
    font-size: bold;
}
#debug_file_links a {
    color: grey;
    font-weight: bold;
}
#debug_file_links a:hover {
  color: brown;
}
#debug_file_links a {
    min-width: 300px;
    text-decoration: none;
}
#debug_file_links span {
    text-align: right;
    min-width: 150px;
}
#debug_file_links a,
#debug_file_links span {
    display: inline-block;
}
</style>
<div id="this_creation_time">Creation
     time is <span>'.date('Y-M-d H:i:s',current_time( 'timestamp' )).'</span></div>
';

$end_tmpl='</body></html>'.chr(10);

static $cntr=0;
$cntr++;

if('newtimes'==substr($var_name,0,8)){
    $file_version_nmb='_'.rasolo_precise_time().'_'.$cntr.'_'.$var_name;
} else if('newfiles'==$var_name){
    $file_version_nmb='_static_'.sprintf('%04d', $cntr);
} else if(in_array($var_name,array('partial_beg','partial','partial_end')) ){
    $file_version_nmb='partial';
} else if(strlen($var_name)>1 && (is_null($file_version_nmb) || strlen($file_version_nmb)==0)){
    $file_version_nmb='_'.substr($var_name,1);
} else if($file_version_nmb){
    $file_version_nmb='_'.$file_version_nmb;
} else {
    $file_version_nmb='_'.rasolo_precise_time().'_'.$cntr.'_else';
}

$upl_dir=rasolo_get_fresh_upload_dir();

//myvar_dump($upl_dir,'$upl_dir_3423223');
//die('$upl_dir');

if (!file_exists($upl_dir)) {
    mkdir($upl_dir, 0777);
//    echo "The directory $upl_dir was successfully created.";
//    exit;
//} else {
//    echo "The directory $upl_dir exists.";
}

//$debug_var_name=$var_name;
//$debug_var_name_len=strlen($var_name);
//$debug_gettype_value=gettype($value_to_debug);
//$debug_gettype_vn=gettype($file_version_nmb);
//$debug_isnull_vn=is_null($file_version_nmb);
//$test_filename=$upl_dir.'/d'.$file_version_nmb.'_'.$ncalls.'.html';

$debug_filename=$upl_dir.'/debug'.$file_version_nmb.'.html';

if(is_null($var_name)){

//    file_put_contents($test_filename.'_000null_'.$ncalls.substr($var_name,0,3),
//                '$ncalls_='.$ncalls.
//                ', $test_filename='.$test_filename.
//                ', $debug_var_name_len='.$debug_var_name_len.
//                ', $debug_var_name='.$debug_var_name.
//                ', $debug_gettype_vn='.$debug_gettype_vn.
//                ', $debug_isnull_vn='.$debug_isnull_vn.
//                ', $debug_gettype_value='.$debug_gettype_value.
//                '!'
//    );



    // let us create the root html document
    $root_filename='debug_root.html';
    $debug_filename=$upl_dir.'/'.$root_filename;

    $my_dir=scandir($upl_dir);
//    myvar_dump($my_dir,'$my_dir',1);


    $upl_url=rasolo_get_fresh_upload_dir(false);
    $html_files_descr='';
    foreach($my_dir as $nth_file_name){
        if(strlen($nth_file_name)<6){
            continue;
        }
        $file_info=pathinfo($nth_file_name);
        if(empty($file_info['extension'])){
            continue;
        }
        if(empty($file_info['filename'])){
            continue;
        }
//        myvar_dump($file_info,'$file_info',1);
        if('html'<>$file_info['extension']){
            continue;
        }
        if('debug_root'==$file_info['filename']){
            continue;
        }
        $file_time = date('Y-M-d H:i:s',filemtime($upl_dir. '/' . $nth_file_name));
        $file_size = filesize ($upl_dir. '/' . $nth_file_name);
//        myvar_dump($file_time ,'$files_time ',1);

        $html_files_descr.='<div><a href="'.$upl_url.'/'.$nth_file_name.'">'.$file_info['filename'].
                '.html</a> <span>'.$file_time.'</span> <span>'.$file_size.'</span></div>'.chr(10);
//        $point_pos=strrpos($nth_file_name, '.');
//        $required_length=strlen($nth_file_name)-$point_pos;
//        $file_extension
    }
    if(strlen($html_files_descr)>0){
        file_put_contents($debug_filename,$begin_tmpl.
        '<h2>The root debug file</h2>'.chr(10).
        '<h2>Such debug files are available:</h2>'.chr(10).
        '<div id="debug_file_links">'.chr(10).
        $html_files_descr.'</div>'.chr(10).
        $end_tmpl);
    } else {
        file_put_contents($debug_filename,$begin_tmpl.
            '<h2>The root debug file</h2>'.chr(10).
            '<h2>There are no appropriate files there.</h2>'.chr(10).
            $end_tmpl);
    }

    if(false && verify_ip()){
//        die('ssdasfasf2412366666');
        $root_href=$upl_url.'/'.$root_filename;
        add_action('wp_footer',function() use ($root_href){
?><div class="debug_footer"><a href="<?php echo $root_href;
            ?>">...</a></div>
        <?php
        },99);
    }

    return;

} else {
//    file_put_contents($test_filename.'_000isnotnull_'.$ncalls.substr($var_name,0,3),
//                '$ncalls_='.$ncalls.
//                ', $test_filename='.$test_filename.
//                ', $debug_var_name_len='.$debug_var_name_len.
//                ', $debug_var_name='.$debug_var_name.
//                ', $debug_gettype_vn='.$debug_gettype_vn.
//                ', $debug_isnull_vn='.$debug_isnull_vn.
//                ', $debug_gettype_value='.$debug_gettype_value.
//                '!'
//);

}

$value_type=gettype($value_to_debug);

//file_put_contents($test_filename.'_001_'.$ncalls,'$ncalls_='.$ncalls.', $value_type_='.$value_type);

$simple_types=array('integer','double','string');

if(strlen($file_version_nmb)>0 && 'partial'==$file_version_nmb){
    if('partial_beg'==$var_name){
        file_put_contents($debug_filename,$begin_tmpl);
    } else {
        $handle=fopen($debug_filename,'a');
        if('partial_end'==$var_name) {
            fputs($handle,$end_tmpl);
        } else if('partial'==$var_name){
            if(in_array($value_type,$simple_types)){
                fputs($handle,'<hr><h2>Value type: '.
                    $value_type.'</h2>'.chr(10).
                    '<h2>Value itself:</h2>'.chr(10).
                    '<h4>'.$value_to_debug.'</h4>'.chr(10));
            } else {
                fputs($handle,'<hr><h2>Value type:</h2>'.chr(10).
                    '<h4>'.$value_type.'</h4>'.chr(10).
                    '<h2>Value itself:<h2>'.chr(10).
                    '<div><pre>'.print_r($value_to_debug,true).
                    '</pre></div>'.chr(10));
            }
        }

    }
    return;
}

if($var_name){
    $var_name='<h2>The variable name</h2>'.chr(10).
        '<h4>'.$var_name.'</h4>'.chr(10);
}

//myvar_dump($debug_filename,'$begin_tmpl',1);
if(file_exists($debug_filename)){
    unlink($debug_filename);
}
//file_put_contents($test_filename.'_0002_'.$ncalls,'002 $ncalls_='.$ncalls.', 002$value_type_='.$value_type);

if(in_array($value_type,$simple_types)){
    $hex_wedge='';
    if($need_hex && 'string'==$value_type){
        $hex_wedge='<h2>Hex data:</h2>'.chr(10);
        if(strlen($value_to_debug)<10){
            $hex_wedge.='<table class="table_hex"><tbody><tr>'.chr(10);
            $arr_v = str_split($value_to_debug);
            foreach ($arr_v as $nth_byte) {
                $hex_wedge.='<td>'.bin2hex($nth_byte).'</td>'.chr(10);
            };
            $hex_wedge.='</tr><tr>'.chr(10);
            foreach ($arr_v as $nth_byte) {
                $hex_wedge.='<td>'.$nth_byte.'</td>'. chr(10);
            };
$hex_wedge.='</tr></tbody></table>' . chr(10);
        } else {
            $hex_wedge.='<p class="dump_hex"><b>The hex value is</b>: ';
            $arr_v = str_split($value_to_debug);
            foreach ($arr_v as $nth_byte) {
                $hex_wedge.=bin2hex($nth_byte) . ' ';
            };


            $hex_wedge.= '</p>' . chr(10);
            $hex_wedge.='<p class="dump_hex"><b>The letters are</b>:&nbsp; ';
            foreach ($arr_v as $nth_byte) {
                $hex_wedge.=$nth_byte . '&nbsp; ';
            };
            $hex_wedge.='</p>' . chr(10);
        }
    }
    file_put_contents($debug_filename,$begin_tmpl.$var_name.
        '<h2>Value type: '.$value_type.'</h2>'.chr(10).
        '<h2>Value itself:</h2>'.chr(10).
        '<h4>'.$value_to_debug.'</h4>'.chr(10).
        $hex_wedge.
        $end_tmpl);
    return;
}

//file_put_contents($test_filename.'_0003_'.$ncalls,'003 $ncalls_='.$ncalls.', 003$value_type_='.$value_type);

if('null'==$value_type){
    file_put_contents($debug_filename,$begin_tmpl.$var_name.
        '<h2>Value type: '.$value_type.'</h2>'.chr(10).
        '<h4>Null</h4>'.chr(10).
        $end_tmpl);
    return;
}

//file_put_contents($test_filename.'_0004_'.$ncalls,'004 $ncalls_='.$ncalls.', 004$value_type_='.$value_type);

if('boolean'==$value_type){
    file_put_contents($debug_filename,$begin_tmpl.$var_name.
        '<h2>Value type: Boolean</h2>'.chr(10).
        '<h2>Value itself:</h2>'.chr(10).
        '<h4>'.($value_to_debug?'True':'False').'</h4>'.chr(10).
        $end_tmpl);
    return;
}
//file_put_contents($test_filename.'_00005_'.$ncalls,'005 $ncalls_='.$ncalls.', 00$value_type_='.$value_type);

file_put_contents($debug_filename,$begin_tmpl.$var_name.
        '<h2>Value type:</h2>'.chr(10).
        '<h4>'.$value_type.'</h4>'.chr(10).
        '<h2>Value itself:<h2>'.chr(10).
        '<div><pre>'.print_r($value_to_debug,true).'</pre></div>'.chr(10).
        $end_tmpl);



       } // The end of rasolo_debug_to_file
}


if(!function_exists('myvar_dd')){
function myvar_dd($p1,$p2=false,$p3=false,$p4=false)
       {
if(false===$p2){
    myvar_dump($p1);
} else if(false===$p3) {
    myvar_dump($p1,$p2);
} else if(false===$p4){
    myvar_dump($p1,$p2,$p3);
} else {
    myvar_dump($p1,$p2,$p3,$p4);
}
die('The application dies with the myvar_dd function');
       }; // The end of myvar_dd
}

if(!function_exists('myvar_dump')){
function myvar_dump( $v,
                     $my_comment=' *** The variable name has been omitted ***',
                     $html_comment=false,
                     $hex_view=false )
       {
//           $html_comment - Where the dump should be in the html comment mode
if( (!isset($v)) || $v===null ){
  if($html_comment){
     echo '<!-- This variable ('.$my_comment.') does not exist!!! Type='.gettype($v).'. -->'.chr(10);
  } else {
     echo '<p>-- This variable('.$my_comment.') does not exist!!! Type='.gettype($v).'. ---</p>'.chr(10);
  };
  return false;
};
$trace = debug_backtrace();
$vLine = file( __FILE__ );
$varname = '*** The name of this variable is unknown ***';
if(isset($trace[0]['line'])){
    if (isset($vLine[ $trace[0]['line'] - 1 ])) {
        $fLine = $vLine[ $trace[0]['line'] - 1 ];
        preg_match( "#\\$(\w+)#", $fLine, $match );
        if(isset($match[1])){
            $varname=$match[1];
        } else {

            $var_find_success=false;
            foreach($GLOBALS as $var_key_name => $value) {
                if ($value === $v) {
                    $varname=$var_key_name;
                    $var_find_success=true;
                    break;
                };
            };
            if(!$var_find_success) $varname='*** Unknown variable name ***';
        };
    };
};
if($html_comment){
    echo '<!-- '.$my_comment.chr(10).$varname.':'.chr(10);
} else {
    echo '<p class="dump_header"><b>'.$varname.'</b>: '.$my_comment.'</p>'.chr(10);
};

echo ($html_comment?chr(10):'<pre>').chr(10);
var_dump($v);
if ($hex_view) {
    if (is_string($v)){
        if(strlen($v)<60){
            echo '<table class="table_hex"><tbody><tr><td><b>The hex value is</b>:</td>'.chr(10);
            $arr_v = str_split($v);
            foreach ($arr_v as $nth_byte) {
                echo '<td>'.bin2hex($nth_byte).'</td>'.chr(10);
            };
            echo '</tr><tr><td><b>The letters are</b>:&nbsp;</td>';
            foreach ($arr_v as $nth_byte) {
                echo '<td>'.$nth_byte.'</td>'. chr(10);
            };
            echo '</tr></tbody></table>' . chr(10);
        } else {
            echo '<p class="dump_hex"><b>The hex value is</b>: ';
            $arr_v = str_split($v);
            foreach ($arr_v as $nth_byte) {
                echo bin2hex($nth_byte) . ' ';
            };
            echo '</p>' . chr(10);
            echo '<p class="dump_hex"><b>The letters are</b>:&nbsp; ';
            foreach ($arr_v as $nth_byte) {
                echo $nth_byte . '&nbsp; ';
            };
            echo '</p>' . chr(10);
        };

    } else {
        echo '<h3>The hexadecimal view is impossible due to type of debugged variable!<h3>'.chr(10);
    };

};

echo ($html_comment?chr(10).' -->':'</pre>').chr(10);
       }; // The end of procedure myvar_dump
}

if(!function_exists('this_plugin_url')){
function this_plugin_url()
       {

$plugin_url=plugins_url( ' ', __FILE__ ) ;

//$plugin_url=trim($plugin_url);
$plugin_url=trim($plugin_url,'/ ');
$site_url=$_SERVER['SERVER_NAME'];
$str_parts=explode($site_url,$plugin_url);
if(isset($str_parts[1])){
    return $str_parts[1].'/';
} else {
    return $plugin_url;
};
       }; // The end of the function this_plugin_url
};

if(!function_exists('this_theme_url')){
function this_theme_url()
       {

$theme_url=get_template_directory_uri();
$site_url=$_SERVER['SERVER_NAME'];

$str_parts=explode($site_url,$theme_url);

if(isset($str_parts[1])){
    return $str_parts[1].'/';
} else {
    return $theme_url;
};

       }; // The end of the function this_theme_url
};

if(!function_exists('is_this_image_url')){
function is_this_image_url($may_be_url)
       {
$last4=substr($may_be_url,-4);
if($last4=='.jpg')return true;
if($last4=='.png')return true;
$last5=substr($may_be_url,-5);
if($last5=='.jpeg')return true;
return false;
       }; // The end of the is_this_image_url
}

if(!function_exists('clear_input_str')){
function clear_input_str($str)
       {
$str = trim($str);
$str = stripslashes($str);
$str = strip_tags($str);
$str = htmlspecialchars($str);
$str = esc_sql($str);
return $str;
       };   // The end of clear_input_str
}

if(!function_exists('rasolo_set_admin_message_01')){
function rasolo_set_admin_message_01($rasolo_msg_content,
                                    $rasolo_msg_mode='info',
                                    $dismiss=false)
       {
global $rasolo_messages_01;
//           die('rasolo_set_admin_message_01');
if(empty($rasolo_msg_content))return;
if(!isset($rasolo_messages_01))$rasolo_messages_01=array();
if(empty($rasolo_msg_mode))return;
$rasolo_messages_01[]=array(
    'msg_txt'=>$rasolo_msg_content,
    'is_dismiss'=>$dismiss,
    'msg_mode'=>$rasolo_msg_mode
);
       } // The end of rasolo_set_admin_message_01
};

if(!function_exists('rasolo_display_admin_messages_01')){
function rasolo_display_admin_messages_01()
       {
global $rasolo_messages_01;

if(!current_user_can('edit_others_posts') &&
	!current_user_can('view_woocommerce_reports') )return;
$msg_types=array(
    'error'=>'error',
    'warning'=>'warning',
    'success'=>'success',
    'info'=>'info',
);

if(!isset($rasolo_messages_01))$rasolo_messages_01=array();
foreach ($rasolo_messages_01 as $msg_key=>$nth_msg) {

//    myvar_dump($nth_msg,'$nth_msg');
//    die('$nth_msg');
    if(in_array($nth_msg['msg_mode'],array_flip($msg_types))){
        $msg_mode=$nth_msg['msg_mode'];
    } else {
        $msg_mode='info';
    };
    if(empty($nth_msg['msg_txt']))continue;

    ?>
    <div class="notice notice-<?php
        echo $msg_types[$msg_mode].($nth_msg['is_dismiss']?' is-dismissible':'');
        ?>">
        <p><strong><?php echo $nth_msg['msg_txt'] ?></strong></p><?php
        if($nth_msg['is_dismiss']){
          ?><button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Убрать это сообщение.</span>
        </button><?php
        };
        ?>
    </div>
<?php
	unset($rasolo_messages_01[$msg_key]);
};
       }; // The end of rasolo_display_admin_messages
};

if(!function_exists('is_action_exists')){
function is_action_exists($action_in_quest,$callback_func_in_quest=false)
       {
// This function checks whether some action/filter exists
// You can verify the existance of at least one action without respect to callback procedure name
//     Just do not specify the second parameter for this
global $wp_filter;
if(empty($action_in_quest))return false;
if(!isset($wp_filter[$action_in_quest]))return false;
$callback_object=$wp_filter[$action_in_quest];
if(!is_object($callback_object))return false;
$callback_array=$callback_object->callbacks;
if(empty($callback_array))return false;
if($callback_func_in_quest===false)return count($callback_array);
$cleared_priority_callbacks=array();
foreach($callback_array as $nth_arr){
    $cleared_priority_callbacks=array_merge($cleared_priority_callbacks,$nth_arr);
};
if(array_key_exists($callback_func_in_quest,$cleared_priority_callbacks))return true;
return false;
       }; // The end of is_action_exists
};
