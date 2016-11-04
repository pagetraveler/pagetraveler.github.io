
<?php
date_default_timezone_set('Asia/Taipei');
set_time_limit(300);
ini_set('memory_limit', '2048M');

require_once "src/vendor/multi-array/MultiArray.php";
require_once "src/vendor/multi-array/Factory/MultiArrayFactory.php";
require_once "src/class/Jieba.php";
require_once "src/class/Finalseg.php";
require_once "src/class/Posseg.php";

use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Posseg;
Jieba::init(array('mode'=>'default','dict'=>'big'));
Jieba::loadUserDict("user_dict.txt");
Finalseg::init();
Posseg::init();
    
$filename = 'lotterydata_recent.json';
            $handle = fopen($filename,'rb');
            $content = "";
            while (!feof($handle)) {
                    $content .= fread($handle, 10000000);
            }
            fclose($handle);

            $content = json_decode($content,true);
            #print_r($content[1]);
            # $eee="EEEE";
          #  $content[1]["start"]=$eee;
           # print_r($content[1]);
            $message = array(""); 
            $r = 0;
             foreach ($content as $key => $value) {
                 # echo "$value[message] <br>";        
                 $str = $value[message] ;
                 $str1= str_replace ("/","斜",$str);
                 $str1= str_replace ("／","斜",$str1);
                 $str1= str_replace (".","斜",$str1);
                 $str1= str_replace ("～","到",$str1);
                 $str1= str_replace ("：",":",$str1);
                 $str1= str_replace ("－","到",$str1);
                 $str1= str_replace ("-","到",$str1);
                 $str1= str_replace ("~","到",$str1);
    
                 $seg_list = Posseg::cut($str1);
                 #var_dump($seg_list);
                 $s=0;
                 $flag = FALSE;
                 $flag_lotterydate = FALSE;
                 $lottery_date='';
                 $startdate= '';
                 $enddate='';
                for ($i=0; $i<count($seg_list);$i++){
                    if ($seg_list[$i][word] == "活動時間" || $seg_list[$i][word] == "活動期間" || $seg_list[$i][word] == "活動日期" ){
                            $s = $i;
                            $flag = TRUE;
                        
                            if ($flag && empty($enddate)){
                                 $flag = FALSE;
                                 $i+=20;
                                 for ($j=$s; $j<$s+20;$j++){
                                    ######################################START_DATE#####################################################
                                    #start-年/月/日
                                    if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "斜" && $seg_list[$j+4][tag] == m && ($seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至" || $seg_list[$j+8][word] == "到" || $seg_list[$j+8][word] == "至")){
                                           #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word]."-".$seg_list[$j+4][word];
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=5;

                                       }
                                    #start-X年X月X日
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "年" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "月" && $seg_list[$j+4][tag] == m && ($seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至" || $seg_list[$j+8][word] == "到" || $seg_list[$j+8][word] == "至" || $seg_list[$j+9][word] == "到" || $seg_list[$j+9][word] == "至")){
                                           #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word]."-".$seg_list[$j+4][word];
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=5;

                                       }
                                    #start-即日起
                                    else if ($seg_list[$j][word] == "即日起" && ($seg_list[$j+1][word] == "到" || $seg_list[$j+1][word] == "至")){
                                        #$start .= "開始時間:".date("Y-m-d",$value[date]);
                                        $d = strtotime($value[created_time]);
                                        $startdate = "開始時間:". date("Y-m-d",$d); 
                                        $j+=1;
                                    }
                                    #start-月/日 到
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && ($seg_list[$j+3][word] == "到" || $seg_list[$j+3][word] == "至" || $seg_list[$j+4][word] == "到" || $seg_list[$j+4][word] == "至" || $seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至")){ 
                                        #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word];
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=3;
                                    } 
                                    #start-X月X日 到
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "月" && $seg_list[$j+2][tag] == m && ($seg_list[$j+4][word] == "到" || $seg_list[$j+4][word] == "至" || $seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至")){ 
                                        #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word];
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=3;
                                    } 

                                    ######################################END_DATE#####################################################
                                    #end-到 年/月/日
                                    if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "斜" && $seg_list[$j+5][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-". $seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-到 X年X月X日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "年" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "月" && $seg_list[$j+5][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-". $seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-年/月/日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "斜" && $seg_list[$j+5][tag] == m &&  ($seg_list[$j+6][word] == "止" || $seg_list[$j+6][word] == "截止")){
                                        #$end .= "結束時間".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-".$seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if ($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-X年X月X日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "年" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "月" && $seg_list[$j+5][tag] == m &&  ($seg_list[$j+7][word] == "止" || $seg_list[$j+7][word] == "截止")){
                                        #$end .= "結束時間".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-".$seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }

                                    #end-月/日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && ($seg_list[$j+4][word] == "止" || $seg_list[$j+4][word] == "截止" || $seg_list[$j+4][word] == "前" || $seg_list[$j+4][word] == "活動截止")){
                                       # $end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                         break;
                                    }
                                     #end-X月X日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "月" && $seg_list[$j+3][tag] == m && ($seg_list[$j+5][word] == "止" || $seg_list[$j+5][word] == "截止" || $seg_list[$j+5][word] == "前" || $seg_list[$j+5][word] == "活動截止")){
                                       # $end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                         break;
                                    }

                                    #end-到 月/日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                        break;
                                    }
                                     #end-到 X月X日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "月" && $seg_list[$j+3][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                        break;

                                    }
                                    }
                    }
                    }
                }
                if (empty($enddate)){
                    for ($i=0; $i<count($seg_list);$i++){
                     if ($seg_list[$i][word] == "活動" && ( $seg_list[$i+1][word] == "到" || $seg_list[$i+1][word] == "至" ||($seg_list[$i+1][tag] == m && $seg_list[$i+2][word] ==  "斜"))){
                            $s = $i;
                            $flag = TRUE;
                            if ($flag && empty($enddate)){
                                 $flag = FALSE;
                                 $i+=20;
                                 for ($j=$s; $j<$s+20;$j++){
                                    ######################################START_DATE#####################################################
                                    #start-年/月/日
                                    if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "斜" && $seg_list[$j+4][tag] == m && ($seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至" || $seg_list[$j+8][word] == "到" || $seg_list[$j+8][word] == "至")){
                                           #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word]."-".$seg_list[$j+4][word];
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=5;

                                       }
                                    #start-X年X月X日
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "年" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "月" && $seg_list[$j+4][tag] == m && ($seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至" || $seg_list[$j+8][word] == "到" || $seg_list[$j+8][word] == "至" || $seg_list[$j+9][word] == "到" || $seg_list[$j+9][word] == "至")){
                                           #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word]."-".$seg_list[$j+4][word];
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=5;

                                       }
                                    #start-即日起
                                    else if ($seg_list[$j][word] == "即日起" && ($seg_list[$j+1][word] == "到" || $seg_list[$j+1][word] == "至")){
                                        #$start .= "開始時間:".date("Y-m-d",$value[date]);
                                        $d = strtotime($value[created_time]);
                                        $startdate = "開始時間:". date("Y-m-d",$d); 
                                        $j+=1;
                                    }
                                    #start-月/日 到
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && ($seg_list[$j+3][word] == "到" || $seg_list[$j+3][word] == "至" || $seg_list[$j+4][word] == "到" || $seg_list[$j+4][word] == "至" || $seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至")){ 
                                        #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word];
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=3;
                                    } 
                                    #start-X月X日 到
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "月" && $seg_list[$j+2][tag] == m && ($seg_list[$j+4][word] == "到" || $seg_list[$j+4][word] == "至" || $seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至")){ 
                                        #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word];
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=3;
                                    } 

                                    ######################################END_DATE#####################################################
                                    #end-到 年/月/日
                                    if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "斜" && $seg_list[$j+5][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-". $seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-到 X年X月X日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "年" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "月" && $seg_list[$j+5][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-". $seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-年/月/日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "斜" && $seg_list[$j+5][tag] == m &&  ($seg_list[$j+6][word] == "止" || $seg_list[$j+6][word] == "截止")){
                                        #$end .= "結束時間".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-".$seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if ($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-X年X月X日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "年" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "月" && $seg_list[$j+5][tag] == m &&  ($seg_list[$j+7][word] == "止" || $seg_list[$j+7][word] == "截止")){
                                        #$end .= "結束時間".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-".$seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }

                                    #end-月/日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && ($seg_list[$j+4][word] == "止" || $seg_list[$j+4][word] == "截止" || $seg_list[$j+4][word] == "前" || $seg_list[$j+4][word] == "活動截止")){
                                       # $end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                         break;
                                    }
                                     #end-X月X日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "月" && $seg_list[$j+3][tag] == m && ($seg_list[$j+5][word] == "止" || $seg_list[$j+5][word] == "截止" || $seg_list[$j+5][word] == "前" || $seg_list[$j+5][word] == "活動截止")){
                                       # $end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                         break;
                                    }

                                    #end-到 月/日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                        break;
                                    }
                                     #end-到 X月X日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "月" && $seg_list[$j+3][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                        break;

                                    }
                                    }
                    }
                        }
                    }
                    
                }
                if (empty($enddate)){
                    for ($i=0; $i<count($seg_list);$i++){
                        if ($seg_list[$i][word] == "活動" || $seg_list[$i][word] == "即日起"){
                            $s = $i;
                            $flag = TRUE;
                            if ($flag && empty($enddate)){
                                 $flag = FALSE;
                                 $i+=20;
                                 for ($j=$s; $j<$s+20;$j++){
                                    ######################################START_DATE#####################################################
                                    #start-年/月/日
                                    if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "斜" && $seg_list[$j+4][tag] == m && ($seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至" || $seg_list[$j+8][word] == "到" || $seg_list[$j+8][word] == "至")){
                                           #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word]."-".$seg_list[$j+4][word];
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=5;

                                       }
                                    #start-X年X月X日
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "年" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "月" && $seg_list[$j+4][tag] == m && ($seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至" || $seg_list[$j+8][word] == "到" || $seg_list[$j+8][word] == "至" || $seg_list[$j+9][word] == "到" || $seg_list[$j+9][word] == "至")){
                                           #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word]."-".$seg_list[$j+4][word];
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=5;

                                       }
                                    #start-即日起
                                    else if ($seg_list[$j][word] == "即日起" && ($seg_list[$j+1][word] == "到" || $seg_list[$j+1][word] == "至")){
                                        #$start .= "開始時間:".date("Y-m-d",$value[date]);
                                        $d = strtotime($value[created_time]);
                                        $startdate = "開始時間:". date("Y-m-d",$d); 
                                        $j+=1;
                                    }
                                    #start-月/日 到
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && ($seg_list[$j+3][word] == "到" || $seg_list[$j+3][word] == "至" || $seg_list[$j+4][word] == "到" || $seg_list[$j+4][word] == "至" || $seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至")){ 
                                        #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word];
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=3;
                                    } 
                                    #start-X月X日 到
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "月" && $seg_list[$j+2][tag] == m && ($seg_list[$j+4][word] == "到" || $seg_list[$j+4][word] == "至" || $seg_list[$j+5][word] == "到" || $seg_list[$j+5][word] == "至" || $seg_list[$j+6][word] == "到" || $seg_list[$j+6][word] == "至" || $seg_list[$j+7][word] == "到" || $seg_list[$j+7][word] == "至")){ 
                                        #$start .= "開始時間:".$seg_list[$j][word]."-".$seg_list[$j+2][word];
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $startdate = "開始時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=3;
                                    } 

                                    ######################################END_DATE#####################################################
                                    #end-到 年/月/日
                                    if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "斜" && $seg_list[$j+5][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-". $seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-到 X年X月X日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "年" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "月" && $seg_list[$j+5][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-". $seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-年/月/日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "斜" && $seg_list[$j+5][tag] == m &&  ($seg_list[$j+6][word] == "止" || $seg_list[$j+6][word] == "截止")){
                                        #$end .= "結束時間".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-".$seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if ($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }
                                    #end-X年X月X日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "年" && $seg_list[$j+3][tag] == m && $seg_list[$j+4][word] == "月" && $seg_list[$j+5][tag] == m &&  ($seg_list[$j+7][word] == "止" || $seg_list[$j+7][word] == "截止")){
                                        #$end .= "結束時間".$seg_list[$j+1][word]."-".$seg_list[$j+3][word]."-".$seg_list[$j+5][word];
                                        if($seg_list[$j+1][word] == date("Y") || $seg_list[$j+1][word] == date("Y")+1){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]));
                                        }
                                        else if($seg_list[$j+1][word] == date("Y")-1911 || $seg_list[$j+1][word] == date("Y")-1910){
                                             $enddate =  "結束時間:".date("Y-m-d",mktime(0,0,0,$seg_list[$j+3][word], $seg_list[$j+5][word],$seg_list[$j+1][word]+1911));
                                        }
                                        break;
                                    }

                                    #end-月/日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m && ($seg_list[$j+4][word] == "止" || $seg_list[$j+4][word] == "截止" || $seg_list[$j+4][word] == "前" || $seg_list[$j+4][word] == "活動截止")){
                                       # $end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                         break;
                                    }
                                     #end-X月X日止
                                    else if ($seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "月" && $seg_list[$j+3][tag] == m && ($seg_list[$j+5][word] == "止" || $seg_list[$j+5][word] == "截止" || $seg_list[$j+5][word] == "前" || $seg_list[$j+5][word] == "活動截止")){
                                       # $end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                         break;
                                    }

                                    #end-到 月/日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "斜" && $seg_list[$j+3][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                        break;
                                    }
                                     #end-到 X月X日
                                    else if (($seg_list[$j][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+1][tag] == m && $seg_list[$j+2][word] == "月" && $seg_list[$j+3][tag] == m){
                                        #$end .= "結束時間:".$seg_list[$j+1][word]."-".$seg_list[$j+3][word];
                                        if($seg_list[$j+1][word] >= date("m")-2){
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y"))); 
                                        }
                                        else{
                                            $enddate = "結束時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+1][word], $seg_list[$j+3][word],date("Y")+1));
                                        }
                                        break;

                                    }
                                    }
                    }
                        }
                    }
                }
                
                for ($i=0; $i<count($seg_list);$i++){
                    if ($seg_list[$i][word] == "開獎日期" || $seg_list[$i][word] == "抽獎日期" || $seg_list[$i][word] == "中獎名單" || $seg_list[$i][word] == "得獎名單" || $seg_list[$i][word] == "開獎時間" || $seg_list[$i][word] == "開獎辦法"){
                        $s = $i;
                        $flag_lotterydate = TRUE;
                        if($flag_lotterydate && empty($lottery_date)){
                            $flag_lotterydate = FALSE;
                            $i+=4;
                             for ($j=$s; $j<$s+4;$j++){
                                  if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "斜" && $seg_list[$j+4][tag] == m){
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=4;

                                       }
                                    #start-X年X月X日
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "年" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "月" && $seg_list[$j+4][tag] == m){
                                        if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                        }
                                        else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                        }
                                           $j+=4;

                                       }
                                    #start-月/日
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m){ 
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=2;
                                    } 
                                    #start-X月X日
                                    else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "月" && $seg_list[$j+2][tag] == m){ 
                                        if($seg_list[$j][word] >= date("m")-2){
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                        }
                                        else{
                                            $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                        }
                                         $j+=2;
                                    } 
                             }
                        }
                    }
                }
                if (empty($lottery_date)){ 
                     for ($i=0; $i<count($seg_list);$i++){
                        if ($seg_list[$i][word] == "公布" || $seg_list[$i][word] == "公佈" || $seg_list[$i][word] == "抽出" || $seg_list[$i][word] == "公告" || $seg_list[$i][word] == "開獎"){
                            $s = $i-10;
                            $flag_lotterydate = TRUE;
                            if($flag_lotterydate && empty($lottery_date) && $s > 0){
                                $flag_lotterydate = FALSE;
                                 for ($j=$s; $j<$s+10;$j++){
                                      if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "斜" && $seg_list[$j+4][tag] == m){
                                            if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                            }
                                            else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                            }
                                               $j+=4;

                                           }
                                        #start-X年X月X日
                                        else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "年" && $seg_list[$j+2][tag] == m && $seg_list[$j+3][word] == "月" && $seg_list[$j+4][tag] == m){
                                            if($seg_list[$j][word] == date("Y") || $seg_list[$j][word] == date("Y")+1){
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word])); 
                                            }
                                            else if ($seg_list[$j][word] == date("Y")-1911 || $seg_list[$j][word] == date("Y")-1910){
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j+2][word], $seg_list[$j+4][word],$seg_list[$j][word]+1911)); 
                                            }
                                               $j+=4;

                                           }
                                        #start-月/日
                                        else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "斜" && $seg_list[$j+2][tag] == m){ 
                                            if($seg_list[$j][word] >= date("m")-2){
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                            }
                                            else{
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                            }
                                             $j+=2;
                                        } 
                                        #start-X月X日
                                        else if ($seg_list[$j][tag] == m && $seg_list[$j+1][word] == "月" && $seg_list[$j+2][tag] == m){ 
                                            if($seg_list[$j][word] >= date("m")-2){
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y"))); 
                                            }
                                            else{
                                                $lottery_date = "開獎時間:". date("Y-m-d",mktime(0,0,0,$seg_list[$j][word], $seg_list[$j+2][word],date("Y")+1));
                                            }
                                             $j+=2;
                                        } 
                                 }
                            }
                        }
                    }
                }

                 ######################################沒有時間#####################################################
                     if(empty($startdate) && !empty($enddate)){
                            $d = strtotime($value[created_time]);
                            $startdate = "開始時間:". date("Y-m-d",$d); 
                        }
                     else if(empty($enddate)){
                            $startdate = NULL;
                            $enddate = NULL;
                        }
                    if (empty($lottery_date)){
                        $lottery_date = NULL;
                    }
                 
                 $content[$r]["start_activetime"] = $startdate ;
                 $content[$r]["end_activetime"] = $enddate ;
                 $content[$r]["lottery_time"] = $lottery_date ;
                 $r++;
             } 
    print json_encode($content);
    
    $fp = fopen('lotterydata_recent.json', 'w');
			fwrite($fp, json_encode($content));
			fclose($fp);
   # print_r($content);
   # && ($seg_list[$j+5][word] == "到" || $seg_list[$j][word] == "至") && $seg_list[$j+6][tag] == m && $seg_list[$j+7][word] == "斜" && $seg_list[$j+8][tag] == m $seg_list[$j+9][word] == "斜" && $seg_list[$j+10][tag] == m 
#       else if ($seg_list[$i][tag] == m && $seg_list[$i+1][tag] == m && $i+1<count($seg_list)){
#           echo $seg_list[$i][word];
#           echo "/";
#          echo $seg_list[$i+1][word];
#           echo "<br>";
#           $i=$i+1;
#       }
   
    


?>
