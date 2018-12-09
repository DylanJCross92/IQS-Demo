<?php require "../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

$Iqs = new Iqs;

$SystemData = $Iqs->getSystemData();

Cache::set("getSystemData", $SystemData);
$SystemData = $SystemData["responseDataPayload"]["System"];

$iqsVersion = $SystemData["version"] ?: false;
$ezQuoteVersion = preg_match('#[\d]#',$SystemData["ezqversion"]) ?: false;
?>

<li class="col-25-percent giq">
    <div class="inner-wrapper">
        <div class="heading">IQS</div>
        <div class="status-wrapper">
            <?php if($iqsVersion){?>
                <div class="status ok"><strong>Status:</strong> Ok</div>
            <?php } else {?>
                <div class="status offline"><strong>Status:</strong> Offline</div>
            <?php }?>
            <div class="meta"><?php echo $iqsVersion;?></div>
        </div>
    </div>
</li>
<li class="col-25-percent ezquote">
    <div class="inner-wrapper">
        <div class="heading">EZQuote</div>
        <div class="status-wrapper">
            <?php if($ezQuoteVersion){?>
                <div class="status ok"><strong>Status:</strong> Ok</div>
            <?php } else {?>
                <div class="status offline"><strong>Status:</strong> Offline</div>
            <?php }?>
            <div class="meta"><?php echo $SystemData["ezqversion"];?></div>
        </div>
    </div>
</li>
<li class="col-25-percent admin">
    <div class="inner-wrapper">
        <div class="heading">Admin</div>
        <div class="status-wrapper">
            <div class="status"><strong>Status:</strong> Ok</div>
            <div class="meta">1.0.0</div>
        </div>
    </div>
</li>