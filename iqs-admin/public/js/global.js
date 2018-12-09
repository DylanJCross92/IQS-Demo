$(function(){
	$(document).on("click", ".login-page .login-form input[type='submit'].login", function(e){
		e.preventDefault();
		
		var $serializedData = $(this).closest("form").serialize();
		
		$(".login-page").find(".loading-wrapper").remove();
		$(".login-page").prepend('<div class="loading-wrapper"><div class="text">Logging In...</div><div class="loader"></div></div>');
		
		var $loader = $(".login-page").find(".loading-wrapper");
		$loader.show();
		
		setTimeout(function(){
			$(".login-page .loading-wrapper .text").html("Setting up Dashboard...");
		}, 1500);
		
		$.ajax({
			url: "ajax/login/login.php",
			dataType: "json",
			type: "POST",
			data: $serializedData,
			success: function(json) {
				
				if(!json.error)
				{
					$(".login-page .loading-wrapper .text").html("Redirecting to Dashboard...");
					window.location = "settings";
				}
				else 
				{
					$loader.hide();
					$.each(json.error_messages, function(name, value){
						var $field = $(".signin-form [name='"+name+"']");
						
						$field.addClass("error");
						$field.before("<div class='form-error'>"+value+"</div>");
					});
				}
			}
		});
	});
});


$(function(){
	$('.product-switcher').change(function() {
		location.href = $(this).val();
	});
});

$(document).on("keypress keyup blur", ".number-validation", function(event) {    
   $(this).val($(this).val().replace(/[^\d].+/, ""));
	
	if((event.which < 48 || event.which > 57) && event.which !== 8) {
		event.preventDefault();
	}
});


$(function(){
	$("[name='zipcode_whitelisting']").change(function() {
		
		if(this.checked!=true)
		{
			$(".content .toggle-content").removeClass("enabled").addClass("disabled");
		}
		else
		{
			$(".content .toggle-content").addClass("enabled").removeClass("disabled");
		}
		
	});
});

$(function(){ 
	
	
	/* ON CLICK - Show ZipCode Whitelistin Popup */
	$(document).on("click", "[data-add-row='true'][data-table-name='zipcode-whitelisting']", function(){
		var $tableName = "zipcode-whitelisting";
		
		$.ajax({
			url: "ajax/view/popups/add-zip-whitelist.php",
			dataType: "html",
			type: "POST",
			success: function(html) {
				$(".overlay-wrapper .overlay-container").html(html);
				$(".overlay-wrapper").fadeIn("fast");
				
				$(document).on("click", ".cancel", function(e){
					$(this).closest(".overlay-wrapper").fadeOut("fast", function(){
						$(this).closest(".overlay-wrapper .overlay-container").empty();	
					});
				});
			}
		});
	});
	
	/* ZipCode Whitelist form submit */
	$(document).on("click", ".overlay-container [name='add_zipwhitelist'] input[type='submit'].submit", function(e){
		e.preventDefault();
		
		var $form = $(this).closest("form");
		
		var $serializedData = $form.serialize();
		var $loader = $(this).closest(".overlay-container").find(".loading-wrapper");
		
		$loader.show();
		
		$.ajax({
			url: "ajax/insert/insertZipWhitelist.php",
			dataType: "html",
			type: "POST",
			data: $serializedData,
			success: function(html) {
				
				$form.closest("div").find(".error").remove();
				
				if(html){
					$(".table.zipcode-whitelisting").find(".row:not(.header)").remove();
					$(".table.zipcode-whitelisting").find(".row.header").after(html);
					$(".overlay-wrapper").fadeOut("fast");
				}
				else
				{
					$form.before("<div class='error'>There was an error with your input</div>");
				}
				
				
				$loader.hide();
			}
		});
	});
	
	/* ON CLICK - Check if new row has data and save it */
	$(document).on("click", ".table > .row .delete", function(e){
		e.stopPropagation();
		
		var $table = $(this).closest(".table");
		var $row = $(this).closest(".row");
		var $whitelistId = $(this).data("row-id");
		
		if($whitelistId) {
			$row.addClass("confirm-delete");
		}
		else
		{
			alert("Error deleting row");	
		}
	});
	
	$(window).click(function() {
		
		var $rowDelete = $(".table > .row.confirm-delete");
		
		if($rowDelete.length > 0)
		{
			$rowDelete.removeClass("confirm-delete");	
		}
	});
	
	/* ON CLICK - Confirm Delete ZipCode WHitelist row */
	$(document).on("click", ".table.zipcode-whitelisting > .row.confirm-delete .delete", function(e){
		e.stopPropagation();
		
		var $table = $(this).closest(".table");
		var $row = $(this).closest(".row");
		var $whitelistId = $(this).data("row-id");
		
		if($whitelistId) {
			
			$table.find(".row-"+$whitelistId).remove();
			
			$.ajax({
				url: "ajax/delete/deleteZipWhitelist.php",
				dataType: "html",
				type: "POST",
				data: "whitelistId="+$whitelistId,
				success: function(html) {
					
				}
			});
		}
		else
		{
			alert("Error deleting row");	
		}
	});
	
	/* ON CLICK - Edit ZipCode Whitelist row */
	$(document).on("click", ".table.zipcode-whitelisting > .row [data-edit-row='true']", function(e){
		e.stopPropagation();
		
		var $table = $(this).closest(".table");
		var $row = $(this).closest(".row");
		var $whitelistId = $(this).data("row-id");
		
		if($whitelistId) {
			
			var $tableName = "zipcode-whitelisting";
		
			$.ajax({
				url: "ajax/view/popups/add-zip-whitelist.php",
				dataType: "html",
				type: "POST",
				data: {
						whitelistId: $whitelistId, 
						product: $row.data("product"), 
						zipcode: $row.data("zipcode")
				},
				success: function(html) {
					$(".overlay-wrapper .overlay-container").html(html);
					$(".overlay-wrapper").fadeIn("fast");
					
					$(document).on("click", ".cancel", function(e){
						$(this).closest(".overlay-wrapper").fadeOut("fast", function(){
							$(this).closest(".overlay-wrapper .overlay-container").empty();	
						});
					});
				}
			});
		}
	});
	
	$(document).on("click", ".import-csv-zip-whitelist", function(e){
		e.preventDefault();
		
		$("form[name='zip_whitelist_csv'] [name='csv']").trigger("click");
	});
	
	
	$(document).on("change", "form[name='zip_whitelist_csv'] [name='csv']", function(e){
		
		e.preventDefault();
		
		var form = $(this).closest("form")[0];
		var formData = new FormData(form);
		
		var $table = $(".table.zipcode-whitelisting");
		
		$(".csv-loader").addClass("show");
		
		$.ajax({
			url: "ajax/upload/zipWhitelistCSV.php",
			dataType: "html",
			/* Required for file uploads */
			contentType: false,
   			processData: false,
			/* *** */
			type: "POST",
			data: formData,
			success: function(html) {
				
				$table.find(".row:not(.header)").remove();
				$table.find(".row.header").after(html);
				$table.find(".processing").remove();
				
				$(".csv-loader").removeClass("show");
			}
		});
		
	});
	
	/* ON CLICK - Show Blockcodes Popup */
	$(document).on("click", "[data-add-row='true'][data-table-name='blockcodes']", function(){
		var $tableName = "blockcodes";
		
		$.ajax({
			url: "ajax/view/popups/add-blockcode.php",
			dataType: "html",
			type: "POST",
			success: function(html) {
				$(".overlay-wrapper .overlay-container").html(html);
				$(".overlay-wrapper").fadeIn("fast");
				
				$(document).on("click", ".cancel", function(e){
					$(this).closest(".overlay-wrapper").fadeOut("fast", function(){
						$(this).closest(".overlay-wrapper .overlay-container").empty();	
					});
				});
			}
		});
	});
	
	/* ON CLICK - Show Blockcodes edit Popup */
	$(document).on("click", ".table.blockcodes > .row [data-edit-row='true']", function(e){
		e.stopPropagation();
		
		var $table = $(this).closest(".table");
		var $row = $(this).closest(".row");
		var $blockcodeId = $row.data("blockcode");
		
		if($blockcodeId) {
		
			$.ajax({
				url: "ajax/view/popups/add-blockcode.php",
				dataType: "html",
				type: "POST",
				data: {
						blockcodeId: $blockcodeId, 
						blockcode: $row.data("blockcode"), 
						blockcodeText: $row.data("blockcode-text")
				},
				success: function(html) {
					$(".overlay-wrapper .overlay-container").html(html);
					$(".overlay-wrapper").fadeIn("fast");
					
					$(document).on("click", ".cancel", function(e){
						$(this).closest(".overlay-wrapper").fadeOut("fast", function(){
							$(this).closest(".overlay-wrapper .overlay-container").empty();	
						});
					});
				}
			});
		}
	});
	
	
	
	/* Blockcodes form submit */
	$(document).on("click", ".overlay-container [name='add_blockcode'] input[type='submit'].submit", function(e){
		e.preventDefault();
		
		var $form = $(this).closest("form");
		var $serializedData = $form.serialize();
		var $loader = $(this).closest(".overlay-container").find(".loading-wrapper");
		
		$loader.show();
		
		$.ajax({
			url: "ajax/insert/insertBlockCodes.php",
			dataType: "html",
			type: "POST",
			data: $serializedData,
			success: function(html) {
				
				if(html){
					$(".table.blockcodes").find(".row:not(.header)").remove();
					$(".table.blockcodes").find(".row.header").after(html);
					$(".overlay-wrapper").fadeOut("fast");
				}
				else
				{
					$form.before("<div class='error'>There was an error with your input</div>");
				}
				
				$loader.hide();
			}
		});
	});
	
	$(document).on("click", ".import-csv-blockcodes", function(e){
		e.preventDefault();
		$("form[name='blockcodes_csv'] [name='csv']").trigger("click");
	});
	
	
	$(document).on("change", "form[name='blockcodes_csv'] [name='csv']", function(e){
		
		e.preventDefault();
		
		var form = $(this).closest("form")[0];
		var formData = new FormData(form);
		
		var $table = $(".table.blockcodes");
		
		$(".csv-loader").addClass("show");
		
		$.ajax({
			url: "ajax/upload/blockCodesCSV.php",
			dataType: "html",
			/* Required for file uploads */
			contentType: false,
   			processData: false,
			/* *** */
			type: "POST",
			data: formData,
			success: function(html) {
				
				$table.find(".row:not(.header)").remove();
				$table.find(".row.header").after(html);
				
				$(".csv-loader").removeClass("show");
			}
		});
		
	});
	
	
	/* BlockCodes delete */
	$(document).on("click", ".table.blockcodes > .row.confirm-delete .delete", function(e){
		e.stopPropagation();
		
		var $table = $(this).closest(".table");
		var $row = $(this).closest(".row");
		var $blockCodeId = $(this).data("row-id");
		
		if($blockCodeId) {
			
			$table.find(".row-"+$blockCodeId).remove();
			
			$.ajax({
				url: "ajax/delete/deleteBlockCode.php",
				dataType: "html",
				type: "POST",
				data: "blockCodeId="+$blockCodeId,
				success: function(html) {
					
				}
			});
		}
		else
		{
			alert("Error deleting row");	
		}
	});
});

$(function(){
	
	$(document).on("click", ".refresh-system:not(.refreshing)", function(e){
		
		var $this = $(this);
		var $OriginalTxt = $this.text();
		
		$this.text("Refreshing...").addClass("refreshing");
		
		$.ajax({
			url: "ajax/refresh/system-checks.php",
			dataType: "html",
			type: "POST",
			success: function(html) {
				$(".system-checks").html(html);
				
				$this.text($OriginalTxt).removeClass("refreshing");
			}
		});
	});
});

/* 
	Settings/Configuration
*/
$(function(){
	/* ON CLICK - Check if new row has data and save it */
	$(document).on("click", ".configuration-content input[type='submit']", function(e){
		e.preventDefault();
		
		var $form = $(this).closest("form[name]");
		var $data = $form.serialize();
		
		$(this).closest("section").find(".loading-wrapper").remove();
		$(this).closest("section").prepend('<div class="loading-wrapper"><div class="loader">Loading...</div></div>');
		
		var $loader = $(this).closest("section").find(".loading-wrapper");
		
		$loader.show();
		
		$.ajax({
			url: "ajax/update/updateConfiguration.php",
			dataType: "html",
			type: "POST",
			data: $data,
			success: function(html) {
				$loader.hide().remove();
			}
		});
	});
	
	$(document).on("click", ".import-csv-configuration", function(e){
		e.preventDefault();
		$("form[name='configuration_csv'] [name='csv']").trigger("click");
	});
	
	
	$(document).on("change", "form[name='configuration_csv'] [name='csv']", function(e){
		
		e.preventDefault();
		
		var form = $(this).closest("form")[0];
		var formData = new FormData(form);
		
		$(".csv-loader").addClass("show");
		
		$.ajax({
			url: "ajax/upload/configurationCSV.php",
			dataType: "html",
			/* Required for file uploads */
			contentType: false,
   			processData: false,
			/* *** */
			type: "POST",
			data: formData,
			success: function(html) {
				
				$(".csv-loader").removeClass("show");
				window.location.reload();
			}
		});
		
	});
});

$(function(){

	/* Sort table */
	$(document).on("click", ".table .row.header [data-table-sort-by]", function(e){
		
		var $table = $(this).closest(".table");
		var $sortAsc = !$(this).attr("data-table-sort") || $(this).attr("data-table-sort") == "asc" ? true : false;
		var $sortBy = $(this).attr("data-table-sort-by");
		
		var rows = $table.find('li.row:not(.header)').get();
		
		rows.sort(function(a, b) {
			var keyA = $(a).attr("data-"+$sortBy);
			var keyB = $(b).attr("data-"+$sortBy);
			
			if ($sortAsc) {
				return (keyA > keyB) ? 1 : 0;
			} else {
				return (keyA < keyB) ? 1 : 0;
			}
		});
		
		$.each(rows.reverse(), function(index, row) {
			
			if($(".table > .row.new-row").length > 0)
			{
				$(".table > .row.new-row").after(row);
			}
			else
			{
				$(".table > .row.header").after(row);
			}
		});
		
		$sortAsc ? $(this).attr("data-table-sort", "desc") : $(this).attr("data-table-sort", "asc");
	});
});