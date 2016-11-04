function myFunction(){

	var namejson;
	var colorArray;
	var x = document.getElementById("mySelect").value;
	
	if(x=="all"){
		colorArray = ["#FF3838", "#FF9933", "#FFFF57", "#91D04E", "#9EC3E6", "#CC66FF"];
		$(".taa").attr("src",'../images/all.png');
	}
	else if(x=="artist" || x=="publicfigure" || x=="schoolarship" || x=="politic" || x=="art" || x=="fiction"){
		colorArray = ["#1e5ba4","#205ecf","#3577e3","#66a0e5","#87bfed", "#ACD6FF"];//blue
		$(".taa").attr("src",'../images/blue.png');
	}
	else if(x =="music" || x=="sports" || x=="video" || x=="game" || x=="reading"){
		colorArray = ["#F00078", "#FF0080", "#FF359A", "#FF60AF", "#FF8FC7", "#FFB8DB"];
		$(".taa").attr("src",'../images/pink.png');//pink
	}
	else if(x =="food" || x=="informationtech" || x=="service" || x=="clothes" || x=="retail" || x=="dailyuse"){
		colorArray = ["#3c7239", "#4f8f4a", "#599d53", "#62aa5a", "#6ab761", "#A9D18F"];
		$(".taa").attr("src",'../images/green.png');//green
	}
	else if(x =="spot" || x=="store"){
		colorArray = ["#ea5414", "#f26a30", "#f27b48", "#f28c60", "#f29d79", "#f2ae91"];
		$(".taa").attr("src",'../images/orange.png');//orange
	}
	else if(x =="government" || x=="social" || x=="health" || x=="education" || x=="company"){
		colorArray = ["#5f1885", "#7f20b2", "#9a3dcc", "#b150e5", "#c46df2", "#d091f2"];
		$(".taa").attr("src",'../images/purple.png');//purple
	}
	
	
	namejson = "../assets/json/discussion/discussion_"+ x +".json";



            var svg = d3.select('.div1');
            var format = d3.format(",d");
            var tip = d3.tip();

          
            function rendering() {
                var diameter;
                var test = parseInt(d3.select(".content").style("width"), 10);
                if (test > 600) {
                    diameter = 600;
                } else if(test < 300){
                    diameter = 300;
                }
                else{
                    diameter = parseInt(d3.select(".content").style("width"), 10);
                }
                var bubble = d3.layout.pack()
                    .sort(null)
                    .size([diameter, diameter])
                    .padding(1.5);
			
                svg.html('');
                svg.attr("width", diameter)
                    .attr("height", diameter);               

                d3.json(namejson, function namejson(error, root) {
                    if (error) throw error;

                    var range = new Array();

                    function classes(root) {
                        var classes = [];

                        function recurse(name, node) {
                            if (node.children) node.children.forEach(function(child) {
                                recurse(node.name, child);
                            });
                            else classes.push({
                                packageName: name,
                                className: node.name,
                                value: node.size,
                                number: node.number,
                                facebookid: node.id
                            });
                        }

                        recurse(null, root);
                        return {
                            children: classes
                        };
                    }

                    var node = svg.selectAll(".node")
                        .data(bubble.nodes(classes(root))
                            .filter(function(d) {
                                return !d.children;
                            }))
                        .enter().append("g")
                        .attr("class", "node")
                        .attr("transform", function(d) {
                            return "translate(" + d.x + "," + d.y + ")";
                        })
                        .on("click", op);
                    tip.html('');
                    tip.attr('class', 'd3-tip')
                        .offset([-5, 100])
                        .html(function(d) {
                            return "粉專：" + d.className + "<span style='color:#444444'>" + " 粉專人數：" + format(d.value) + "</span>";
                        });

                    svg.call(tip);

                    function op(d, r) {
                        location.href = "related.html?id=" + d.facebookid;
                    }

                    node.append("circle")
                        .attr("r", function(d) {
                            return d.r;
                        })
                        .style("fill", function(d) {
                            if (1 < (d.number / d.value)) {
                                return colorArray[0];
                            } else if (0.8 < (d.number / d.value) && (d.number / d.value) <= 1) {
                                return colorArray[1];
                            } else if (0.6 < (d.number / d.value) && (d.number / d.value) <= 0.8) {
                                return colorArray[2];
                            } else if (0.4 < (d.number / d.value) && (d.number / d.value) <= 0.6) {
                                return colorArray[3];
                            } else if (0.2 < (d.number / d.value) && (d.number / d.value) <= 0.4) {
                                return colorArray[4];
                            } else if (0 <= (d.number / d.value) && (d.number / d.value) <= 0.2) {
                                return colorArray[5];
                            };
                        })
                        .on('mouseover', tip.show)
                        .on('mouseout', tip.hide);
                });

            }
            d3.select(window).on('resize', rendering);
            rendering();
}