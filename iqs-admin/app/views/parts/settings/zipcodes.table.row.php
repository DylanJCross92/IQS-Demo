<li class="row row-<?php echo $zipData->WhiteListId?>" data-product="<?php echo strtolower(trim($zipData->State));?><?php echo strtolower(trim($zipData->Product));?>" data-zipcode="<?php echo trim($zipData->ZipCode);?>" data-datetime="<?php echo trim(strtotime($zipData->DateTime));?>">
    <div class="cell number"></div>
    <div class="cell product"><?php echo strtoupper(trim($zipData->State));?> <?php echo strtoupper($zipData->Product);?></div>
    <div class="cell zipcode" data-original-value="<?php echo trim($zipData->ZipCode);?>" data-edit-row="true" data-row-id="<?php echo $zipData->WhiteListId?>"><?php echo trim($zipData->ZipCode);?></div>
    <div class="cell date" title="<?php echo date("F j, Y, g:i a", strtotime($zipData->DateTime));?>"><?php echo date("F j, Y", strtotime($zipData->DateTime));?></div>
    <div class="cell actions"><a class="button gray edit" data-edit-row="true" data-row-id="<?php echo $zipData->WhiteListId?>">Edit</a><a class="delete button red" data-delete-row="true" data-row-id="<?php echo $zipData->WhiteListId?>">Delete</a></div>
</li>