<?php include_once 'header.php';?>

<script type="text/javascript">
function redirectToTab(event, ui) {
	var tabTitle = '';
	// fix for DEED-266 tab buttons not working in ff
	if(navigator.userAgent.indexOf("Firefox") != -1 ) { 
		tabTitle = ui.newTab[0].textContent.toLowerCase();
	} else { 
		tabTitle = ui.newTab[0].innerText.toLowerCase();
	}
    window.location = "<?php echo site_url('cartulary/edit/'.$cartnum);?>#"+tabTitle;
}
$(function() {
$("#tabs").tabs({beforeActivate: redirectToTab});
});
</script>

<?php if ($form_errors = validation_errors()) : ?>
<h4 class="alert_error"><?php echo $form_errors; ?></h4>
<?php endif;?>
<div id="tabs" class="module width_full">
  <header><h3>Cartulary Form</h3></header>
  <ul>
    <li><a href="#info">Info</a></li>
    <?php if (isset($cartnum)): ?>
      <li><a href="#resource">Resource</a></li>
      <li><a href="#image">Image</a></li>
      <li><a href="#source">Source</a></li>
      <li><a href="#biblio">Biblio</a></li>
      <li><a href="#location">Location</a></li>
      <li><a href="#institution">Institution</a></li>
    <?php endif ?>
  </ul>
<div id="info">
<form method="post">
  <label for="cartnum">Cart Num</label>
  <?php if (isset($cartnum) && $cartnum != '' && form_error('cartnum') == ''): ?>
    <?php echo $cartnum; // editing existing cartulary?>
    <input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" /> 
  <?php else :?>
    <br />
    <input type="text" name="cartnum" id="cartnum" value="<?= set_value('cartnum', (isset($cartulary['cartnum'])? $cartulary['cartnum']:''))?>" /> *
  <?php endif;?>
  <br />
  <br />

  <label for="short">Short Name</label><br />
  <input type="text" name="short" id="short" value="<?= set_value('short', (isset($cartulary['short'])? $cartulary['short']:''))?>" /> *
  <br />
  <br />

  <label for="title">Title</label><br />
  <input type="text" name="title" id="title" value="<?= set_value('title', (isset($cartulary['title'])? $cartulary['title']:''))?>" /> *
  <br />
  <br />

  <label for="multi">Multi</label><br />
  <input type="text" name="multi" id="multi" value="<?= set_value('multi', (isset($cartulary['multi'])? $cartulary['multi']:''))?>" />
  <br />
  <br />

  <label for="order-name">Order Name</label><br />
  <?= form_dropdown('order_name', $order_names, set_value('order_name', (isset($cartulary['order_name']) ? $cartulary['order_name'] : ''))); ?>
  <br />
  <br />

  <label for="cart-type">Cart Type</label><br />
  <?php echo form_dropdown('cart_type', $cart_types, set_value('cart_type', (isset($cartulary['cart_type']) ? $cartulary['cart_type'] : ''))); ?> * 
  <br />
  <br />

  <label for="series">Series</label><br />
  <?= form_dropdown('series', $series, set_value('series', (isset($cartulary['series']) ? $cartulary['series'] : ''))); ?>
  <br />
  <br />

  <label for="private">Public / Private</label><br />
  <select name="private">
    <option value="">Please select</option>
    <option value="public" <?= set_value('private', (isset($cartulary['private'])? $cartulary['private']:'')) == 'public'? 'selected':''?>>Public</option>
    <option value="private" <?= set_value('private', (isset($cartulary['private'])? $cartulary['private']:'')) == 'private'? 'selected':''?>>Private</option>
  </select> *
  <br />
  <br />

  <label for="diplomatics">Diplomatics</label>
  <?= form_checkbox('diplomatics', 'diplomatics', set_value('true', (isset($cartulary['diplomatics'])? $cartulary['diplomatics']:''))) ?>
  <br />
  <br />

  <label for="names">Names</label>
  <?= form_checkbox('names', 'names', set_value('true', (isset($cartulary['names'])? $cartulary['names']:''))) ?>

  <input type="hidden" name="save_mode" value="cartulary_info" />
  <br />
  <input type="submit" value="Save" />
</fieldset>
</form>
</div>

<?php if (isset($cartnum)): ?>

<div id="resource">
<form method="post">
  <label for="utl">UTL</label><br />
  <input type="text" name="utl" id="utl" value="<?= set_value('utl', isset($resource['utl']) ? $resource['utl'] :'')?>" />
  <br />

  <label for="google">Google</label><br />
  <input type="text" name="google" id="google" value="<?= set_value('google', isset($resource['google']) ? $resource['google'] :'')?>" />
  <br />

  <label for="wiki">Wiki</label><br />
  <input type="text" name="wiki" id="wiki" value="<?= set_value('wiki', isset($resource['wiki']) ? $resource['wiki'] :'')?>" />
  <br />
  <br />
  <label for="pdf">PDF</label> 
  <?php echo form_checkbox('pdf', 'pdf', set_value('true', (isset($resource['pdf'])? $resource['pdf']:''))); ?>
  <br />
  <br />

  <label for="worldcat">Worldcat</label><br />
  <input type="text" name="worldcat" id="worldcat" value="<?= set_value('worldcat', isset($resource['worldcat']) ? $resource['worldcat'] :'')?>" />
  <input type="hidden" name="save_mode" value="cartulary_resource" />
  <br />
  <input type="submit" value="Save" />
</form>
</div>

<div id='image'>
<form method="post">

  <label for="image-field">Image</label><br />
  <input type="text" name="image" id="image-field" value="<?= set_value('image', isset($image['image']) ? $image['image'] :'')?>" /><br />

  <label for="thumb">Thumbnail</label><br />
  <input type="text" name="thumb" id="thumb" value="<?= set_value('thumb', isset($image['thumb']) ? $image['thumb'] :'')?>" />
  <input type="hidden" name="save_mode" value="cartulary_image" />
  <br />
  <input type="submit" value="Save" />
</form>
</div>

<div id='source'>
  <form method="post" onsubmit="return validate_cartulary_source_form(-1);">
	<fieldset>
	<legend>Add a new source</legend>
	<input type="hidden" name="save_mode" value="cartulary_source" />
	<input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" />

	<table>
		<tr>
			<td><label for="source_url">Source URL *</label></td>
			<td><input type="text" name="source_url" id="source_url" /></td>
		</tr>
		<tr>
			<td><label for="source_title">Source Name *</label></td>
			<td><input type="text" name="source_title" id="source_title" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Add new source" /></td>
		</tr>
	</table>
	</fieldset>
	</form>

  <?php foreach ($sources as $key => $source) : ?>
	<form method="post" onsubmit="return validate_cartulary_source_form(<?php echo $source['sourceid']?>);">
		<input type="hidden" name="save_mode" value="cartulary_source" />
		<input type="hidden" name="sourceid" value="<?= $source['sourceid'] ?>" />
		<input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" />
		<fieldset>
		<legend>Source # <?php echo $key + 1 ?></legend>
		<table>
			<tr>
				<td><label for="source_url">Source URL *</label></td>
				<td><input type="text" name="source_url" id="source_url_<?= $source['sourceid']; ?>" value="<?= $source['source_url']?>" /></td>
			</tr>
			<tr>
				<td><label for="source_title">Source Name *</label></td>
				<td><input type="text" name="source_title" id="source_title_<?= $source['sourceid']; ?>" value="<?= $source['source_title']?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Update this source" /></td>
			</tr>
		</table>
		</fieldset>
	</form>
  <?php endforeach; ?>
</div>

<div id="biblio">
<form method="post">
  <input type="hidden" name="save_mode" value="cartulary_biblio" />

  <label for="cartnum">Cart Num</label>
  <?php if (isset($cartnum)): ?>
    <?php echo $cartnum; // editing existing biblio?>
    <input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" />
  <?php else :?>
    <br />
    <input type="text" name="cartnum" id="cartnum" value="<?= set_value('cartnum', (isset($biblio['cartnum'])? $biblio['cartnum']:''))?>" />
  <?php endif;?>
  <br />
  <br />

  <label for="biblio-field">Biblio *</label><br />
  <!--
  <input type="text" name="biblio" id="biblio-field" value="<?= set_value('biblio', (isset($biblio['biblio'])? $biblio['biblio']:''))?>" /> --> 

  <textarea name="biblio" id="biblio-field" style="width:100%; height:100px;"><?= set_value('biblio', (isset($biblio['biblio'])? $biblio['biblio']:''))?></textarea> 

  <br />
  <br />
  <input type="submit" value="Save" />
</form>
</div>

<div id="location">
	<form method="post" onsubmit="return validate_cartulary_location_form(-1);">
		<fieldset>
			<legend>Add a new Location</legend>

			<?php $instance = isset($locations) ? count($locations) + 1 : 1; ?>
			<input type="hidden" name="instance" id="instance" value='<?= $instance; ?>'/>
			<input type="hidden" name="save_mode" value="cartulary_location" />
			<input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" />

			<table>
				<tr>
					<td><label for='location_field'>Location *</label></td>
					<td><input type="text" name="location" id="location_field" /></td>
				</tr>
				<tr>
					<td><label for="latlong">Lat,Long *</label></td>
					<td><input type="text" name="latlong" id="latlong" /></td>
				</tr>
				<tr>
					<td></td>
					<td>
						Tips for formatting your coordinates so they work on Google Maps:<br />
						<ul>
							<li>Correct: 41.40338, 2.17403</li>
							<li>Incorrect: 41,40338, 2,17403</li>
							<li>List your latitude coordinates before longitude coordinates.</li>
							<li>Check that the first number in your latitude coordinate is between -90 and 90 and the first number in your longitude coordinate is between -180 and 180.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Save this new location" /></td>
				</tr>
			</table>
		</fieldset>
	</form>

  <?php foreach ($locations as $key => $location) { ?>
	<form method="post" onsubmit="return validate_cartulary_location_form(<?php echo $location['instance']?>);">
		<fieldset>
		<legend>Location # <?php echo $key + 1 ?></legend>

		<input type="hidden" name="instance" id="instance" value="<?= $location['instance'] ?>" />
		<input type="hidden" name="save_mode" value="cartulary_location" />
		<input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" />

		<table>
			<tr>
				<td><label for='location_field_<?= $location['instance'] ?>'>Location *</label></td>
				<td><input type="text" name="location" id="location_field_<?= $location['instance'] ?>" value="<?= $location['location'] ?>" /></td>
			</tr>
			<tr>
				<td><label for="latlong_<?= $location['instance'] ?>">Lat,Long *</label></td>
				<td><input type="text" name="latlong" id="latlong_<?= $location['instance'] ?>" value="<?= $location['lat'].','.$location['long'] ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td>
					Tips for formatting your coordinates so they work on Google Maps:<br />
					<ul>
						<li>Correct: 41.40338, 2.17403</li>
						<li>Incorrect: 41,40338, 2,17403</li>
						<li>List your latitude coordinates before longitude coordinates.</li>
						<li>Check that the first number in your latitude coordinate is between -90 and 90 and the first number in your longitude coordinate is between -180 and 180.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Update this location" /></td>
			</tr>
		</table>
		</fieldset>
	</form>
  <?php } ?>

</div>

<div id="institution">

<?php if(isset($institutions) == null) { ?>		
	<form method="post" onsubmit="return validate_cartulary_institution_form(-1);">

		<input type="hidden" name="save_mode" value="cartulary_institution" />
		<input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" />
		<?php $instance = isset($institutions) ? count($institutions) + 1 : 1; ?>
		<input type="hidden" name="instance" value="<?= $instance?>" />

		<fieldset>
		<legend>Add a new Institution</legend>
		<table>
			<tr>
				<td><label for="institution_field">Institution  *</label></td>
				<td><input type="text" name="institution" id="institution_field" value="<?= $institution['institution'] ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Save this new institution" /></td>
			</tr>
		</table>
		</fieldset>
	</form>
<?php } ?>
	<?php foreach ($institutions as $key => $institution) { ?>
		<form method="post" onsubmit="return validate_cartulary_institution_form(<?= $institution['instance'] ?>);">
			<input type="hidden" name="save_mode" value="cartulary_institution" />
			<input type="hidden" name="cartnum" id="cartnum" value="<?= $cartnum;?>" />
			<input type="hidden" name="instance" value="<?= $institution['instance']?>" />

			<fieldset>
			<legend>Institution # <?php echo $key + 1 ?></legend>
			<table>
				<tr>
					<td><label for="institution_field_<?= $institution['instance'] ?>">Institution *</label></td>
					<td><input type="text" name="institution" id="institution_field_<?= $institution['instance'] ?>" value="<?= $institution['institution'] ?>" /> </td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Update this institution" /></td>
				</tr>
			</table>
			</fieldset>
		</form>
  <?php } ?>

</div>

<?php endif;?>

<!-- javascript at the end to allow the DOM to finish loading -->

<script type="text/javascript">
$(function() {
	$("#tabs").tabs();
});
</script>

<script type="text/javascript" src="<?php echo base_url();?>js/tabs_firefox.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>js/cartulary_validate.js"></script>

<?php include_once 'footer.php';?>
