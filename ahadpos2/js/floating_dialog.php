<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>JQuery Tutorial Demo: How to create floating dialog windows using jQuery plug-ins</title>
	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript" src="interface.js"></script>
	<script type="text/javascript" src="jquery.form.js"></script>
	<style type="text/css">
		body
		{
			font-family:Verdana, Arial, Helvetica, sans-serif;
			font-size:11px
		}
		#layer1 
		{
			position: absolute;
			left:200px;
			top:100px;
			width:250px;
			background-color:#f0f5FF;
			border: 1px solid #000;
			z-index: 50;
		}
		#layer1_handle 
		{
			background-color:#5588bb;
			padding:2px;
			text-align:center;
			font-weight:bold;
			color: #FFFFFF;
			vertical-align:middle;
		}
		#layer1_content 
		{
			padding:5px;
		}
		#close
		{
			float:right;
			text-decoration:none;
			color:#FFFFFF;
		}
	</style>
</head>

<body>
	<script type="text/javascript">
		$(document).ready(function()
		{
			$('#layer1').Draggable(
					{
						zIndex: 	20,
						ghosting:	false,
						opacity: 	0.7,
						handle:	'#layer1_handle'
					}
				);	
			$('#layer1_form').ajaxForm({
				target: '#content',
				success: function() 
				{
					$("#layer1").hide();
				}				
			});			
			$("#layer1").hide();
						
			$('#preferences').click(function()
			{
				$("#layer1").show();
			});
			
			$('#close').click(function()
			{
				$("#layer1").hide();
			});
		});
	</script>
	<a href="http://jetlogs.org/2007/07/01/jquery-floating-dialog-windows/">&laquo; Code Explanation</a> | <a href="jquery_floating_dialog.zip">Download Source</a>
	<h2>JQuery Tutorial Demo: Floating Dialog Windows</h2>
	<p>Here is the demo on using floating dialog windows by using the form and interface plug-in of jQuery</p>
	
	
	
	<div id="content"><input type="button" id="preferences" value="Edit Preferences" /></div>
	<div id="layer1">
		<div id="layer1_handle">			
			<a href="#" id="close">[ x ]</a>
			Preferences
		</div>
		<div id="layer1_content">
			<form id="layer1_form" method="post" action="save_settings.php">
				Display Settings<br />
				<input type="radio" name="display" checked="checked" value="Default" />Default<br />
				<input type="radio" name="display" value="Option A" />Option A<br />
				<input type="radio" name="display" value="Option B" />Option B<br /><br />				
				Autosave settings<br />
				<input type="radio" name="autosave" checked="checked" value="Enabled" />Enabled<br />
				<input type="radio" name="autosave" value="Disabled" />Disabled<br /><br />
				
				<input type="submit" name="submit" value="Save" />
			</form>
		</div>
	</div>
</body>
</html>
