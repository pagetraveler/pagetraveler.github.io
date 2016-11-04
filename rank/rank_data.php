<?php 
$facebook_id=$_GET['id'];
$array = explode(',', $facebook_id); 
//print_r($array);
$app_id = '553315818188739';
$app_secret = 'c92ce6c9aa1cacb8b2b9a432f36acba4';

@$token_url = "https://graph.facebook.com/oauth/access_token?" .
"client_id=" . $app_id .
"&client_secret=" . $app_secret .
"&grant_type=client_credentials";

@$response = file_get_contents($token_url);
     $params = null;
    parse_str($response, $params);

for($j= 0 ; $j<count($array);$j++){
     //access_token 通行證 用來存取資料
     //可以存取的資料說明:https://developers.facebook.com/docs/graph-api/reference
 
    @$postlikes_url = "https://graph.facebook.com/v2.7/".$array[$j]."?fields=posts.limit(10)%7Blikes.summary(true)%2Cmessage%7D&access_token="
       . $params['access_token'];
 
     @$likes_data = json_decode(file_get_contents($postlikes_url),true);
    
     @$content_likes = $likes_data['posts']['data'];
$summary = array();
for($i=0 ; $i< count($content_likes) ; $i++){
   @$summary[$i] =$content_likes[$i]['likes']['summary']['total_count'];
   if($summary[$i]==max($summary)){
      @$max_postid=$content_likes[$i]['id'];
        @$output[$j]['id']=$max_postid;
         @$output[$j]['message']=$content_likes[$i]['message'];
    }
}
}
     //$pages_data["children"] = $content_likes; 
@$last_data = json_encode($output);
@print_r($last_data);
   // $user_data = json_decode(file_get_contents($graph_url),true);

?>