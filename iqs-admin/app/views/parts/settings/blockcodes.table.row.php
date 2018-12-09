<li class="row row-<?php echo $blockCodesData->BlockCode?>" data-blockcode="<?php echo trim($blockCodesData->BlockCode);?>" data-blockcode-text="<?php echo base64_encode(trim($blockCodesData->BlockText));?>" data-datetime="<?php echo trim(strtotime($blockCodesData->DateTime));?>">
    <div class="cell blockcode"><?php echo $blockCodesData->BlockCode;?></div>
    <div class="cell blocktext" data-original-value="" data-edit-row="true"><?php echo trim($blockCodesData->BlockText);?></div>
    <div class="cell date" title="<?php echo date("F j, Y, g:i a", strtotime($blockCodesData->DateTime));?>"><?php echo date("F j, Y", strtotime($blockCodesData->DateTime));?></div>
    <div class="cell actions"><a class="button gray edit" data-edit-row="true" data-row-id="<?php echo $blockCodesData->BlockCode?>">Edit</a><a class="button red delete" data-delete-row="true" data-row-id="<?php echo $blockCodesData->BlockCode?>">Delete</a></div>
</li>