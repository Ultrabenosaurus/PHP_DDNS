<?php

error_reporting(E_ALL);
ini_set("display_errors", -1);
require_once "config.php";

if( isset( $_POST['PHP_DDNS_INSTALL'] ) )
{
    $page = 2;
    unset( $_POST['PHP_DDNS_INSTALL'] );

    $conf = array( 'database' => $_POST );
    file_put_contents( PHP_DDNS_ROOT . "assets/other/.conf", json_encode( $conf ) );

    $PD = new \PHP_DDNS\Core\PHP_DDNS( $conf );

    $device = array(
        'uuid' => 'c13bfd49d74bc4a291deddfbd8d30b8ac20b2527',
        'name' => 'DB-Laptop',
        'ip'   => '127.0.0.1',
        'key'  => '{O<{DxGc35'
    );
}
else
{
    $page = 1;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>PHP_DDNS Initialisation</title>
    <link rel="stylesheet" href="assets/css/reset.css" />
    <link rel="stylesheet" href="assets/css/fonts.css" />
    <link rel="stylesheet" href="assets/css/install.css" />
</head>
<body>
    <div id="container">
        <h1>PHP_DDNS Installer</h1>
        <p>This is a one-time script to help you get <strong>PHP_DDNS</strong> installed on your server.</p>
        <?php if( 2 === $page ){ ?>
            <h2>Database Test</h2>
            <p>Adding test device...<?php $add_res = $PD->addDevice( $device ); ?><br />
            <?php if( "success" == $add_res[0] ){ ?>
                Deleting test device...<?php $rem_res = $PD->removeDevice( $add_res[2] ); ?><br />
                <?php if( "success" == $rem_res[0] ){ ?>
                    </p>Success! The database seems to have been setup correctly!<p>
                    </p>You can now safely delete this file.<p>
                <?php }else{ ?>
                    Looks like the test device couldn't be removed. Check out the error below to see what happened.<br />
                <?php echo "<pre>" . ( ( isset( $rem_res[2] ) ) ? print_r( $rem_res[2], true ) : $rem_res[1] ) . "</pre>"; } ?>
            <? }else{ ?>
                Looks like the test device couldn't be added. Check out the error below to see what happened.<br />
            <?php echo "<pre>" . ( ( isset( $rem_res[2] ) ) ? print_r( $rem_res[2], true ) : $rem_res[1] ) . "</pre>"; } ?>
            </p>
        <?php }else{ ?>
            <h2>Database Configuration</h2>
            <form method="post" action="install.php">
                <input type="hidden" name="PHP_DDNS_INSTALL" value="1" />
                <div class="form-row">
                    <label for="host">Database Host:</label>
                    <input type="text" name="host" value="<?php echo ( ( isset( $_config['database']['host'] ) ) ? $_config['database']['host'] : "" ) ?>" />
                </div>
                <div class="form-row">
                    <label for="name">Database Name:</label>
                    <input type="text" name="name" value="<?php echo ( ( isset( $_config['database']['name'] ) ) ? $_config['database']['name'] : "" ) ?>" />
                </div>
                <div class="form-row">
                    <label for="user">Database User:</label>
                    <input type="text" name="user" value="<?php echo ( ( isset( $_config['database']['user'] ) ) ? $_config['database']['user'] : "" ) ?>" />
                </div>
                <div class="form-row">
                    <label for="pass">Database Password:</label>
                    <input type="password" name="pass" value="<?php echo ( ( isset( $_config['database']['pass'] ) ) ? $_config['database']['pass'] : "" ) ?>" />
                </div>
                <div class="form-row">
                    <label for="table">Table Name:</label>
                    <input type="text" name="table" value="<?php echo ( ( isset( $_config['database']['table'] ) ) ? $_config['database']['table'] : "" ) ?>" />
                </div>
                <div class="form-row">
                    <input type="submit" value="Submit" />
                </div>
            </form>
        <?php } ?>
    </div>
</body>
</html>
