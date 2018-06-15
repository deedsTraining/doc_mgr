<?php include_once 'header.php';?>

<article class="module width_full">
    <header>
	 <h3 class="tabs_involved">
	   Charters &nbsp; &nbsp;[<a href="<?=base_url();?>index.php/charter/edit">Add new record</a>]
	 </h3>
	</header>

	<div class="submit_link">
  	 <label style="font-weight:bold">Show records starts with DOCNUM: </label><input type="text" id="starts-with" value="<?php echo isset($starts_with) ? $starts_with:'0001';?>"/>
  	 <input type="button" value="Go" onclick="window.location = '<?php echo site_url('charter/list_records');?>/'+jQuery('#starts-with').val();"/>
	</div>

	<table class="tablesorter module_content" cellspacing="0">
	<thead>
		<tr>
		  <th>Actions</th>
		  <th>Docnum</th>
		  <th>Language</th>
		  <th>Origin</th>
		  <th>Charter Type</th>
		  <th>Source</th>
		  <th>Status</th>
		</tr>
	</thead>
	<tbody>
	   <?php foreach ($charter_list as $charter) { ?>
          <tr>
            <td><a href="<?= site_url('charter/edit/'.$charter->docnum)?>"><img src="<?php echo base_url();?>images/icn_edit.png" title="Edit"></a></td>
            <td><?= $charter->docnum ?></td>
			<td><?php echo $charter->language; ?></td>
			<td><?php echo $charter->origin;?></td>
			<td><?php echo $charter->charter_type?></td>
			<td><?php echo $charter->charter_source;?></td>
			<td><?php echo $charter->charter_status;?></td>
		</tr>
        <?php } ?>
	</tbody>
	</table>

</article><!-- end of content manager article -->