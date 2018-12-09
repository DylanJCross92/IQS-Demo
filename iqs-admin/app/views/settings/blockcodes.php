<div class="header-wrapper">
	<div class="multi-cols">
    	<div class="col-1">
			<h1>Blockcodes</h1>
        </div>
        <div class="col-2">
           
    	</div>
	</div>
</div>

<div class="table-actions">
    <div class="multi-cols">
        <div class="col-1">
            <a class="button light-blue" data-add-row="true" data-table-name="blockcodes">Add</a>
        </div>
        <div class="col-2">
        	<div class="loading-wrapper csv-loader">
                <div class="loader"></div>
                <div class="loading-text">Importing...</div>
            </div>
            <a href="#" class="button green import-csv-blockcodes">Import</a>
            <a href="csv.php?template=blockcodes" target="_blank" class="button gray export-csv-blockcodes">Export</a>
        </div>
    </div>
</div>

<ul class="table blockcodes" data-table-sort="asc">
    <li class="row header">
      <div class="cell blockcode" data-table-sort-by="blockcode">
        Code
      </div>
      <div class="cell message">
        Message
      </div>
      <div class="cell datetime" data-table-sort-by="datetime">
        Date Added
      </div>
      <div class="cell actions">
        Actions
      </div>
    </li>
    
     <?php
	 foreach($page_data->data->blockCodes as $blockCodesData):?> 
		<?php require ROOT."/app/views/parts/settings/blockcodes.table.row.php";?>
	<?php endforeach; ?>
</ul>


<form style="display: none;" action="" method="post" enctype="multipart/form-data" name="blockcodes_csv" id="form1">
  Choose your file: <br />
  <input name="csv" type="file" id="csv" />
  <input type="submit" name="Submit" value="Submit" />
</form>