<?php
    echo $header;

    if (!empty($column_left))
    {
        echo $column_left;
    }
 ?>
<?php
    function oa_selected($expected, $actual){
        if($expected == $actual) echo 'selected';
    }
?>

<div id="content" class="oasso-container">
    <form id="form-layout" action="<?php echo $action; ?>" method="post">
        <div class="page-header">
            <div class="container-fluid">
                <div class="pull-right">
                    <button type="submit" form="form-account" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                            class="btn btn-primary">
                        <i class="fa fa-save"></i>
                    </button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                       class="btn btn-default"><i class="fa fa-reply"></i></a>
                </div>
                <h1>
                    <?php echo $heading_title; ?>
                </h1>
                <ul class="breadcrumb">
                    <?php
                            foreach ($breadcrumbs as $breadcrumb)
                            {
                                ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                    <?php
                            }
                        ?>
                </ul>
            </div>
        </div>
        <div class="container-fluid">
                <?php

                    // Success
                    if ( ! empty ($oasso_success_message))
                    {
                        ?>
            <div class="alert alert-success">
                <i class="fa fa-cogs"></i> <?php echo $oasso_success_message; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
                        <?php
                    }
                    // Error
                    elseif ( ! empty ($oasso_error_message))
                    {
                        ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i><?php echo $oasso_error_message; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
                        <?php
                    }
                    // Info
                     elseif ( ! empty ($oasso_intro_message))
                    {
                        ?>
            <p class="alert alert-info">
                <?php echo $oasso_intro_message ?>
            </p>
                        <?php
                    }
                ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-plug"></i> <?php echo $oasso_api_connection_handle ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="well">
                        <div class="row">
                            <div class="col-lg-12">
                                <p><?php echo $oasso_port_field_details ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label"
                                       for="oasso_handler"><?php echo $oasso_connection_handler_label ?></label>
                                <select name="oasso_handler" id="oasso_handler" class="form-control">
                                    <option value="curl"
                                    <?php if ($oasso_handler <> 'fsockopen') {echo ' selected="selected"';} ?>
                                    ><?php echo $oasso_connection_handled_option_curl ?></option>
                                    <option value="fsockopen"
                                    <?php if($oasso_handler == 'fsockopen') {echo ' selected="selected"';} ?>
                                    ><?php echo $oasso_connection_handled_option_fsockopen ?></option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label"
                                       for="oasso_port"><?php echo $oasso_port_field_label ?></label>
                                <select name="oasso_port" id="oasso_port" class="form-control">
                                    <option value="443"
                                    <?php if ($oasso_port <> 80) {echo ' selected="selected"';}
                                    ?>><?php echo $oasso_port_field_443_label; ?></option>
                                    <option value="80"
                                    <?php if ($oasso_port == 80) {echo ' selected="selected"';} ?>
                                    ><?php echo $oasso_port_field_80_label; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-lg-2">
                                <input id="oasso_connection_autodetect" name="oasso_connection_autodetect"
                                       value="<?php echo $oasso_autodetect_api_connection; ?>" type="button"
                                       class="btn btn-block btn-lg btn-info"/>
                            </div>
                            <div class="col-md-8 col-lg-10">
                                <div id="oasso_connection_autodetect_result"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-cog"></i> <?php echo $oasso_api_settings_title; ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="well">
                        <div class="row">
                            <div class="col-lg-12">
                                <p><?php echo $oasso_api_settings_intro; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="control-label" for="oasso_api_subdomain">
                                    <?php echo $oasso_api_subdomain_label; ?>
                                </label>
                                <input name="oasso_api_subdomain" value="<?php echo $oasso_api_subdomain ?>"
                                       id="oasso_api_subdomain" class="form-control" type="text"/>
                            </div>
                            <div class="col-sm-4">
                                <label class="control-label" for="oasso_public_key">
                                    <?php echo $oasso_api_public_key; ?>
                                </label>
                                <input name="oasso_public_key" value="<?php echo $oasso_public_key ?>"
                                       id="oasso_public_key" class="form-control" type="text"/>
                            </div>
                            <div class="col-sm-4">
                                <label class="control-label" for="oasso_private_key">
                                    <?php echo $oasso_api_private_key; ?>
                                </label>
                                <input name="oasso_private_key" value="<?php echo $oasso_private_key ?>"
                                       id="oasso_private_key" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-4 ">
                                <input id="oasso_connection_verify" type="button" class="btn btn-block btn-lg btn-info"
                                       value="<?php echo $oasso_verify_api_settings ?>" name="oasso_connection_verify"/>
                            </div>
                            <div class="col-lg-10 col-md-4 ">
                                <div id="oasso_connection_verify_result"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-user"></i> <?php echo $oasso_account_creation_title; ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="well">
                        <div class="row">
                            <p><?php echo $oasso_account_creation_intro ?></p>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label" for="oasso_accounts_create_auto">
                                    <?php echo $oasso_account_creation_auto_label ?>
                                </label>
                                <select id="oasso_accounts_create_auto" name="oasso_accounts_create_auto"
                                        class="form-control">
                                    <option
                                    <?php if(1 == $oasso_accounts_create_auto) echo 'selected '; ?> value="1">
                                    <?php echo $oasso_account_creation_auto_option_yes ?>
                                    </option>
                                    <option
                                    <?php if(0 == $oasso_accounts_create_auto) echo 'selected '; ?> value="0">
                                    <?php echo $oasso_account_creation_auto_option_no ?>
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label" for="oasso_accounts_create_sendmail">
                                    <?php echo $oasso_account_creation_mail_label ?>
                                </label>
                                <select id="oasso_accounts_create_sendmail" name="oasso_accounts_create_sendmail"
                                        class="form-control">
                                    <option
                                    <?php if(1 == $oasso_accounts_create_sendmail) echo 'selected '; ?> value="1">
                                    <?php echo $oasso_account_creation_mail_label_yes ?>
                                    </option>
                                    <option
                                    <?php if(0 == $oasso_accounts_create_sendmail) echo 'selected '; ?> value="0">
                                    <?php echo $oasso_account_creation_mail_label_no ?>
                                    </option>
                                </select>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-users"></i> <?php echo $oasso_account_link_title; ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="well">
                        <div class="row">
                            <p><?php echo $oasso_account_link_intro1; ?></p>
                            <p><?php echo $oasso_account_link_intro2; ?></p>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label" for="oasso_accounts_link_automatic">
                                    <?php echo $oasso_account_link_label ?>
                                </label>
                                <select id="oasso_accounts_link_automatic" name="oasso_accounts_link_automatic"
                                        class="form-control">
                                    <option
                                    <?php if(1 == $oasso_accounts_link_automatic) echo 'selected '; ?> value="1">
                                    <?php echo $oasso_account_link_yes ?>
                                    </option>
                                    <option
                                    <?php if(0 == $oasso_accounts_link_automatic) echo 'selected '; ?> value="0">
                                    <?php echo $oasso_account_link_no ?>
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label" for="oasso_accounts_link_unverified">
                                    <?php echo $oasso_account_link_unverified_label ?>
                                </label>
                                <select id="oasso_accounts_link_unverified" name="oasso_accounts_link_unverified"
                                        class="form-control">
                                    <option value="1"
                                    <?php if(1 == $oasso_accounts_link_unverified) echo 'selected '; ?>>
                                    <?php echo $oasso_account_link_unverified_yes ?>
                                    </option>
                                    <option value="0"
                                    <?php if(0 == $oasso_accounts_link_unverified) echo 'selected '; ?>>
                                    <?php echo $oasso_account_link_unverified_no ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php echo $oasso_account_link_unverified_help ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-cog"></i> <?php echo $oasso_session_title; ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="well">
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <label class="control-label" for="oasso_session_lifetime">
                                    <?php echo $oasso_session_lifetime_label ?>
                                </label>
                                <select id="oasso_session_lifetime" name="oasso_session_lifetime" class="form-control">
                                    <option
                                    <?php if(7200 == $oasso_session_lifetime) echo 'selected '; ?>value="7200">
                                    <?php echo $oasso_session_lifetime_2_Hours; ?>
                                    </option>
                                    <option
                                    <?php if(14400 == $oasso_session_lifetime) echo 'selected '; ?> value="14400">
                                    <?php echo $oasso_session_lifetime_4_Hours; ?>
                                    </option>
                                    <option
                                    <?php if(21600 == $oasso_session_lifetime) echo 'selected '; ?> value="21600">
                                    <?php echo $oasso_session_lifetime_6_Hours; ?>
                                    </option>
                                    <option
                                    <?php if(43200 == $oasso_session_lifetime) echo 'selected '; ?> value="43200">
                                    <?php echo $oasso_session_lifetime_12_Hours; ?>
                                    </option>
                                    <option
                                    <?php if(86400 == $oasso_session_lifetime) echo 'selected '; ?> value="86400">
                                    <?php echo $oasso_session_lifetime_1_Day; ?>
                                    </option>
                                    <option
                                    <?php if(172800 ==  $oasso_session_lifetime) echo 'selected '; ?> value="172800">
                                    <?php echo $oasso_session_lifetime_2_Days; ?>
                                    </option>
                                    <option
                                    <?php if(259200 ==  $oasso_session_lifetime) echo 'selected '; ?> value="259200">
                                    <?php echo $oasso_session_lifetime_3_Days; ?>
                                    </option>
                                    <option
                                    <?php if(345600 ==  $oasso_session_lifetime) echo 'selected '; ?> value="345600">
                                    <?php echo $oasso_session_lifetime_4_Days; ?>
                                    </option>
                                    <option
                                    <?php if(432000 ==  $oasso_session_lifetime) echo 'selected '; ?> value="432000">
                                    <?php echo $oasso_session_lifetime_5_Days; ?>
                                    </option>
                                    <option
                                    <?php if(518400 ==  $oasso_session_lifetime) echo 'selected '; ?> value="518400">
                                    <?php echo $oasso_session_lifetime_6_Days; ?>
                                    </option>
                                    <option
                                    <?php if(604800 ==  $oasso_session_lifetime) echo 'selected '; ?> value="604800">
                                    <?php echo $oasso_session_lifetime_1_Week; ?>
                                    </option>
                                    <option
                                    <?php if(1209600 == $oasso_session_lifetime) echo 'selected '; ?> value="1209600">
                                    <?php echo $oasso_session_lifetime_2_Weeks; ?>
                                    </option>
                                    <option
                                    <?php if(1814400 == $oasso_session_lifetime) echo 'selected '; ?> value="1814400">
                                    <?php echo $oasso_session_lifetime_3_Weeks; ?>
                                    </option>
                                    <option
                                    <?php if(2419200 == $oasso_session_lifetime) echo 'selected '; ?> value="2419200">
                                    <?php echo $oasso_session_lifetime_1_Month; ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php echo $oasso_session_lifetime_help; ?></p>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label class="control-label" for="oasso_session_realm">
                                    <?php echo $oasso_session_realm_label; ?>
                                </label>
                                <input name="oasso_session_realm" value="<?php echo $oasso_session_realm ?>"
                                       id="oasso_session_realm" class="form-control" type="text">
                                <p class="help-block"><?php echo $oasso_session_realm_help; ?></p>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label class="control-label" for="oasso_session_subrealm">
                                    <?php echo $oasso_session_subrealm_label; ?>
                                </label>
                                <input name="oasso_session_subrealm" value="<?php echo $oasso_session_subrealm ?>"
                                       id="oasso_session_subrealm" class="form-control" type="text">
                                <p class="help-block"><?php echo $oasso_session_subrealm_help; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php echo $footer; ?>
