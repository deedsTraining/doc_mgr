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
  window.location = "<?php echo site_url('institution/edit/'.$instid);?>#"+tabTitle;
}
$(function() {
  $("#tabs").tabs({beforeActivate: redirectToTab});
});
</script>

<?php if ($form_errors = validation_errors()):?>
<h4 class="alert_error"><?php echo $form_errors; ?></h4>
<?php endif;?>

<article class="module width_full">
  <header>
    <h3>Institution Form</h3>
  </header>
<div id="tabs">
  <ul>
    <li><a href="#info">Info</a></li>
    <li><a href="#location">Location</a></li>
    <li><a href="#resource">Resource</a></li>
    <li><a href="#date">Date</a></li>
  </ul>

  <div id="info">
    <form method="post">
    <?php if (isset($instid)): ?>
      <input type="hidden" name="instid" id="instid" value="<?= $institution->instid;?>" />
      <label for="inst_name">Institution Name *</label>
      <input id="inst_name" name="inst_name" type="text" value="<?= set_value('inst_name', (isset($institution->inst_name)? $institution->inst_name:''))?>" style="width: 350px">
    <?php else :?>
      <?php if (isset($new_instid)) { ?>
      <input type="hidden" name="instid" id="instid" value="<?= $new_instid?>" />
    <?php } else { ?>
      <input type="hidden" name="instid" id="instid" value="<?= set_value('instid', (isset($institution->instid)? $institution->instid:''))?>" />
      <?php  } ?>
        <label for="inst_name">Institution Name *</label>
        <input id="inst_name" name="inst_name" type="text" value="<?= set_value('inst_name', (isset($institution->inst_name)? $institution->inst_name:''))?>" style="width: 350px">
    <?php endif;?>
    <br>

    <label for="inst_type">Institution Type *</label>
    <?php echo form_dropdown('inst_type', $dm_inst_type_lookup, set_value('inst_type', (isset($institution->inst_type) ? $institution->inst_type : '')), 'id="info_inst_type"'); ?>
    <br>

    <label for="inst_rank">Institution Rank *</label>
    <!-- below options will be filled based on inst_type selection above-->
    <?php echo form_dropdown('inst_rank', $dm_inst_rank_lookup, set_value('inst_rank', (isset($institution->inst_rank) ? $institution->inst_rank : '')), 'id="info_inst_rank"'); ?>

    <br />
    <br />

    <label for="order_name">Order Name</label>
    <?php echo form_dropdown('order_name', $order_names, set_value('order_name', (isset($institution->order_name) ? $institution->order_name : ''))); ?>
    <br>
    <br>

    <label for="mother_house">Mother House</label><br />
    <?php echo form_dropdown('mother_house', $mother_houses, set_value('mother_house', (isset($institution->mother_house) ? $institution->mother_house : ''))); ?>

    <br />
    <br />

    <label for="alien_house">Alien House</label>
    <select name="alien_house" id="alien_house">
      <option value="Yes" "<?= set_select('alien_house', 'Yes', (isset($institution->alien_house) && ($institution->alien_house == 'Alien')) ? TRUE:FALSE)?>">Yes </option>
      <option value="No" "<?= set_select('alien_house', 'No', (!isset($institution->alien_house)) ? TRUE:FALSE)?>">No</option>
    </select>
    <br>
    <label for="convent">Convent</label>
    <select name="convent" id="convent">
      <option value="Yes" "<?= set_select('convent', 'Yes', (isset($institution->convent) && ($institution->convent == 'Convent')) ? TRUE:FALSE)?>">Yes </option>
      <option value="No" "<?= set_select('convent', 'No', (!isset($institution->convent)) ? TRUE:FALSE)?>">No</option>
    </select>
    <br>
    <input type="hidden" name="save_mode" value="institution_info" />
    <?php if ($error_location == 'institution_location' || $error_location == 'institution_date') { ?>
      <input type="submit" value="Update"  disabled>
    <?php } else { ?>
      <input type="submit" value="Update">
    <?php } ?>
    </form>
  </div>

  <div id="location">
    <form method="post">
    <label for="lat">Latitude *</label>
    <input type="hidden" name="inst_name" value="<?= set_value('inst_name',(isset($location->inst_name)? $location->inst_name:(isset($institution->inst_name)? $institution->inst_name:'')))?>" />
    <input type="hidden" name="instid" value="<?= set_value('instid', (isset($location->instid)? $location->instid:(isset($new_loc_id)? $new_loc_id: ''))); ?>" />
    <input id="lat" name="lat" type="text" value="<?= set_value('lat', (isset($location->lat)? $location->lat: ''))?>">
    <br>
    <label for="long">Longitude *</label>
    <input id="long" name="long" type="text" value="<?= set_value('long', (isset($location->long)? $location->long: ''))?>">
    <br>
    <label for="loc">Location *</label>
    <input id="loc" name="loc" type="text" value="<?= set_value('loc',(isset($location->location)? $location->location: '')) ?>">
    <br>
      <input type="hidden" name="save_mode" value="institution_location" />
       <?php if ($error_location == 'institution_info' || $error_location == 'institution_date' || isset($new_instid)) { ?>
      <input type="submit" value="Update" disabled>
      <?php } else { ?>
         <input type="submit" value="Update">
      <?php } ?>
    </form>
  </div>

  <div id="resource">
      <legend>Institution Resource</legend>
      <form method="post">
      <label for="wiki">Wiki URL</label>
      <input type="hidden" name="instid" value="<?=set_value('instid', (isset($resource->instid)? $resource->instid:(isset($institution->instid)? $institution->instid:''))); ?>" />
      <input type="hidden" name="inst_name" value="<?=set_value('inst_name',(isset($resource->inst_name)? $resource->inst_name:(isset($institution->inst_name)? $institution->inst_name:''))); ?>" />
      <input id="wiki" name="wiki" type="text" value="<?=set_value('wiki',(isset($resource->wiki)? $resource->wiki:'')); ?>" style="width: 350px">
      <br>
      <input type="hidden" name="save_mode" value="institution_resource" />
      <?php if ($error_location == 'institution_info' || $error_location == 'institution_date' || $error_location == 'institution_location' || isset($new_instid)) { ?>
        <input type="submit" value="Update" disabled>
      <?php } else { ?>
      <input type="submit" value="Update">
      <?php } ?>
      </form>
    </div>

    <div id="date">
      <form method="post">
      <input type="hidden" name="dateid" value="<?=set_value('dateid',(isset($date->did)? $date->did:'')); ?>" />
      <input type="hidden" name="inst_name" value="<?=set_value('inst_name',(isset($date->inst_name)? $date->inst_name:(isset($institution->inst_name)? $institution->inst_name:''))); ?>" />
      <label for="first-date">First Date *</label>
      <input id="first-date" name="first-date" type="text" value="<?= set_value('first-date', (isset($date->first_date)? $date->first_date:'')); ?>"> 
      <br>
       <label for="last-date">Last Date *</label>
      <input id="last-date" name="last-date" type="text" value="<?= set_value('last-date', (isset($date->last_date)? $date->last_date:'')); ?>">
      <br>
       <label for="circa">Circa</label>
    <input type="radio" name="circa" value="Yes" "<?= set_radio('circa', 'Yes', (isset($date->circa) && ($date->circa == 'Circa')) ? TRUE:FALSE)?>">Yes
    <input type="radio" name="circa" value="No" "<?= set_radio('circa', 'No', (is_null($date->circa)) ? TRUE:FALSE)?>">No</option>
      <br>
        <input type="hidden" name="save_mode" value="institution_date" />
        <?php if ($error_location == 'institution_info' || $error_location == 'institution_location' || isset($new_instid)) { ?>
        <input type="submit" value="Update" disabled>
        <?php } else { ?>
     <input type="submit" value="Update">
        <?php } ?>
    </form>
  </div>
</div>

<?php include_once 'footer.php';?>
</article>
