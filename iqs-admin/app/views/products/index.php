<?php 
	//var_dump($page_data);
	
	$product = $page_data->data->product;
	
	if($product)
	{
		echo $product;	
	}
?>
<div class="content">
    <div class="page-header">
        <h1>Products</h1>
    </div>
    
    <ul class="products-list">
        <li>
            <div class="heading">Alaska</div>
            <div class="sub-products">
                <a href="products/akh03">HO3</a>
            </div>
        </li>
        <li>
            <div class="heading">Virginia</div>
            <div class="sub-products">
                <a href="products/vah03">HO3</a>
            </div>
        </li>
        <li>
            <div class="heading">New York</div>
            <div class="sub-products">
                <a href="products/nyh03">HO3</a>
                <a href="products/nydp3">DP3</a>
            </div>
        </li>
        <li>
            <div class="heading">Texas</div>
            <div class="sub-products">
                <a href="products/txh03">HO3</a>
            </div>
        </li>
        <li>
            <div class="heading">South Carolina</div>
            <div class="sub-products">
                <a href="products/sch03">HO3</a>
                <a href="products/scdp3">DP3</a>
            </div>
        </li>
    </ul>
</div>