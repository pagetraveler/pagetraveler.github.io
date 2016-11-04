function selectFunction(){

	var namejson;
	var colorArray;
	var x = document.getElementById("select_info").value;
	
	if(x=="7day"){
		colorArray = ["#FF3838", "#FF9933", "#FFFF57", "#91D04E"];
		$("#picture_tip").append("<img id='image centered' src='../images/lottery_color.png' width='35%' style='float:right;'>");
	}
	else if(x=="recent"){
		colorArray = ["#3577e3", "#3577e3", "#3577e3","#3577e3"];
		$("#picture_tip").empty();
	}
	
	
	
	namejson = "../assets/json/lottery/lotterydata_"+ x +".json";
	
	var svg = d3.select('.div1');
        var format = d3.format(",d");
        var ss = d3.tip();
        var format = d3.format(",d");

        // --- 轉換巢狀結構的key ---   
        function reSortRoot(root, value_key) {
            for (var key in root) {
                if (key == "key") {
                    root.name = root.key;
                    delete root.key;
                }
                if (key == "values") {
                    root.children = [];
                    for (item in root.values) {
                        root.children.push(reSortRoot(root.values[item], value_key));
                    }
                    delete root.values;
                }
                if (key == value_key) {
                    root.value = parseFloat(root[value_key]);
                    delete root[value_key];
                }
            }
            return root;
        }
        // --- 轉換巢狀結構的key ---  
        function rendering() {
            var diameter;
            var test = parseInt(d3.select(".content").style("width"), 10);
            if (test > 900) {
                diameter = 900;
            } else if (test < 300){
                diameter = 300;
            }
            else{
                 diameter = parseInt(d3.select(".content").style("width"), 10);
            }
            d3.json(namejson, function(root) {

                var nodesBydate = d3.nest()
                    .key(function(d, i) {
                        return d.created_time;
                    })
                    .entries(root);

                var d = {};

                //將資料命名
                d.key = "flare";
                d.values = nodesBydate;

                //修改資料的key,children名稱

                root = reSortRoot(d, "children");

                var pack = d3.layout.pack()
                    .size([diameter, diameter-100])
                    .value(function(d) {
                        return 20;
                    }).padding(5);

                var hexbin = d3.hexbin()
                    .size([diameter, diameter])
                    .radius(function(d) {
                        return d.r * 2 / Math.sqrt(3);
                    });

                ss.html('');
                ss.direction(function(d) {
                        return 'e'
                    })
                    .attr('class', 'd3-tip')
                    .offset([-10, 0])
                    .html(function(d) {
                        if (d.start_activetime == null || d.end_activetime == null) {
                            return "<span style='color:white'>活動詳見內文<br>分享數:" + d.size +"</span>";
                        } else if ((d.start_activetime != null || d.end_activetime != null) && d.lottery_time == null) {
                            var enddate = d.end_activetime.split(":");
                            //console.log(enddate);
                            if ((Date.parse(enddate[1])).valueOf() < (Date.parse(new Date().toDateString())).valueOf()) {
                                //console.log("過期");
                                return "<span style='color:red'>" + "****活動已過期****" + "<br>" + d.start_activetime + "<br>" + d.end_activetime + "<br>分享數:" + d.size +"</span>";
                            } else {
                                return "<span style='color:white'>" + d.start_activetime + "<br>" + d.end_activetime + "<br>分享數:" + d.size +"</span>";
                            }
                        } else {
                            var enddate = d.end_activetime.split(":");
                            if ((Date.parse(enddate[1])).valueOf() < (Date.parse(new Date().toDateString())).valueOf()) {
                                // console.log("過期");
                                return "<span style='color:red'>" + "****活動已過期****" + "<br>" + d.start_activetime + "<br>" + d.end_activetime + "<br>" + d.lottery_time + "<br>分享數:" + d.size +"</span>";
                            } else {
                                return "<span style='color:white'>" + d.start_activetime + "<br>" + d.end_activetime + "<br>" + d.lottery_time + "<br>分享數:" + d.size +"</span>";
                            }
                        }
                    });
                svg.html('');
                svg.attr("width", diameter)
                    .attr("height", diameter)
                    .append("g")
                    .attr("transform", "translate(2,2)")
                    .call(ss);

                var node = svg.datum(root).selectAll(".node")
                    .data(pack.nodes)
                    .enter().append("g")
                    .attr("class", function(d) {
                        return d.children ? "node" : "leaf node";
                    })
                    .attr("transform", function(d) {
                        return "translate(" + d.x + "," + d.y + ")";
                    });
                var days = root.children.length - 1;
              
                node.filter(function(d) {
                        return d.children && (d.name != "flare");
                    })
                    .append("path")
                    .attr("d", function(d) {
                        return hexbin.hexagon(d.r * 2 / Math.sqrt(3));
                    })
                    .style("fill", "steelblue")
                    .style("stroke", "white")
                    .style("opacity", 0.5);

                node.filter(function(d) {
                        return !d.children;
                    })
                    .append("path")
                    .attr("d", function(d) {
                        return hexbin.hexagon(d.r * 2 / Math.sqrt(3));
                    })
                    .style("fill", function(d) {
                        if (d.size < 200) {
                            return colorArray[3];
                        } else if (200 < d.size && d.size <= 500) {
                            return colorArray[2];
                        } else if (500 < d.size && d.size <= 800) {
                            return colorArray[1];
                        } else if (800 < d.size) {
                            return colorArray[0];
                        }
                    })
                    .on('mouseover', ss.show)
                    .on('mouseout', ss.hide)
                    .on('click', function(d) {
                        window.open("https://www.facebook.com/" + d.id);
                    });

                node.filter(function(d) {
                        return d.children && (d.name != "flare");
                    }).append("text")
                    .attr("transform", function(d) {
                        return "translate(" + 0 + "," + -d.r * 0.9 + ")";
                    })
                    .attr("y", ".3em")
                    .style("font-size", "12px")
                    .style("fill", "gray")
                    .style("text-anchor", "middle")
                    .text(function(d) {
                        return d.name.substring(0, d.r / 3);
                    });

            });
        }

        d3.select(window).on('resize', rendering);
        rendering();
}
