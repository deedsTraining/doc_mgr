<!DOCTYPE HTML>
<html>
	<head>
	<title> DEEDS Document Manager - Login </title>

	</head>

	<body style="text-align: center;">
    <h2>DEEDS Document Manager</h2>
	 <?php echo validation_errors(); ?>
	<fieldset style="width:300px; margin:50px auto; font-size:1.2em;">
	<legend> Enter login information </legend>
	<?php echo form_open('login/verify'); ?>
		<?php if (isset($new_charter_cartnum)) { ?>
			<input type="hidden" id="new_charter_cartnum" name="new_charter_cartnum" value="<?=set_value('new_charter_cartnum', (isset($new_charter_cartnum)?$new_charter_cartnum:'')); ?>">
		<?php } ?>
		<input type="hidden" id="type" name="type" value="<?=set_value('type', (isset($type)?$type:'')); ?>">
		<input type="hidden" id="action" name="action" value="<?=set_value('action', (isset($action)?$action:'')); ?>">
		<input type="hidden" id="id_num" name="id_num" value="<?=set_value('id_num', (isset($id_num)?$id_num:'')); ?>">
		<label for="username">Username</label>
		<input type="text" id="username" name="username">

		<br>

		<label for="password">Password</label>
		<input type="password" id="password" name="password">

		<br>

		<input type="submit" value="Login">
	</form>

	</fieldset>

	</body>

</html>