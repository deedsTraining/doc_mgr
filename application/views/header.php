<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8"/>
	<title>DEEDS Document Manager</title>

	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/deeds_doc_mgr.css?20160923" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="<?php echo base_url();?>css/layout.css" type="text/css" media="screen" />
	<!--[if lt IE 9]>
	<link rel="stylesheet" href="<?php echo base_url();?>css/ie.css" type="text/css" media="screen" />
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->


	<script src="<?php echo base_url();?>js/jquery.min.js" type="text/javascript"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>js/jquery-textrange.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/charter_markup.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/institution_markup.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.highlighter.js"></script>

	<script src="<?php echo base_url();?>js/hideshow.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>js/jquery.tablesorter.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.equalHeight.js"></script>
	<script type="text/javascript">
	$(document).ready(function()
    	{
      	  $(".tablesorter").tablesorter();
   	 }
	);
	$(document).ready(function() {

	//When page loads...
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	//On Click Event
	$("ul.tabs li").click(function() {

		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});

});
    </script>
    <script type="text/javascript">
    $(function(){
        $('.column').equalHeight();
    });
</script>

</head>


<body>

	<header id="header">
		<hgroup>
			<h1 class="site_title"><img style="margin-top:5px; width:80%"src="<?php echo base_url()?>images/deeds-logo.png" /></h1>
			<h2 class="section_title">Document Manager</h2><div class="btn_view_site"><a href="http://deeds.library.utoronto.ca">DEEDS Site</a></div>
		</hgroup>
	</header> <!-- end of header bar -->



	<aside id="sidebar" class="column">
		<h3>Content</h3>
		<ul class="toggle">
			<li class="icn_categories"><a href="<?= site_url('cartulary')?>">Cartularies</a></li>
			<li class="icn_categories"><a href="<?= site_url('charter/list_records')?>">Charters</a></li>
			<li class="icn_categories"><a href="<?= site_url('institution')?>">Institutions</a></li>
                        <li class="icn_categories"><a href="<?= site_url('reindex')?>">Reindex</a></li>
		</ul>

		<h3>Logout</h3>
		<ul class="toggle">
			<li class="icn_jump_back"><a href="<?=base_url();?>index.php/charter/logout">Logout</a></li>
		</ul>

		<footer>
			<hr />
			<p><strong>Copyright &copy; DEEDS</strong></p>
			<p>Programmed by <a href="http://library.utoronto.ca">University of Toronto Libraries</a></p>
			<p>Theme by <a href="http://www.medialoot.com">MediaLoot</a></p>
		</footer>
	</aside><!-- end of sidebar -->

	<section id="main" class="column">
