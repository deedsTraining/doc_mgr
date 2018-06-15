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
    window.location = "<?php echo site_url('charter/edit/'.$docnum);?>#"+tabTitle;
	}
  $(function() {
    $("#tabs").tabs({beforeActivate: redirectToTab});
  });
</script>


<?php if ($form_errors= validation_errors()): ?>
<h4 class="alert_error"><?php echo $form_errors;?></h4>
<?php endif;?>

<div id="tabs" class="module width_full">
  <header><h3>Charter Form for charter # <?= $docnum ?></h3></header>
  <ul>
    <li><a href="#info">Info</a></li>

    <?php if ($docnum != ''): ?>
    <li><a href="#document-text">Document-Text</a></li>
    <li><a href="#date">Date</a></li>
    <li onclick="set_markup_mode('names');"><a href="#markups">Markups</a></li>
    <?php /*<li><a href="#names-title">Names-Title</a></li> */ ?>
    <li><a href="#image">Image</a></li>
    <li><a href="#resource">Resource</a></li>
    <li><a href="#notes">Notes</a></li>
    <?php /* <li><a href="#charter-parties">Parties</a></li> */ ?>
    <li><a href="#location">Location</a></li>
    <li><a href="#institution">Institution</a></li>
    <?php endif; ?>
  </ul>
  <div id="info">
    <form method="post">
      <input type="hidden" name="save_mode" value="charter_info" />
      <label for="docnum">Document Number</label>
      <?php if ($docnum != '') {
      	echo $docnum;
      ?>
      <input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">
      <?php } else { ?>
      <input type="text" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">
      <?php } ?>
      <br />
      <label for="language">Language</label>
      <select id="language" name="language">
        <?php //<option value="">Pick a language</option> ?>

        <?php 
          // Add Latin to the top of list
          $dm_charter_language_lookup_sort = array();

          foreach ($dm_charter_language_lookup as $language_lookup) :
            if($language_lookup->language == "Latin") { 
              array_unshift($dm_charter_language_lookup_sort, $language_lookup);
            } else { 
              array_push($dm_charter_language_lookup_sort, $language_lookup); 
            }
          endforeach;
        ?>

        <?php foreach ($dm_charter_language_lookup_sort as $language_lookup) :?>
        <option value="<?php echo $language_lookup->language;?>" <?=set_value ('language',(isset($charter->language)? $charter->language: '')) == $language_lookup->language ? 'selected': ''?>><?php echo $language_lookup->language;?></option>
        <?php endforeach;?>
      </select>
      <br />
      <label for="origin">Origin</label>
      <select id="origin" name="origin">
        <option value="">Please select</option>
        <?php foreach ($dm_charter_origin_lookup as $origin_lookup) :?>
        <option value="<?php echo $origin_lookup->origin;?>" <?=set_value ('origin',(isset($charter->origin)? $charter->origin: '')) == $origin_lookup->origin ? 'selected': ''?>><?php echo $origin_lookup->origin;?></option>
        <?php endforeach;?>
      </select>
      <br />

      <label>Charter Type</label><br />
      <div style="width: 250px; height: 200px; overflow: scroll;">
      <?php foreach ($dm_charter_type_lookup as $i => $type_lookup):?>
        <?php $type_checkbox_data = array('name'=>'charter_type[]', 'id'=>'charter_type_'.$i, 'value'=>$type_lookup->type, 'checked' => in_array($type_lookup->type, $charter_type));?>
        <?php echo form_checkbox($type_checkbox_data);?> <?php echo form_label($type_lookup->type, 'charter_type_'.$i);?><br />
      <?php endforeach; ?>
      </div>
      <br />



<?php /*
      <label for="charter_source">Charter Source</label>
      <select id="charter_source" name="charter_source">
        <option value="" <?=set_value ('charter_source',(isset($charter->charter_source)? $charter->charter_source: '')) == '' ? 'selected': ''?>>Pick a source</option>
        <option value="Cartulary Copy" <?=set_value ('charter_source',(isset($charter->charter_source)? $charter->charter_source: '')) == 'Cartulary Copy' ? 'selected': ''?>>Cartulary Copy</option>
        <option value="Manuscript" <?=set_value ('charter_source',(isset($charter->charter_source)? $charter->charter_source: '')) == 'Manuscript' ? 'selected': ''?>>Manuscript</option>
        <option value="Online Collection" <?=set_value ('charter_source',(isset($charter->charter_source)? $charter->charter_source: '')) == 'Online Collection' ? 'selected': ''?>>Online Collection</option>
      </select>
*/ ?>

      <label for="charter_source">Charter Source</label>
      <select id="charter_source" name="charter_source">
        <option value="">Pick a Charter Source</option>
        <?php foreach ($dm_charter_source_lookup as $source_lookup) :?>
        <option value="<?php echo $source_lookup->source;?>" <?=set_value ('charter_source',(isset($charter->charter_source)? $charter->charter_source: '')) == $source_lookup->source ? 'selected': ''?>><?php echo $source_lookup->source;?></option>
        <?php endforeach;?>
      </select> *
      <br />
      <br />

      <label for="charter_status">Charter Status</label><br />
      <?php foreach ($dm_charter_status_lookup as $status_lookup):?>


        <?php 
        $radio_checked = FALSE;

        if($status_lookup->status == $charter_status[0]->charter_status) { $radio_checked = TRUE; }
        ?>

        <?php echo form_radio('charter_status', $status_lookup->status, $radio_checked) ?> <?php echo form_label($status_lookup->status, 'charter_status_'.$status_lookup->status_code);?><br />

      <?php endforeach;?>

      <label for="embedded-in">Embedded In</label>
      <input type="text" id="embedded-in" name="embedded-in" value="<?php echo set_value('embedded-in', (in_array('Embedded', $charter_status)) ? $charter_ref:'');?>" />

      <br />
      <input type="submit" value="Save" />
    </form>
  </div>


  <?php if ($docnum != ''): ?>
  <div id="document-text">
    <form method="post">
      <input type="hidden" name="save_mode" value="charter_doc" />
      <br />

      <label for="txt">Document Content</label><br />
      <textarea name="txt" id="txt" style="width:100%; height:250px;"><?= set_value('txt', isset($doc['txt']) ? $doc['txt'] :'')?></textarea>
      <br />
      <br />

      <input type="hidden" name="save_mode" value="charter_doc" />
      <input type="submit" value="Save" />
    </form>
    </div>

    <div id="date">
		<form method="post">
			<input type="hidden" name="save_mode" value="charter_date" />
			<table class='form-big-table'>
				<tr>
					<td>Dated</td>
					<td><input type="text" id="dated" name="dated"  value="<?=set_value ('dated',(isset($date->dated)? $date->dated: ''))?>"></td>
				</tr>
				<tr>
					<td>Low Date</td>
					<td><input type="text" id="lodate" name="lodate" value="<?=set_value ('lodate',(isset($date->lodate)? $date->lodate: ''))?>"></td>
				</tr>
				<tr>
					<td>High Date</td>
					<td><input type="text" id="hidate" name="hidate" value="<?=set_value ('hidate',(isset($date->hidate)? $date->hidate: ''))?>"></td>
				</tr>
				<tr>
					<td>Circa</td>
					<td>
						<input type="radio" id="circa" name="circa" value="circa" <?=set_value ('circa',(isset($date->circa)? 'checked': ''))?> /> Yes
						<input type="radio" id="circa" name="circa" value="" <?= isset($date->circa)? '': 'checked'?> /> No
					</td>
				</tr>
				<tr>
					<td>Scope</td>
					<td><?= form_dropdown("scope", $date_scope, set_value('scope',(isset($date->scope)? $date->scope: ''))); ?></td>
				</tr>
				<tr>
					<td>Date Type</td>
					<td>
						<ul class='no-dot-list'>
							<li><?php echo form_checkbox('date_type[]', 'A.D.', in_array("A.D.", $date_type_selected))."A.D."; ?></li>
							<li><?php echo form_checkbox('date_type[]', 'Abbot-Prior', in_array("Abbot-Prior", $date_type_selected))."Abbot-Prior"; ?></li>
							<li><?php echo form_checkbox('date_type[]', 'Calendar', in_array("Calendar", $date_type_selected))."Calendar"; ?></li>
							<li><?php echo form_checkbox('date_type[]', 'Episcopal', in_array("Episcopal", $date_type_selected))."Episcopal"; ?></li>
							<li><?php echo form_checkbox('date_type[]', 'Event', in_array("Event", $date_type_selected))."Event"; ?></li>
							<li><?php echo form_checkbox('date_type[]', 'Feast', in_array("Feast", $date_type_selected))."Feast"; ?></li>
							<li><?php echo form_checkbox('date_type[]', 'Papal', in_array("Papal", $date_type_selected))."Papal"; ?></li>
							<li><?php echo form_checkbox('date_type[]', 'Regnal', in_array("Regnal", $date_type_selected))."Regnal"; ?></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Save" /></td>
				</tr>
			</table>
		</form>
    </div>

    <div id="markups">
        <br />
        <label>Create / Edit :</label>
        <input type="button" value="Name Markkups" onclick="set_markup_mode('names');" />
        <input type="button" value="Diplomatic Markups" onclick="set_markup_mode('diplomatics');" />

        <h2 id="markup-tab-title">Name Markups</h2>
        <form method="post" onsubmit="return validate_markup_form();">
        <input type="hidden" name="save_mode" value="charter_markup" />
          <table style="width: 100%">
            <tr>
              <td colspan="2">
                Markup Area
                <span id="markup-guide" style="color: orange;"></span><br />
                <br />
                <textarea id="markup_canvas" readonly><?php echo $doc['txt'];?></textarea>
                <span id="markup_canvas_display" style="display:none;"><?php echo $doc['txt'];?></span>
              </td>
            </tr>

            <tr>
              <td style="width:350px;vertical-align: top;">
                Existing Markups - click to view / update
                <ul id="markup_listing_names">
                  <?php foreach ($names as $i=>$name):?>
                  <li id="markup_names_<?php echo $i?>">
                    <span class="markup_text"><?php echo $name['txt'];?></span>
                    <span class="markup_instance" style="display: none;"><?php echo $name['instance'];?></span>
                    <span class="markup_item" style="display: none;"><?php echo $name['item'];?></span>
                    <span class="markup_start" style="display: none;"><?php echo $name['start'];?></span>
                    <span class="markup_end" style="display: none;"><?php echo $name['end'];?></span>
                    <span class="markup_type" style="display: none;"><?php echo $name['names'];?></span>
                  </li>
                  <?php endforeach; ?>
                </ul>

                <ul id="markup_listing_diplomatics" style="display:none;">
                  <?php foreach ($diplomatics as $diplomatic):?>
                  <li id="markup_diplomatics_<?php echo $i?>">
                    <span class="markup_text"><?php echo $diplomatic['txt'];?></span>
                    <span class="markup_instance" style="display: none;"><?php echo $diplomatic['instance'];?></span>
                    <span class="markup_item" style="display: none;"><?php echo $diplomatic['item'];?></span>
                    <span class="markup_start" style="display: none;"><?php echo $diplomatic['start'];?></span>
                    <span class="markup_end" style="display: none;"><?php echo $diplomatic['end'];?></span>
                    <span class="markup_type" style="display: none;"><?php echo $diplomatic['layer'];?></span>
                  </li>
                  <?php endforeach; ?>
                </ul>
               </td>

               <td style="vertical-align: top">
                  <strong>Current Selection</strong>
                  <input type="submit" value="Add current selection to diplomatics" id="markup_save_button"/>
                  <input style="display:none;" type="button" id="markup_edit_button" value="Edit selected markup" onclick="markup_start_editing();" />
                  <input type="button" value="Reset" onclick="cancel_markup();"/>
                  <br />
                  <br />
                  Selection Start: <span id="markup_start" style="color:red"></span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  Selection End: <span id="markup_end" style="color:red"></span><br />
                  <br />

                  <input type="hidden" id="markup_type" name="markup_type" value="" />
                  <label for="markup_type">Markup Type</label>
                  <select id="names_markup_type">
                    <option value="">Please specify</option>
                    <?php foreach($dm_markup_types_lookup['names'] as $markup_names):?>
                    <option><?php echo $markup_names->names;?></option>
                    <?php endforeach;?>
                  </select>

                  <select id="diplomatics_markup_type" style="display:none;">
                    <option value="">Please specify</option>
                    <?php foreach($dm_markup_types_lookup['diplomatics'] as $markup_diplomatics):?>
                    <option><?php echo $markup_diplomatics->layer;?></option>
                    <?php endforeach;?>
                  </select>

                  <br />
                  <br />
                  Selected Text:<br /> <span id="markup_text" style="color:blue;"></span>
                  <br />
                  <br />
                  <input type="hidden" name="save_mode" value="charter_markup" />

                  <input type="hidden" id="markup_instance_val" name="markup_instance_val" />
                  <input type="hidden" id="markup_item_val" name="markup_item_val" />

                  <input type="hidden" id="markup_start_val" name="markup_start_val" />
                  <input type="hidden" id="markup_end_val" name="markup_end_val"/>
                  <input type="hidden" id="markup_text_val" name="markup_text_val" />
                  <br />
                  <input type="hidden" id="loaded_from" />
               </td>
              </tr>
          </table>
        </form>
      </div>

<?php /*
      <div id="names-title">
			<?php foreach ($names_titles as $key => $name_title) { ?>
				<form method='post'>
					<input type='hidden' name='save_mode' value='charter_names_title' />
					<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">
					<input type='hidden' name='tid' value='<?= $name_title['tid'] ?>' />
					<input type='hidden' name='layer_instance' value="<?= $name_title['layer_instance'] ?>" />
					<input type='hidden' name='item_instance' value="<?= $name_title['item_instance'] ?>" />
					<input type='hidden' name='title_instance' value="<?= $name_title['title_instance'] ?>" />

					<fieldset>
						<legend>Charter Names Title # <?= $key + 1 ?></legend>
						<table>
							<tr>
								<td>First name</td><td><input type='text' id='first_name' name='first_name' value="<?= $name_title['first_name'] ?>" /></td>
							</tr>
							<tr>
								<td>Definitive name</td><td><input type='text' id='definitive_name' name='definitive_name' value="<?= $name_title['definitive_name'] ?>" /></td>
							</tr>
							<tr>
								<td>Standard name</td><td><input type='text' id='standard_name' name='standard_name' value="<?= $name_title['standard_name'] ?>" /></td>
							</tr>
							<tr>
								<td>Definitive name type</td><td><?= form_dropdown('definitive_name_type', $definitive_name_types, $name_title['definitive_name_type']) ?></td>
							</tr>
							<tr>
								<td>Name type</td><td><?= form_dropdown('name_type', $charter_origin, $name_title['name_type']) ?></td>
							</tr>
							<tr>
								<td>Name role</td><td><?= form_dropdown('name_role', $name_title_name_roles, $name_title['name_role']) ?></td>
							</tr>
							<tr>
								<td>Nature</td><td><?= form_dropdown('nature', $name_natures, $name_title['nature']) ?></td>
							</tr>
							<tr>
								<td>Title text</td><td><?= form_dropdown('title_txt', $title_txt, $name_title['title_txt']) ?></td>
							</tr>
							<tr>
								<td>Institution name</td><td><?= form_dropdown('inst_name', $inst_names, $name_title['inst_name']) ?></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" value="Update all Names titles data" /></td>
							</tr>
						</table>
					</fieldset>
				</form>
			<?php } ?>

			<form method='post'>
				<input type='hidden' name='save_mode' value='charter_names_title' />
				<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">
				<fieldset>
					<legend>Add a New Names Title</legend>
					<table>
						<tr>
							<td>First name</td><td><input type='text' id='first_name' name='first_name' /></td>
						</tr>
						<tr>
							<td>Definitive name</td><td><input type='text' id='definitive_name' name='definitive_name' /></td>
						</tr>
						<tr>
							<td>Standard name</td><td><input type='text' id='standard_name' name='standard_name'  /></td>
						</tr>
						<tr>
							<td>Definitive name type</td><td><?= form_dropdown('definitive_name_type', $definitive_name_types) ?></td>
						</tr>
						<tr>
							<td>Name type</td><td><?= form_dropdown('name_type', $charter_origin) ?></td>
						</tr>
						<tr>
							<td>Name role</td><td><?= form_dropdown('name_role', $name_title_name_roles) ?></td>
						</tr>
						<tr>
							<td>Nature</td><td><?= form_dropdown('nature', $name_natures) ?></td>
						</tr>
						<tr>
							<td>Title text</td><td><?= form_dropdown('title_txt', $title_txt) ?></td>
						</tr>
						<tr>
							<td>Institution name</td><td><?= form_dropdown('inst_name', $inst_names) ?></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Save this new Names title" /></td>
						</tr>
					</table>
				</fieldset>
			</form>
      </div>
*/ ?>

      <div id="image">
		  <form method='post' onsubmit="return validate_charter_image_form(-1);" >
				<input type='hidden' name='save_mode' value='charter_image' />
				<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">

				<table>
					<tr>
						<td>Image *</td>
						<td><input type='text' id='image' name='image' value="<?=set_value ('image',(isset($image->image)? $image->image: ''))?>" />
            </td>
					</tr>
					<tr>
						<td>Thumbnail *</td>
						<td><input type='text' id='thumb' name='thumb' value="<?=set_value ('thumb',(isset($image->thumb)? $image->thumb: ''))?>" /></td>
					</tr>
          <tr>
            <td>&nbsp;</td>
            <td><p>If no image then images are na.jpg, na.png</p></td>
          </tr>
					<tr>
						<td></td>
						<td><input type="submit" value="Save" /></td>
					</tr>
				</table>
			</fieldset>
		  </form>
      </div>

      <div id="resource">

		<?php foreach ($resources as $key => $resource) { ?>
			 <form method='post' onsubmit="return validate_charter_resource_form(<?= $key ?>);">
			 	<fieldset>
					<legend>Resource # <?= $key + 1 ?></legend>

					<input type='hidden' name='save_mode' value='charter_resource' />
					<input type="hidden" id="docnum" name="docnum" value="<?=set_value('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">

					<table>
						<tr>
							<td>Resource URL</td>
							<td><input type='text' id='resource_url_<?= $key ?>' name='resource_url' value="<?= set_value('resource_url', (isset($resource['resource_url']) ? $resource['resource_url'] : '')); ?>" /></td>
						</tr>
						<tr>
							<td>URL_TITLE</td>
							<td><input type='text' id='url_title_<?= $key ?>' name='url_title' value="<?= set_value('url_title', (isset($resource['url_title']) ? $resource['url_title'] : '')); ?>" /></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Save this resource" /></td>
						</tr>
					</table>
				</fieldset>
			</form>
      	<?php } ?>

		<form method='post' onsubmit="return validate_charter_resource_form(-1);">
			<fieldset>
			<legend>Add a new resource</legend>

      		<input type='hidden' name='save_mode' value='charter_resource' />
      		<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">

			<table>
				<tr>
					<td>Resource URL</td>
					<td><input type='text' id='resource_url' name='resource_url' /></td>
				</tr>
				<tr>
					<td>URL Title</td>
					<td><input type='text' id='url_title' name='url_title' /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Add this new resource" /></td>
				</tr>
			</table>
			</fieldset>
		</form>
      </div>

      <div id="notes">
      	<?php foreach ($notes as $key => $note) { ?>
      		<form method='post' onsubmit="return validate_charter_notes_form(<?= $key ?>);">
      			<input type='hidden' name='save_mode' value='charter_notes' />
				<input type='hidden' name='docid' value='<?= $note['docid'] ?>' />
				<input type="hidden" id="docnum" name="docnum" value="<?= $charter->docnum ?>">
      			<input type='hidden' name='instance' value="<?= $note['instance']?> ">
      			<fieldset>
      				<legend><h1>Notes # <?= $key + 1 ?></h1></legend>
					<table>
						<tr>
							<td>Note Type</td>
							<td><?php echo form_dropdown('note_type', $note_types, $note['note_type'], "id=\"note_type_$key\""); ?></td>
						</tr>
						<tr>
							<td>Note Text</td>
							<td><textarea name="note_text" id='note_text_<?= $key ?>'  style="width:500px; height:160px;"><?=set_value('note_text',(isset($note['note_text'])? $note['note_text']: ''))?></textarea></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Update this note" /></td>
						</tr>
					</table>
      			</fieldset>
      		</form>
      	<?php } ?>
      	<form method="post" onsubmit="return validate_charter_notes_form(-1);">
			<fieldset>
				<?php $instance = isset($notes) ? count($notes) + 1 : 1; ?>
				<input type='hidden' name='instance' value='<?= $instance ?>' />
				<input type="hidden" id="docnum" name="docnum" value="<?= $charter->docnum ?>">
				<input type='hidden' name='save_mode' value='charter_notes' />

				<legend><h1>Add a new note</h1></legend>
				<table>
					<tr>
						<td>Note Type</td>
						<td><?php echo form_dropdown('note_type', $note_types, 'Please Select', "id=\"note_type\""); ?></td>
					</tr>
					<tr>
						<td>Note Text</td>
						<td><textarea name="note_text" id="note_text" style="width:500px; height:160px;"><?php echo set_value('note_text_new');?></textarea></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="Save this new note" /></td>
					</tr>
				</table>				
			</fieldset>
		</form>
    </div>
<?php /*
      <div id="charter-parties">
		  <?php if (isset($parties) && count($parties) > 1) {?>
			  <!-- Edit the charter_parties -->
			  <form method='post'>
					<!-- Save Mode -->
					<input type='hidden' name='save_mode' value='charter_parties' />

					<!-- Doc Num -->
					<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">

					<!-- 1 to many -->
					<?php foreach ($parties as $key => $party) { ?>
						<!-- Fields -->
						<fieldset>
							<legend>Charter parties # <?= $key + 1 ?></legend>
							<input type="hidden" name="party_instance[]" id="party_instance" value='<?= $party['party_instance']; ?>'/>
							<input type="hidden" name="title_instance[]" id="title_instance" value='<?= $party['title_instance']; ?>'/>
							<input type="hidden" name="person_instance[]" id="person_instance" value='<?= $party['person_instance']; ?>'/>
							<table>
								<tr><td>Type</td><td> <?= form_dropdown('parties_type[]', $parties_type, $party['parties_type']); ?></td></tr>
								<tr><td>Person Type</td><td> <?= form_dropdown('person_type[]', $parties_person_types, $party['person_type']); ?></td></tr>
								<tr><td>Name Type</td><td> <?= form_dropdown('name_type[]', $parties_name_types, $party['name_type']); ?></td></tr>
								<tr><td>Name Role</td><td><?= form_dropdown('name_role[]', $parties_name_roles, $party['name_role']); ?></td></tr>
								<tr><td>Name Link</td><td> <input type='text' id='name_link' name='name_link[]' value="<?=set_value('name_link',(isset($party['name_link']) ? $party['name_link'] : ''))?>" /></td></tr>
								<tr><td>Name Text</td><td> <input type='text' id='name_txt' name='name_txt[]' value="<?=set_value('name_txt',(isset($party['name_txt']) ? $party['name_txt'] : ''))?>" /></td></tr>
								<tr><td>Title Institution</td><td> <?= form_dropdown('title_inst[]', $inst_names, $party['title_inst']); ?></td></tr>
								<tr><td>Title Text</td><td> <input type='text' id='title_txt' name='title_txt[]' value="<?=set_value('title_txt',(isset($party['title_txt']) ? $party['title_txt'] : ''))?>" /></td></tr>
							</table>

						</fieldset>

					<?php } ?>
					<input type="submit" value="Save existing parties data" />
			  </form>
		<?php } ?>
      	  <!-- Add new party -->
		  <form method='post'>
				<!-- Save Mode -->
				<input type='hidden' name='save_mode' value='charter_parties' />

				<!-- Doc Num -->
				<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">

				<fieldset>
					<legend>Add a new party</legend>
						<?php $instance = isset($parties) ? count($parties) + 1 : 1; ?>
						<input type='hidden' id='party_instance' name='party_instance[]' value=<?= $instance ?>/>
						<input type='hidden' id='person_instance' name='person_instance[]' value=<?= $instance ?> />
						<input type='hidden' id='title_instance' name='title_instance[]' value=<?= $instance ?> />
						<table>
						<tr><td>Type</td><td> <?= form_dropdown('parties_type[]', $parties_type) ?></td></tr>
						<tr><td>Person Type</td><td> <?= form_dropdown('person_type[]', $parties_person_types) ?></td></tr>
						<tr><td>Name Type</td><td> <?= form_dropdown('name_type[]', $parties_name_types); ?></td></tr>
						<tr><td>Name Role</td><td> <?= form_dropdown('name_role[]', $parties_name_roles); ?></td></tr>
						<tr><td>Name Link</td><td> <input type='text' id='name_link' name='name_link[]' /></td></tr>
						<tr><td>Name Text</td><td> <input type='text' id='name_txt' name='name_txt[]' /></td></tr>
						<tr><td>Title Institution</td><td> <?= form_dropdown('title_inst[]', $inst_names) ?></td></tr>
						<tr><td>Title Text</td><td> <input type='text' id='title_txt' name='title_txt[]' /></td></tr>
						</table>
				</fieldset>

				<input type="submit" value="Add new party" />
		  </form>
      </div>
*/ ?>
      <div id="location">
        <!-- Edit the charter_location -->
        <fieldset>
          <legend>Add new location</legend>
          <form method="post" onsubmit="return validate_charter_location_form(<?= count($locations)+1?>);">
    		 <!-- Save Mode -->
      		<input type='hidden' name='save_mode' value='charter_locations' />

      		<!-- Doc Num -->
      		<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">

      		<input type="hidden" name="instance" value="<?= count($locations)+1?>">

      		<table>
				<tr>
					<td>Location Type</td>
					<td>
					   <select id="location_type_<?= count($locations)+1?>" name="location_type">
					     <option value="">Please select</option>
					     <option value="property">Property</option>
					     <option value="issued">Issued</option>
					   </select>
					 </td>
				</tr>
				<tr><td>Property Type</td><td><input type='text' id="property_type_<?= count($locations)+1?>" name='property_type' value="" /></td></tr>
				<tr><td>Lat, Long</td><td> <input type='text' name='latlong' id="latlong_<?= count($locations)+1?>" value="" /> Example: 51.961269,-0.098948 (no space)</td></tr>
				<tr><td>Country</td><td> <input type='text' name='country' id="country_<?= count($locations)+1?>" value="" /></td></tr>
				<tr><td>County</td><td> <input type='text' name='county' id="county_<?= count($locations)+1?>" value="" /></td></tr>
				<tr><td>Place </td><td><input type='text' name='place' id="place_<?= count($locations)+1?>" value="" /></td></tr>
				<tr><td>&nbsp;</td><td><input type="submit" value="Save" /></td></tr>
			</table>
            </form>
        </fieldset>

        <fieldset>
          <legend>Existing locations:</legend>
          <?php foreach ($locations as $location) :?>
            <form method="post" onsubmit="return validate_charter_location_form(<?= $location['instance']?>);">
          		<!-- Save Mode -->
          		<input type='hidden' name='save_mode' value='charter_locations' />

          		<!-- Doc Num -->
          		<input type="hidden" id="docnum" name="docnum" value="<?=set_value ('docnum',(isset($charter->docnum)? $charter->docnum: ''))?>">

          		<input type="hidden" name="instance" value="<?=set_value ('instance',(isset($location['instance'])? $location['instance']: ''))?>">

          		<table>
      				<tr>
      					<td>Location Type</td>
      					<td>
      					   <select id="location_type_<?= $location['instance']?>" name="location_type">
      					     <option value="">Please select</option>
      					     <option value="property" <?= $location['location_type'] == 'property' ? 'selected': ''?>>Property</option>
      					     <option value="issued" <?= $location['location_type'] == 'issued' ? 'selected': ''?>>Issued</option>
      					   </select>
      					 </td>
      				</tr>
      				<tr><td>Property Type</td><td><input type='text' id="property_type_<?= $location['instance']?>" name='property_type' value="<?=set_value('property_type',(isset($location['property_type'])? $location['property_type']: ''))?>" /></td></tr>
      				<tr><td>Lat, Long</td><td> <input type='text' name='latlong' id="latlong_<?= $location['instance']?>" value="<?=set_value('latlong', $location['lat'].','.$location['long'])?>" /> Example: 51.961269,-0.098948 (no space)</td></tr>
      				<tr><td>Country</td><td> <input type='text' name='country' id="country_<?= $location['instance']?>" value="<?=set_value('country',(isset($location['country'])? $location['country']: ''))?>" /></td></tr>
      				<tr><td>County</td><td> <input type='text' name='county' id="county_<?= $location['instance']?>" value="<?=set_value('county',(isset($location['county'])? $location['county']: ''))?>" /></td></tr>
      				<tr><td>Place </td><td><input type='text' name='place' id="place_<?= $location['instance']?>" value="<?=set_value('place',(isset($location['place'])? $location['place']: ''))?>" /></td></tr>
      				<tr><td>&nbsp;</td><td><input type="submit" value="Save" /></td></tr>
    				</table>
            </form>
          <?php endforeach;?>
          </fieldset>
      </div>

      <div id="institution">
        <p>
          <strong>TODO:</strong><br />
          This would be one or more institution name(s) selected from the institution table. There must also be the ability to add a new institution (although this is probably an executive not an editor function). Filtered lists must be the minimum interface (if something better than filters can be devised then that would be a big plus). Filters should be Rank, Order Name and Place. eg Abbey, Priory, ..., then if Abbatial, Order Name, OR Place - Country then Place. eg England, then York for example as selected form a filtered list.
        </p>
      </div>
  <?php endif; ?>
</div>

<script type="text/javascript" src="<?php echo base_url();?>js/charter_validate.js"></script>
<?php include_once 'footer.php';?>
