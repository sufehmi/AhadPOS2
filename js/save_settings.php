<?php
// Do all data processing here like saving to database, etc...
?>
<b>Preferences:</b><br />
Display Setting: 
<?php echo htmlspecialchars($_POST['display'], ENT_QUOTES); ?><br />
Autosave Setting: 
<?php echo htmlspecialchars($_POST['autosave'], ENT_QUOTES); ?><br />