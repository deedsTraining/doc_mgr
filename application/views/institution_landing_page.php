<?php include_once 'header.php';?>

<article class="module width_full">
    <header>
	 <h3 class="tabs_involved">Institutions &nbsp; &nbsp;[<a href="<?=base_url();?>index.php/institution/edit">Add new record</a>]</h3>
	</header>
	<table class="tablesorter" cellspacing="0">
	<thead>
		<tr>
		  <th>Actions</th>
		  <th>Institution Name</th>
		</tr>
	</thead>
	<tbody>
	   <?php foreach ($institution_list as $institution) { ?>
          <tr>
            <td><a href="<?= site_url('institution/edit/'.$institution->instid)?>"><img src="<?php echo base_url();?>images/icn_edit.png" title="Edit"></a></td>
			<td><?php echo $institution->inst_name; ?></td>
		</tr>
        <?php } ?>
	</tbody>
	</table>

</article><!-- end of content manager article -->