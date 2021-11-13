<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?=$tpl['post']['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<style>
body, td {
	font-family: verdana, arial, sans-serif;
	color: #666;
	font-size: 80%;
}
h1, h2, h3, h4 {
	font-family: verdana, arial, sans-serif;
	color: #666;
	font-size: 100%;
	margin: 0px;
}
.link, .link a {
	color: #0000ff;
	text-decoration: underline;
}
</style>
</head>

<body bgcolor="#FFFFFF" text="#FFFFFF">
<table border="0" width="100%" cellspacing="1" cellpadding="3">
 <tr>
  <td>
   <h3><?=$tpl['post']['title']; ?></h3>
   <p><?=($tpl['post']['full-story'] ? $tpl['post']['full-story'] : $tpl['post']['short-story']); ?>
</table>
<br /><br />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
 <tr>
  <td width="1"><nobr>Огигинал новости &laquo;<?=$tpl['post']['title']; ?>&raquo;</nobr>
  <td>&nbsp;&nbsp;-
  <td class="link"><?=$tpl['post']['link']['post']; ?>
 <tr>
  <td>&laquo;<?=$config['home_title']; ?>&raquo;
  <td>&nbsp;&nbsp;-
  <td class="link"><?=$config['http_home_url']; ?>
</table>

</body>
</html>