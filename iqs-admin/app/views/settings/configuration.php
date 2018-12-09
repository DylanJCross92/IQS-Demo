<?php
$Config = new Config;
?>
 <div class="configuration-content">
	<div class="multi-cols top-content">
    	<div class="col-1">
        	<h1>Configuration</h1>
        </div>
        <div class="col-2">
        	<div class="loading-wrapper csv-loader">
                <div class="loader"></div>
                <div class="loading-text">Importing...</div>
            </div>
            <a href="#" class="button green import-csv-configuration">Import</a>
            <a href="csv.php?template=configuration" target="_blank" class="button gray export-configuration">Export</a>
        </div>
    </div>


    <section>
        <h2>IQS</h2>
        <form name="env-configuration">
            <input type="hidden" name="conf_section" value="env"/>

            <div class="row multi-cols">
                <div class="col-1">
                    <label>IQS Debug Mode:</label>
                </div>
                <div class="col-2">
                    <select name="<?php echo $page_data->data->conf->env->debug->key;?>">
                        <?php
                        $db_logging_options = array(
                            array(
                                "label" => "Enabled",
                                "value" => "true"
                            ),
                            array(
                                "label" => "Disabled",
                                "value" => "false"
                            )
                        );

                        foreach($db_logging_options as $option) {
                            ?>
                            <option value="<?php echo $option["value"]?>" <?php echo $option["value"] == $page_data->data->conf->env->debug->value ? "selected" : "";?>><?php echo $option["label"];?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <input type="submit" class="button green submit" value="Save Changes"/>
        </form>
    </section>

    <section>
        <h2>Logging</h2>
        <form name="logging-configuration">
        	<input type="hidden" name="conf_section" value="logging"/>
            
            <div class="row multi-cols">
                <div class="col-1">
                    <label>Database Logging:</label>
                </div>
                <div class="col-2">
                    <select name="<?php echo $page_data->data->conf->logging->databaselogging->key;?>">
                    	<?php 
							$db_logging_options = array(
								array(
									"label" => "Enabled",
									"value" => "true"
								),
								array(
									"label" => "Disabled",
									"value" => "false"
								)
							);
							
							foreach($db_logging_options as $option) {
								?>
                                <option value="<?php echo $option["value"]?>" <?php echo $option["value"] == $page_data->data->conf->logging->databaselogging->value ? "selected" : "";?>><?php echo $option["label"];?></option>
                                <?php	
							}
						?>
                    </select>
                </div>
            </div>
            
            <div class="row multi-cols">
                <div class="col-1">
                    <label>File Logging:</label>
                </div>
                <div class="col-2">
                    <select name="<?php echo $page_data->data->conf->logging->filelogging->key;?>">
                    	<?php 
							$db_logging_options = array(
								array(
									"label" => "Enabled",
									"value" => "true"
								),
								array(
									"label" => "Disabled",
									"value" => "false"
								)
							);
							
							foreach($db_logging_options as $option) {
								?>
                                <option value="<?php echo $option["value"]?>" <?php echo $option["value"] == $page_data->data->conf->logging->filelogging->value ? "selected" : "";?>><?php echo $option["label"];?></option>
                                <?php	
							}
						?>
                    </select>
                </div>
            </div>
            
            <div class="row multi-cols">
                <div class="col-1">
                    <label>Logging Path:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->logging->logfilepath->key;?>" value="<?php echo $page_data->data->conf->logging->logfilepath->value;?>">
                </div>
            </div>
            
            <input type="submit" class="button green submit" value="Save Changes"/>
        </form>
    </section>
    
    <section>
    	<h2>EZQuote API</h2>
        <form name="ezquoteapi-configuration">
        	<input type="hidden" name="conf_section" value="ezquoteapi"/>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>Base Url:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->ezquoteapi->baseurl->key;?>" value="<?php echo $page_data->data->conf->ezquoteapi->baseurl->value;?>">
                </div>
            </div>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>API Uid:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->ezquoteapi->apiuid->key;?>" value="<?php echo $page_data->data->conf->ezquoteapi->apiuid->value;?>">
                </div>
            </div>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>API Password:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->ezquoteapi->apipw->key;?>" value="<?php echo $page_data->data->conf->ezquoteapi->apipw->value;?>">
                </div>
            </div>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>API Alc:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->ezquoteapi->apialc->key;?>" value="<?php echo $page_data->data->conf->ezquoteapi->apialc->value;?>">
                </div>
            </div>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>Debug API Uid:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->ezquoteapi->debugapiuid->key;?>" value="<?php echo $page_data->data->conf->ezquoteapi->debugapiuid->value;?>">
                </div>
            </div>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>Debug API Password:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->ezquoteapi->debugapipw->key;?>" value="<?php echo $page_data->data->conf->ezquoteapi->debugapipw->value;?>">
                </div>
            </div>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>Debug API Alc:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="<?php echo $page_data->data->conf->ezquoteapi->debugapialc->key;?>" value="<?php echo $page_data->data->conf->ezquoteapi->debugapialc->value;?>">
                </div>
            </div>
            <h3 style="font-weight: normal;">Credential Override</h3>

            <div class="row multi-cols">
                <div class="col-1">
                    <label>API Uid - ezaddresses:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="methodovrduid_ezaddresses" value="<?php echo $page_data->data->conf->ezquoteapi->methodovrduid_ezaddresses->value;?>">
                </div>
            </div>
            <div class="row multi-cols">
                <div class="col-1">
                    <label>API Password - ezaddresses:</label>
                </div>
                <div class="col-2">
                    <input type="text" name="methodovrdpw_ezaddresses" value="<?php echo $page_data->data->conf->ezquoteapi->methodovrdpw_ezaddresses->value;?>">
                </div>
            </div>
            <input type="submit" class="button green" value="Save Changes"/>
        </form>
    </section>
    
    <section>
    	<h2>States</h2>
    	<form name="states-configuration">
        	<input type="hidden" name="conf_section" value="statesenabled"/>
            <?php
                foreach ($page_data->data->conf->statesenabled as $key => $value):
             ?>
                <div class="row multi-cols">
                    <div class="col-1">
                        <label><?php echo $Config->state_from_abbrev($value->key);?>:</label>
                    </div>
                    <div class="col-2">
                        <select name="<?php echo $value->key;?>">
                            <?php
                            $states_options = array(
                                array(
                                    "label" => "Enabled",
                                    "value" => "true"
                                ),
                                array(
                                    "label" => "Disabled",
                                    "value" => "false"
                                )
                            );

                            foreach($states_options as $option) {
                                ?>
                                <option value="<?php echo $option["value"]?>" <?php echo $option["value"] == $value->value ? "selected" : "";?>><?php echo $option["label"];?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
            <input type="submit" class="button green submit" value="Save Changes"/>
        </form>
    
    </section>
    
    <section>
    	<h2>Products</h2>
    	<form name="states-configuration">
        	<input type="hidden" name="conf_section" value="productsenabled"/>

            <?php
            foreach ($page_data->data->conf->productsenabled as $key => $value):
                ?>
                <div class="row multi-cols">
                    <div class="col-1">
                        <label>
                            <?php
                            $state_abbr = substr($value->key, 0,2);
                            $product_abbr = substr($value->key, 2, 10);
                            echo $Config->state_from_abbrev($state_abbr)." ".strtoupper($product_abbr);?>:</label>
                    </div>
                    <div class="col-2">
                        <select name="<?php echo $value->key;?>">
                            <?php
                            $states_options = array(
                                array(
                                    "label" => "Enabled",
                                    "value" => "true"
                                ),
                                array(
                                    "label" => "Disabled",
                                    "value" => "false"
                                )
                            );

                            foreach($states_options as $option) {
                                ?>
                                <option value="<?php echo $option["value"]?>" <?php echo $option["value"] == $value->value ? "selected" : "";?>><?php echo $option["label"];?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
            <input type="submit" class="button green submit" value="Save Changes"/>
        </form>
    
    </section>
    
    <section>
    	<h2>Zipcode Whitelisting</h2>
        <form name="states-configuration">
        	<input type="hidden" name="conf_section" value="whitelistenabled"/>

            <?php
            foreach ($page_data->data->conf->whitelistenabled as $key => $value):
                ?>
                <div class="row multi-cols">
                    <div class="col-1">
                        <label>
                            <?php
                            $state_abbr = substr($value->key, 0,2);
                            $product_abbr = substr($value->key, 2, 10);
                            echo $Config->state_from_abbrev($state_abbr)." ".strtoupper($product_abbr);?>:</label>
                    </div>
                    <div class="col-2">
                        <select name="<?php echo $value->key;?>">
                            <?php
                            $states_options = array(
                                array(
                                    "label" => "Enabled",
                                    "value" => "true"
                                ),
                                array(
                                    "label" => "Disabled",
                                    "value" => "false"
                                )
                            );

                            foreach($states_options as $option) {
                                ?>
                                <option value="<?php echo $option["value"]?>" <?php echo $option["value"] == $value->value ? "selected" : "";?>><?php echo $option["label"];?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
            <input type="submit" class="button green submit" value="Save Changes"/>
        </form>
    
    </section>

</div>

<form style="display: none;" action="" method="post" enctype="multipart/form-data" name="configuration_csv" id="form1">
  Choose your file: <br />
  <input name="csv" type="file" id="csv" />
  <input type="submit" name="Submit" value="Submit" />
</form> 