<?php include_once 'header.php';?>

<article class="module width_full">
    <header>
	 <h3 class="tabs_involved">Cartularies &nbsp; &nbsp;[<a href="<?=base_url();?>index.php/cartulary/edit">Add new record</a>]</h3>
	</header>
	<table class="tablesorter" cellspacing="0">
	<thead>
		<tr>
		  <th>Actions</th>
		  <th>Cartnum</th>
		  <th>Cartulary Title</th>
		  <th>Series</th>
		  <th>Multi</th>
		  <th>Public?</th>
		  <th>Diplomatics</th>
		  <th>Names</th>
		</tr>
	</thead>
	<tbody>
	   <?php foreach ($cartulary_list as $cartulary) { ?>
          <tr>
            <td><a href="<?= site_url('cartulary/edit/'.$cartulary->cartnum)?>"><img src="<?php echo base_url();?>images/icn_edit.png" title="Edit"></a></td>
            <td><?= $cartulary->cartnum ?></td>
			<td><?php echo $cartulary->title; ?></td>
			<td><?php echo $cartulary->series;?></td>
			<td><?php echo $cartulary->multi?></td>
			<td><?php echo $cartulary->private;?></td>
			<td><?php echo $cartulary->diplomatics;?></td>
			<td><?php echo $cartulary->names;?></td>
		</tr>
        <?php } ?>
	</tbody>
	</table>

</article><!-- end of content manager article -->