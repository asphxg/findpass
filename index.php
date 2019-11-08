<?php
// 引用sphinxapi类
require "sphinxapi.php";
//关闭错误提示
error_reporting(E_ALL & ~E_NOTICE);
$num = 0;
if (!empty($_GET) && !empty($_GET['q'])) {
    $Keywords = strip_tags(trim($_GET['q']));
    if (!empty($_GET['m']) && 1 == $_GET['m']) {
        $Keywords = substr(md5($Keywords), 8, 16);
    }
    if (!empty($_GET['m']) && 2 == $_GET['m']) {
        $Keywords = md5($Keywords);
    }
    $cl = new SphinxClient();
    // 返回结果设置
    $cl->SetServer('127.0.0.1', 9321);
    $cl->SetConnectTimeout(3);
    $cl->SetArrayResult(true);
    // 设置是否全文匹配
    if (!empty($_GET) && !empty($_GET['f'])) {
        $cl->SetMatchMode(SPH_MATCH_ALL);
    } else {
        $cl->SetMatchMode(SPH_MATCH_ANY);
    }
    if (!empty($_GET) && !empty($_GET['p'])) {
        $p = !intval(trim($_GET['p'])) == 0 ? intval(trim($_GET['p'])) - 1 : 0;
        $p = $p * 20;
        // 我在sed.conf 设置了最大返回结果数1000。但是我在生成页码的时候最多生成20页，我想能满足大部分搜索需求了。
        // 以下语句表示从P参数偏移开始每次返回20条。
        $cl->setLimits($p, 20);
    } else {
        $cl->setLimits(0, 20);
    }
    $res = $cl->Query(".$Keywords.", "*");
    @mysql_connect("127.0.0.1:3306", "root", "root123456"); //数据库账号密码
    mysql_select_db("sgkdata"); //数据库库名名
	
    if (is_array($res["matches"])) {
        foreach ($res["matches"] as $docinfo) {
            $ids = $ids . $docinfo[id] . ',';
        }
        $ids = rtrim($ids, ',');
        $sql = "select * from data where id in($ids)"; //注意修改表名
        mysql_query("set names utf8");
        $ret = mysql_query($sql);
        $num = mysql_num_rows($ret);
    }
	
}
?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>FindPass</title>
<meta name="keywords" content="FindPass">
<meta name="description" content="FindPass">
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<link href="/css/favicon.ico" rel="shortcut icon">
<link href="/css/bootstrap.css" rel="stylesheet">
<link href="/css/isdc-theme.css" rel="stylesheet">
</head>
<body class="home">
<div class="navbar navbar-inverse navbar-fixed-top headroom animated headroom--top">
    <div class="container">
        <div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
         <a class="navbar-brand" href="#"><img src="/css/logo.png"></a>
		 </div>
        <div class="navbar-collapse collapse">
             <ul class="nav navbar-nav pull-right">
                <li class="active"><a href="/">首页</a></li>
				<li><a href="http://www.96sec.org">指尖的温度's blog</a></li>
            </ul>
      </div>    
	  </div>
</div>
<?php
@mysql_connect("127.0.0.1:3306", "root", "root123456"); //数据库账号密码
mysql_select_db("sgkdata"); //数据库库名名
$count=mysql_fetch_assoc(mysql_query('select max(id) from data'));  //算最大值，等同于统计共有多少数据
?>
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <div class="carousel-inner" role="listbox"> <br>
      <br>
      <div class="jumbotron">
          <div style="margin:0 auto;width: 1050px;">
			<h5>本站现有数据量为 <font color="red"><?php echo $count['max(id)']; ?></font> 条~</h5>
            <div id="jshint-pitch" class="alert  scan-wait" style="margin-top:10px;font-size:14px;color: #8e8e8e;font-weight: bold;">输入你要搜索的内容：</div>
            <div id="scan-result-box" style="font-size:12px;">
			<form action="" method="get" class="form-horizontal" role="form">
                <div class="input-group">
                    <input placeholder="username、password、mail、qq、tel" name="q" class="form-control" value="<?php echo strip_tags(trim($_GET['q']));?>">
				  <span class="input-group-btn scan-but-span">
                  <button type="submit" class="btn btn-success" onclick="check(form)">搜一下!</button>
                  </span>
                </div>
			</form>
              <br>
			</div>
<?php
if (0 == !$num) {
    echo "<div id=\"selecting\" class=\"progress progress-striped active progress-info\"><span><b>找到与&nbsp&nbsp<font color=#382526>{$Keywords}</font>&nbsp&nbsp相关的结果 {$res[total_found]} 个。用时 {$res[time]} 秒。</b></span></div><br>
    ";
    echo "<table style=\"font-size:16px;width: 1050px;line-height: 30px;\">
          <thead>
          <tr>
          <th width=\"20%\">用户名/账号</th>
          <th width=\"35%\">密码/密文</th>
          <th width=\"15%\">Salt/Tel/QQ</th>
		  <th width=\"20%\">邮箱</th>
          <th width=\"10%\">来源</th>
		  ";
    while ($row = mysql_fetch_assoc($ret)) {
        echo "<tr><td>" . $row['username'] . "</td>";
        echo "<td>" . $row['password'] . "</td>";
        echo "<td>" . $row['salt'] . "</td>";
		echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['order'] . "</td></tr>";
    }
    echo "</tr>
          </thead>
          <tbody id=\"value_tables\">
          </tbody>
		  </table>";
} else {
    if (!empty($_GET) && !empty($_GET['q'])) {
        echo "<div id=\"selecting\" class=\"progress progress-striped active progress-info\"><span><b>找不到与<b>&nbsp&nbsp<font color=#382526>{$Keywords}</font>&nbsp&nbsp</b>相关的结果。请更换其他关键词试试。</b></span></div><br>";
    }
}
?>

<?php
if (0 == !$num) {
    $pagecount = (int) ($res[total_found] / 20);
    if (!($res[total_found] % 20) == 0) {
        $pagecount = $pagecount + 1;
    }
    if ($pagecount > 20) {
        $pagecount = 20;
    }
    $highlightid = !intval(trim($_GET['p'])) == 0 ? intval(trim($_GET['p'])) : 1;
	echo "<ul class=\"nav navbar-nav\">";
    for ($i = 1; $i <= $pagecount; $i++) {
        if ($highlightid == $i) {
            echo "";
        } else {
            echo "<li><a href=\"index.php?q={$Keywords}&p={$i}\">{$i}</a></li>";
        }
    }
	echo "</ul>";
	echo "<br><br>";
}
?>
         <font size="3" div="" align="center" class="STYLE2">申明：<br>
         数据来自互联网，旨在找回遗忘密码，或对已泄露密码进行修改防范，请勿非法使用，否则一切后果自负。
		 </font>   
          </div>
      </div>
    <div class="item"> </div>
  </div>
</div>
<footer id="footer">
    <div class="footer2">
        <div class="container">
            <div class="row">
                    <div class="widget-body">
                        <p class="text-center">Copyright © 2019 FindPass</p>
                    </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>