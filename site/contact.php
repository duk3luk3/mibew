<?php 
$page = 'home';
$subpage = 'contacts';
require_once('libs/common.php');
start_html_output();
$title = getlocal("contacts.title");
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="page">
	<div id="content">
		<div class="box1">
			<p><img src="images/webimlogo.gif" alt="" width="74" height="79" class="left" /><?php echo getlocal("head.intro") ?></p>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("contacts.title") ?></h2>
			<div class="entry">
			<p>
			<?php echo getlocal("contacts.person") ?> Evgeny Gryaznov<br/>
			<?php echo getlocal("contacts.email") ?> evgeny [at] mibew (dot) org
			</p>
			<p>
			<?php echo getlocal("contacts.person") ?> Ed Kraus<br/>
			<?php echo getlocal("contacts.email") ?> support [at] mibew (dot) org
			</p>
			</div>
			<div class="nometa"></div>
		</div>
	</div>
	<!-- end content -->
	<!-- start sidebar -->
	<div id="sidebar">
		<ul>
<?php
require_once('inc/main.i');
require_once('inc/locales.i');
?>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>


<?php require_once('inc/footer.i'); ?>
