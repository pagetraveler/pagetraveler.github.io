function rank_chioce(x){
    $(".last").empty();
    $(".last.table_title").text("熱門文章");
	$(".spinner").remove();
	var opts;
        var target;
        var spinner;
        $(function () {
            opts = {
                lines: 13 // The number of lines to draw
                    ,
                length: 28 // The length of each line
                    ,
                width: 14 // The line thickness
                    ,
                radius: 42 // The radius of the inner circle
                    ,
                scale: 1 // Scales overall size of the spinner
                    ,
                corners: 1 // Corner roundness (0..1)
                    ,
                color: '#000' // #rgb or #rrggbb or array of colors
                    ,
                opacity: 0.25 // Opacity of the lines
                    ,
                rotate: 0 // The rotation offset
                    ,
                direction: 1 // 1: clockwise, -1: counterclockwise
                    ,
                speed: 1 // Rounds per second
                    ,
                trail: 60 // Afterglow percentage
                    ,
                fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
                    ,
                zIndex: 2e9 // The z-index (defaults to 2000000000)
                    ,
                className: 'spinner' // The CSS class to assign to the spinner
                    ,
                top: '50%' // Top position relative to parent
                    ,
                left: '60%' // Left position relative to parent
                    ,
                shadow: false // Whether to render a shadow
                    ,
                hwaccel: false // Whether to use hardware acceleration
                    ,
                position: 'absolute' // Element positioning
            }
            target = document.body;
            spinner = new Spinner(opts).spin(target);

        })
		
		var ranklast = [];
        var rankthis = [];
        var rank = [];
        var namejsonlast;
        var namejsonthis;
        var id_list = new Array();
        var post_id_url;
		x = document.getElementById("select_rank").value;
        console.log("黨"+x);
        $("#select_rank option[value=" + x + "]").attr("selected", "true");
        namejsonlast = "../assets/json/rank/rank_" + x + "_last.json";
        namejsonthis = "../assets/json/rank/rank_" + x + "_this.json";
        console.log(namejsonlast);

        function loadJSON(path, success, error) {
            var xhr = new XMLHttpRequest();
            //取得XMLHttpRequest物件，設定非同步傳輸完成函式"onreadystatechange"
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        if (success)
                            success(JSON.parse(xhr.responseText));
                        //讀檔成功時就將檔案內容當成字串，進行JSON解析
                    } else {
                        if (error)
                            error(xhr);
                        //讀檔失敗時就回傳錯誤訊息
                    }
                }
            };
            xhr.open("GET", path, true);
            // 初始設定
            xhr.send();
            // 傳輸

        };


        loadJSON(namejsonlast,
            function (data) {
                //    var t = document.getElementById("table1");

                // var range=[];
                for (var a = 0; a < data.children.length; a++) {
                    var percent = data.children[a].number / data.children[a].size;
                    ranklast.push({
                        "name": data.children[a].name,
                        "id": data.children[a].id,
                        "per": percent
                    });
                }
                ranklast = ranklast.sort(function (a, b) {
                    return a.per < b.per ? 1 : -1;
                });
            },

            function (xhr) {
                console.error(xhr);
            }
        );

        loadJSON(namejsonthis,
            function (data) {

                for (var a = 0; a < data.children.length; a++) {
                    var percent = data.children[a].number / data.children[a].size;
                    rankthis.push({
                        "name": data.children[a].name,
                        "id": data.children[a].id,
                        "per": percent
                    });
                }

                rankthis = rankthis.sort(function (a, b) {
                    return a.per < b.per ? 1 : -1;
                });

            },
            function (xhr) {
                console.error(xhr);
            }

        );



        function compareJSON(path, path, success, error) {
            var xhr = new XMLHttpRequest();
            //取得XMLHttpRequest物件，設定非同步傳輸完成函式"onreadystatechange"
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        if (success)
                            success(JSON.parse(xhr.responseText));
                        //讀檔成功時就將檔案內容當成字串，進行JSON解析
                    } else {
                        if (error)
                            error(xhr);
                        //讀檔失敗時就回傳錯誤訊息
                    }
                }
            };
            xhr.open("GET", path, true);
            // 初始設定
            xhr.send();
            // 傳輸

        };

        compareJSON(namejsonlast, namejsonthis,
            function (data) {

                for (var a = 0; a < rankthis.length; a++) {
                    var count = 0;

                    for (var b = 0; b < ranklast.length; b++) {
                        if (rankthis[a].id == ranklast[b].id) {
                            var c = b - a;
                            rank.push({
                                "name": rankthis[a].name,
                                "id": rankthis[a].id,
                                "place": c
                            });
                            break;
                        } else {
                            count++;
                        }
                    }

                    if (count >= ranklast.length) {
                        rank.push({
                            "name": rankthis[a].name,
                            "id": rankthis[a].id,
                            "place": null
                        });
                    }
                }
                var t = document.getElementById("table1");
                var x = 0;
                var y = 0;
                var z = 0;
                var r = 0;
                var k = 0;
                var rk = 0;
                for (k = 0; k < 10; k++) {
                    id_list[k] = rank[k].id;
                }
                post_id_url = "rank_data.php" + "?id=" + id_list;
                loadJSON(post_id_url,
                    function (data) {
                        for (var i = 2; i < t.rows.length; i += 2) {
                            var sub_message;
                            if(data[rk]!= null){
                                
                              if (data[rk].message != null && data[rk].message.length >= 50) {
                                sub_message = data[rk].message.substr(0, 50);
                                t.rows[i].cells[4].innerHTML = sub_message + "..."+ '<a href="https://www.facebook.com/'+data[rk].id+'" style="color:#3577e3;" target="_blank">more</a>' ;
                                  
                            }else{
                                t.rows[i].cells[4].innerHTML = data[rk].message+ '<a href="https://www.facebook.com/'+data[rk].id+'" style="color:#3577e3;" target="_blank">more</a>';  
                                }
                            }
                            rk++;

                        }
                        spinner.spin();
                    },
                    function (xhr) {
                        console.error(xhr);
                    }


                );
                for (var i = 2; i < t.rows.length; i += 2) {
                    if (rank[z].place > 0) {
                        t.rows[i].cells[1].innerHTML = "<img src='../images/rise.png'  class='updown' align='center' />";
                    } else if (rank[z].place < 0) {
                        t.rows[i].cells[1].innerHTML = "<img src='../images/down.png'  class='updown' align='center'/>";
                    } else if (rank[z].place == 0) {
                        t.rows[i].cells[1].innerHTML = "-";
                    } else {
                        t.rows[i].cells[1].innerHTML = "<img src='../images/new.png'  class='updown' align='center'/>";
                    }
                    z++;
                }

                for (var i = 3; i < t.rows.length; i += 2) {
                    if (rank[r].place != 0) {
                        t.rows[i].cells[0].innerText = rank[r].place;
                    } else {
                        t.rows[i].cells[0].innerText = null;
                    }
                    r++;
                }

                for (var i = 2; i < t.rows.length; i += 2) {
                    var img_url = "https://graph.facebook.com/" + rank[x].id + "/picture?type=large";
                    t.rows[i].cells[2].innerHTML = "<img class='profile' src=" + img_url + " />";
                    x++;
                }

                for (var i = 2; i < t.rows.length; i += 2) {
                    t.rows[i].cells[3].innerHTML = rank[y].name;
                    y++;
                }
            },
            function (xhr) {
                console.error(xhr);
            }
        );
}