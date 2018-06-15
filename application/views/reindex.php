<?php include_once 'header.php';?>

<article class="module width_full">
  <header>
    <h3>Reindex</h3>
  </header>

  <div class="reindex-body">
	
	<?php if(isset($msg) && !empty($msg)) { echo "<div class=\"msg\">$msg</div>"; }?>	

	<?php echo form_open_multipart('reindex/run_reindex');?>
		<label for="userfile">Choose a .txt file and click the upload button</label>
		<input type="file" name="userfile" size="20" />
	<?php 
		if (isset($upload_data) && !empty($upload_data)) {
			$full_path = $upload_data['full_path'];
			$client_name = $upload_data['client_name'];

			echo "<input type=\"text\" name=\"file_name\" readonly value=\"".$client_name." has been uploaded\" size=\"30\" />";			
			echo "<input type=\"text\" name=\"file_path\" hidden value=\"".$full_path."\" />";			
		}
	?>
		<input type="submit" name="upload" value="Upload" />
		<input type="submit" name="reindex" value="Re-index" id="reindex" />
	</form>
	
	<?php if(isset($result)) {

		// TODO: Using Ajax, update the result section to display the status message when a record is reindexed. 
		echo "<div class=\"result\">
			<strong>Reindex finished! Please review below result.</strong>
			<br />".$result."</div>"; 
	}?>
	  	
  </div>
  <div class="reindex-instructions">
  	<h1 class="instructions-toggle">>> Click to View Instructions</h1>
  	<div class="instructions-toggle-body">
	  	<ol>
	  		<li>
	  		<p>Create a .txt file that includes a list of docnumbers or cartnums to reindex.</p>
	  		<p>In the .txt file, add a docnumber or a cartnum per line. Maximum 1000 records (or rows) are allowed in the .txt file. A 8-digit number is recognized as Charter and a 4-digit number as Cartulary. Inputs that don't satisfy either of the patterns are recognized as invalid inputs. If there are any changes in the docnumber or cartnum format, please contact UTL ITS at <a href="deeds@library.utoronto.ca">deeds@library.utoronto.ca</a>.</p> 
	  		<p>Text file example: list.txt</p>
<xmp>0001
0002
0003
00010068
00010069
00010072
00010079  			
</xmp>
	  		</li>
	  		<li>Click the 'Choose File' button and choose the .txt file.</li>
	  		<li>Click the 'Upload' button.</li>
	  		<li>Click the 'Re-index' button. It will start re-indexing. Re-indexing a massive number of records will take a while. Please do not leave the page until the results will be displayed below the 'Re-index' button.</li>
	  	</ol>
	</div>
  </div>

<script type="text/javascript">
$(document).ready(function(){
    $(".instructions-toggle").click(function(){
        $(".instructions-toggle-body").slideToggle("slow");
    });
});
</script>
<?php include_once 'footer.php';?>
</article>
