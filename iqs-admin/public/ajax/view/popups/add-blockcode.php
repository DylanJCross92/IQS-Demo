<?php require "../../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}
?>
<?php 
	$blockcodeId = isset($_POST["blockcodeId"]) ? $_POST["blockcodeId"] : false;
	$blockcode = isset($_POST["blockcode"]) ? $_POST["blockcode"] : false;
	$blockcode_text = isset($_POST["blockcodeText"]) ? base64_decode($_POST["blockcodeText"]) : false;
	
	$edit = $blockcodeId && $blockcode && $blockcode_text;
?>
<h1><?php if($edit){?>Update<?php } else{?>Add<?php }?> a Blockcode</h1>

<form name="add_blockcode" action="" method="post">
	<?php if($edit){?><input type="hidden" name="blockcodeId" value="<?php echo $blockcodeId?>"><?php }?>
    <input type="text" name="code" class="number-validation" placeholder="Blockcode" <?php if($edit){?>value="<?php echo $blockcode?>"<?php }?>/><p>
    <textarea name="blockcode_text" placeholder="Blockcode Text" class="full-width"><?php if($edit){ echo $blockcode_text;}?></textarea></p>
    <a class="button light-gray cancel">Cancel</a>
    <input type="submit" name="submit" class="button green submit" value="<?php if($edit){?>Update<?php } else{?>Add<?php }?>"/>
</form>

<div class="loading-wrapper">
	<div class="loader">Loading...</div>
</div>