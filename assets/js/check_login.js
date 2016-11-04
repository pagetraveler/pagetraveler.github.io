function checkLoginState() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}

window.fbAsyncInit = function () {
    FB.init({
        appId: '553315818188739',
        cookie: true, // enable cookies to allow the server to access 
        // the session
        xfbml: true, // parse social plugins on this page
        version: 'v2.7' // use graph api version 2.5
    });
    checkLoginState();
};

function statusChangeCallback(response) {
    if (response.status === 'connected') {
        // Logged into your app and Facebook
        if (document.getElementById("check_login")) {
            FB.api('/me', 'GET', {
                "fields": "id,name",
                "locale": "zh_TW"
            }, function (response) {
                $("#check_login").empty();
                $("#check_login").append("<span><img id='person_picture' src='http://graph.facebook.com/" + response.id + "/picture?type=small'></img>"+response.name+"</span>");
                $("#logout_li").append("<a href='#' id='logout' onclick='fb_logout()'><span>登出</span></a>");
                $("#check_login").attr("onclick", "");
            })
        }
    }
    else{
             $("#logout_li").empty();
        }
}

// Load the SDK asynchronously
(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.7";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function fb_login() {
    FB.login(function (response) {
        statusChangeCallback(response);
    }, {
        scope: 'user_likes,public_profile'
    });
}

function fb_logout() {
    FB.logout(function (response) {
        $("#check_login").empty();
        $("#logout_li").empty();
        $("#check_login").append("<span>登入</span>");
        $("#check_login").attr("onclick", "fb_login()")
    })
}

function about_me() {
    FB.getLoginStatus(function (response) {
        if (response.status === 'connected') {
            location.href = "http://pagetraveler.mgt.ncu.edu.tw/user_likes/user_likes.html";

        } else if (response.status === 'not_authorized') {
            // The person is logged into Facebook, but not your app.
            alert("請授權");
        } else {
            alert("請先登入");
        }
    })
}