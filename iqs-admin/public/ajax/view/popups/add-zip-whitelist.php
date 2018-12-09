<?php require "../../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}
?>
<?php 
	$whitelistId = isset($_POST["whitelistId"]) ? $_POST["whitelistId"] : false;
	$product = isset($_POST["product"]) ? $_POST["product"] : false;
	$zipcode = isset($_POST["zipcode"]) ? $_POST["zipcode"] : false;
	
	$edit = $whitelistId && $product && $zipcode;

	$products_en = Cache::get("getConfSectionValues_productsenabled");	
?>
<h1>Whitelist a Zipcode</h1>

<form name="add_zipwhitelist" action="" method="post">
	<?php if($edit){?><input type="hidden" name="whitelistId" value="<?php echo $whitelistId?>"><?php }?>
	<select name="state_product">
	<?php foreach($products_en as $product_arr):
		$state_en = substr($product_arr["key"], 0, 2);
		$product_en = substr($product_arr["key"], 2, 5);
		?>
        <option value="<?php echo $product_arr["key"]?>"<?php if($product_arr["key"] == $product){?> selected<?php }?>><?php echo strtoupper($state_en)." ".strtoupper($product_en);?></option>
    <?php endforeach;?>
    </select>
    <p>
    <input type="text" class="number-validation" maxlength="5" name="zipcode" placeholder="ZipCode" <?php if($zipcode){?>value="<?php echo $zipcode?>"<?php }?>/></p>
    <a class="button light-gray cancel">Cancel</a>
    <input type="submit" name="submit" class="button green submit" value="<?php if($edit){?>Update<?php } else{?>Add<?php }?>"/>
</form>

<div class="loading-wrapper">
	<div class="loader">Loading...</div>
</div>