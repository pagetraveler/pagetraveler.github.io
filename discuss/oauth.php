<?php 
$pages_id=$_GET['id'];

//print $pages_id;
 
$app_id = '553315818188739';
$app_secret = 'c92ce6c9aa1cacb8b2b9a432f36acba4';

@$token_url = "https://graph.facebook.com/oauth/access_token?" .
"client_id=" . $app_id .
"&client_secret=" . $app_secret .
"&grant_type=client_credentials";

$response = file_get_contents($token_url);
     @$params = null;
     parse_str($response, $params);
     //access_token 通行證 用來存取資料
     //可以存取的資料說明:https://developers.facebook.com/docs/graph-api/reference
 
    @ $likes_url = "https://graph.facebook.com/".$pages_id."?fields=likes.limit(999)%7Bcategory%2Cname%2Clink%2Cfan_count%2Ctalking_about_count%2Cabout%7D&locale=zh_TW&access_token="
       . $params['access_token'];

    @$pages_url = "https://graph.facebook.com/".$pages_id."?fields=about%2Cid%2Clink%2Cname%2Cfan_count%2Ctalking_about_count&access_token="
       . $params['access_token'];
 
    @ $likes_data = json_decode(file_get_contents($likes_url),true);
    @ $pages_data = json_decode(file_get_contents($pages_url),true);
    
     @$content_likes = $likes_data['likes']['data'];
     @$pages_data["children"] = $content_likes; 

    @ $last_data = json_encode($pages_data);
    @ print_r($last_data);
   // $user_data = json_decode(file_get_contents($graph_url),true);

?>