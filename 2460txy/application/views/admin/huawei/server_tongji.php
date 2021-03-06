<?php
defined('BASEPATH') or exit('No direct script access allowed');

?>
<html style="height: 100%;">
	<head>
		<title></title>
		<meta charset="utf-8">
		<meta id="viewport" name="viewport" content="target-densitydpi=high-dpi,uc-fitscreen=yes,width=device-width, initial-scale=1.0" />
		<!-- <link href="//cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet"> -->
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/admin.css" rel="stylesheet">
		<script type="text/javascript" src="/js/jquery-3.1.1.js"></script>
		<script type="text/javascript">
			$(function(){
				$("#search").click(function(){
                    console.log("click");
                    var start = $("#start").val();
                    var end = $("#end").val();
                    var platform = "huawei";
                    var game_father_id = $("#game_father").val();
                    console.log("start " + start);
                    console.log("end " + end);
                    console.log("platform " + platform);
                    if(start == ""){
                        alert("起始日期不能为空");
                        return;
					// }else if (platform=="") {
					// 	alert("渠道名不能为空");
					// 	return;
					}

                    // var url = "/index.php/Admin_report_api/daily_income_old?start=" + start + "&to="  +end +"&game_father_id="+game_father_id +"&platform="+platform ;
					var url = "/index.php/Huawei_admin_backstage/get_server_info?start=" + start + "&end="  +end +"&game_father_id="+game_father_id +"&platform="+platform ;
					// location.href=url;
					$.get(
                        url,
                        {},
                        function (response,status,xhr) {
							if (response.c == '-1') {
                            	alert('错误：'+response.m);
								return;
                            }
							if (response.c == '1') {
                            	alert('错误：'+response.m);
								return;
                            }
							// console.log(response.d.info);
							var data = new Array();
							data = response.d;
							// console.log(response.c);
							// console.log(response.d.info.length);
							console.log(JSON.stringify(data));
                            if(response.c == 0){

								$(".info_table").remove();
								$.each(data,function(i,v){
									if(v['money']!=0){
										var fufeilv = (v['renshu']/v['denglu']).toFixed(2);
										var arpu =(v['money']/100/v['renshu']).toFixed(2);
										var arppu =(v['money']/100/v['cishu']).toFixed(2);
									}else{
										var fufeilv =0;
										var arpu =0;
										var arppu =0;
									}

									$("#first_row").after(
										"<tr class = 'info_table'>"
										+"<td>"+ v['date'] +"</td>"
										+"<td>"+ v['server_id'] +"</td>"
										+"<td>"+ v['denglu']+"</td>"
										+"<td>"+ v['zhuce']+"</td>"
										+"<td>"+ v['renshu']+"</td>"
										+"<td>"+ v['cishu']+"</td>"

										+"<td>"+fufeilv+"</td>"
										+"<td>"+ arpu+"</td>"
										+"<td>"+ arppu+"</td>"

										+"<td>"+ v['money']/100+"</td>"

										// +"<td>"+ v['2']+"("+(v['2']/v['create_role']).toFixed(2)+")"+"</td>"
										+"</tr>"
									);
								})
                            }
                        },
                        "json"
                    );

				});
			});
		</script>
	</head>
	<body>
	<div class="row">
	<!--右侧边导航栏 start-->
		<div class="col-md-1">
		  <?php $this->load->view('admin/huawei/huawei_sub_menu', ['server' => true]); ?>
		</div>
	<!--右侧边导航栏 end-->

		<div class="col-md-11 myborder">
			<div class="container-fluid">
				 <h4 class="alert-info" style="background-color:#ffffff">收入统计</h4>
			</div>
			<!--搜索条件 start-->
			<br/>
			<br/>
			<div class="alert alert-info " role="alert">
				<form action="">
					<span>起始日期：</span><input type="date" value="" id="start" name="start" style="width:150px;height:25px;">
					<span>截止日期：</span><input type="date" value="" id="end" name="end" style="width:150px;height:25px;">
					<span>游戏</span>
                    <select style="width:100px;height:25px;" id="game_father">
					  <option value=''>全部</option>
					  <?php foreach ($game_faters as $one) {
        ?>
					  <option value="<?php echo $one->game_father_id; ?>"><?php echo $one->game_father_name; ?></option>
					  <?php
    } ?>
					</select>

				</form>
			</div>
			<div class="row">
				<div class="col-md-6">
				<button type="button" id="search">搜索</button>
				</div>
			</div>
            <div id="all_income">
            </div>
			<!--搜索条件 end-->

			<!--数据显示 start-->
			<br/>
			<div class="alert alert-info " role="alert">
			<h4></h4>
				<table border="1" cellspacing="0" cellpadding="0" style="width:100%;height:auto;color:#000;background-color:#fff;">
					<tr id= "first_row">
						<th style="padding:5px;width:auto;">日期</th>
						<th style="padding:5px;">区服</th>
						<th style="padding:5px;">登陆数</th>
						<th style="padding:5px;">创角数</th>
						<th style="padding:5px;">充值人数</th>
						<th style="padding:5px;">充值次数</th>
						<th style="padding:5px;">付费率</th>
						<th style="padding:5px;">人均付费(总额/充值人数)</th>
						<th style="padding:5px;">活跃付费(总额/登陆人数)</th>
						<th style="padding:5px;">总额</th>
					</tr>
				</table>
				<div style="display:none;">
					<button type="button" id="pre" >上一页</button>
					<button type="button" id="next" >下一页</button>
					<input type="number" name="num" value="" min="1" style="width:80px;">
					<button type="button" id="turn_to" >跳转</button>
				</div>
			</div>

			<!--数据显示 end-->
		</div>
	</div>
	</body>
</html>
