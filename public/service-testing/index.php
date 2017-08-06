<?php
//    if ($_SERVER["REMOTE_ADDR"] != "192.168.1.21") {
//        die("Updating... please wait.");
//    }

    error_reporting(E_ALL | E_STRICT);
    ini_set('show_errors', 1);
    
    require_once('./function.php');
    require_once('./config.php');
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $settings = json_decode(file_get_contents('./setting.conf'));
    if (isset($_POST['submit']))
    {
        $action = $_POST['submit'];
        unset($_POST['submit']);
        
        saveSettings($_POST);
        $settings = json_decode(file_get_contents('./setting.conf'));
        if ($action != 'settings')
            $response = eveRequest($action, $_POST, $settings);
        
        if (in_array($action, array('login', 'register')) and isset($response->response['access_token']))
        {
            saveSettings(array('access_token'=>$response->response['access_token']));
        }
    }
?>


<!DOCTYPE HTML>
<head>
	<meta http-equiv="content-type" content="text/html" charset="utf-8" />
	<meta name="author" content="duy.1407" />

	<title>Service Tester 1.0.0</title>
    
    <link rel="stylesheet" href="default.css" />
    <script type="text/javascript" src="jquery-1.7.2.min.js"></script>
</head>

<body>

    <div id="main">
        <div id="header">
            <h2 style="margin-bottom: 20px;">Service Tester 1.0.0</h2>
            <div class="header-toolbar">
                <a class="tab" id="settings" href="#">
                    Settings
                </a>
            </div>
        </div>
        
        <div class="tab-content settings-tab">
            <?php //form('settings', array('Server','server',setting('server'),'radio',array(array('Local Server','local'),array('Live Server','live'))),array('config'=>array('submit_text'=>'Save Settings')))?>
            
            <form method="post">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="25%"><label>Test Sever</label></td>
                        <td>
                        <input type="radio" name="server" value="local" <?php echo((setting('server')=='local')?'checked="checked"':"")?> />&nbsp;&nbsp;Local&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="server" value="live" <?php echo((setting('server')=='live')?'checked="checked"':"")?> />&nbsp;&nbsp;Live&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><label>Local Server Url</label></td>
                        <td><input type="text" name="local_url" style="width: 250px;" value="<?php echo setting('local_url')?>" /></td>
                    </tr>
                    <tr>
                        <td><label>Live Server Url</label></td>
                        <td><input type="text" name="live_url" style="width: 250px;" value="<?php echo setting('live_url')?>" /></td>
                    </tr>
                    <tr><td width="25%"></td><td>
                    <button type="submit" name="submit" value="settings">Save</button>
                    </td></tr>
                </table>'
            </form>
        </div>
        
        <div class="tab first" id="response">
            Response
            <?php if (isset($response)):?>
            <span style="font-weight: normal;">- <a href="#" onclick="window.location.reload();">retry</a></span>
            <?php endif;?>
             - <?php echo date('Y-m-d H:i:s');?>
        </div>
        <?php if (isset($response)):?>
        <div class="tab-content response-tab">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="25%">Request Url</td>
                    <td class="link"><?php echo $response->urlRequest?></td>
                </tr>
                <tr>
                    <td>Request Query</td>
                    <td class="link"><?php echo htmlspecialchars(urldecode($response->queryRequest))?></td>
                </tr>
                <tr>
                    <td>Access Token</td>
                    <td class="link"><?php echo $response->access_token?></td>
                </tr>
                <tr>
                    <td>Request Method</td>
                    <td class="link"><?php echo $response->methodRequest?></td>
                </tr>
            </table>
            <?php echo '<pre>';dump($response->response, -1, false);echo '</pre>';?>
            <a href="#" class="close">close</a>
        </div>
        
        <?php endif;?>
        
        <?php foreach ($config as $requestName => $requestData) :?>
        <?php $title = isset($requestData['title']) ? $requestData['title'] : ucwords(str_replace('-', ' ', $requestName));?>
        <div class="tab" id="<?php echo $requestName?>">
            <?php echo $title;?>
        </div>
        <div class="tab-content <?php echo $requestName?>-tab">
            <?php form($requestName, $requestData); ?>
            <a href="#" class="close">close</a>
        </div>
        <?php endforeach;?>
        
    </div>


<script type="text/javascript">
    $(document).ready(function(){
        $(".tab").click(function(e){
            e.preventDefault();
            var tab_name = $(this).attr("id");
            if ($('.'+tab_name+'-tab').is(":visible"))
            {
                $('.'+tab_name+'-tab').slideUp();
                <?php if ( ! empty($response)) :?>
                if (tab_name == "response")
                {
                    $('.<?php echo $response->action?>-tab').slideDown();
                }
                <?php endif;?>
            }
            else
            {
                $('.tab-content').slideUp();
                $('.'+tab_name+'-tab').slideDown();
            }
            
        });
        $(".close").click(function(e){
            e.preventDefault();
            $(this).parent().prev().click();
        });
        <?php if (isset($response)):?>
        $("#response").click();
        <?php endif;?>
    });
</script>

<div id="footer">
</div>

</body>
</html>