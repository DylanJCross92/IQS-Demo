<div class="product-dropdown" style="display: none;">
	
</div>

<div class="header-wrapper">
	<div class="multi-cols">
    	<div class="col-1">
			<h1>Zipcode Whitelisting</h1>
        </div>
        <div class="col-2">
            <!--<div class="switch" data-tooltip="Toggle Zipcode Whitelisting">
              	<input name="zipcode_whitelisting" id="cmn-toggle-1" class="cmn-toggle cmn-toggle-round" type="checkbox" checked>
              	<label for="cmn-toggle-1" data-on="on" data-off="off"></label>
            </div>-->
    	</div>
	</div>
</div>

<div class="toggle-content enabled">
	
    <div class="content-disabled">
    	<h1>Zipcode Whitelisting is currently disabled</h1>
    </div>
	
    <div class="content-enabled">
        <div class="table-actions">
            <div class="multi-cols">
                <div class="col-1">
                    <a class="button light-blue" data-add-row="true" data-table-name="zipcode-whitelisting">Add</a>
                </div>
                <div class="col-2">
                	<div class="loading-wrapper csv-loader">
                        <div class="loader"></div>
                        <div class="loading-text">Importing...</div>
                    </div>
                    <a href="#" class="button green import-csv-zip-whitelist">Import</a>
                    <a href="csv.php?template=zipcodes" target="_blank" class="button gray export-csv-zip-whitelist">Export</a>
                </div>
            </div>
        </div>
        
        <ul class="table zipcode-whitelisting" data-table-sort="asc">
            <li class="row header">
              <div class="cell">
                #
              </div>
              <div class="cell product" data-table-sort-by="product">
              	Product
              </div>
              <div class="cell zipcode" data-table-sort-by="zipcode">
                Zipcode
              </div>
              <div class="cell datetime" data-table-sort-by="datetime">
                Date Added
              </div>
              <div class="cell actions">
                Actions
              </div>
            </li>
            
            <?php
			 foreach($page_data->data->zipcodes_list as $zipData):?> 
            	<?php require ROOT."/app/views/parts/settings/zipcodes.table.row.php";?>
			<?php endforeach; ?>
        </ul>
    </div>
</div>

<form style="display: none;" action="" method="post" enctype="multipart/form-data" name="zip_whitelist_csv" id="form1">
  Choose your file: <br />
  <input name="csv" type="file" id="csv" />
  <input type="submit" name="Submit" value="Submit" />
</form> 
