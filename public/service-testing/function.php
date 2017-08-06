<?php

/**
 * @author duy.1407
 * @copyright 2012
 */


function form($formName = 'form', $formData = array())
{
    echo '<form method="post">';
    echo '<table cellpadding="0" cellspacing="0" width="100%">';
    $formFields = $formData['params'];
    preg_match_all('/:([^\\/]*)/i', $formData['url'], $matches);
    if (isset($matches[1]))
        $formFields = array_merge($matches[1], $formFields);
    
    $fields = "";
    
    if (strpos($formData['method'], '|') !== false)
    {
        $methods = explode('|', $formData['method']);
        $fields .= '<tr><td width="25%"><label>Request Method</label></td><td>';
        $fields .= '<select name="method">';
        foreach ($methods as $method)
        {
            $fields .= '<option value="'.$method.'" '.(($method==$_POST['method']) ? 'selected="selected"':"").'>'.$method.'</option>';
        }
        $fields .= '</select>';
        $fields .= '</td></tr>';
    }
    
    foreach ($formFields as $index => $formField)
    {
        if ($formField == 'access_token') continue;
        
        $type = 'text';
        if ( ! is_int($index)) {
            $type = $formField;
            $formField = $index;
        }
        
        $fields .= '<tr><td width="25%"><label>'.ucwords(str_replace(array('-','_'),' ',$formField)).'</label></td><td>';
        switch ($type)
        {
            case 'radio':
                foreach ($formField[4] as $choice)
                {
                    $fields .= '<input type="radio" name="'.$formField[1].'" value="'.$choice[1].'" '.(($default==$choice[1])?'checked="checked"':"").' />'.'&nbsp;&nbsp;'.$choice[0].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                break;
            default:
                $fields .= '<input type="text" name="'.$formField.'" value="'.setting($formField).'" />';
                break;
        }
        if ($type == 'multi') {
            $fields .= '&nbsp;&nbsp;<small>?ay la m?ng, Dung d?u ; ?? phan cach cac ph?n t? (Ko dung ???c v?i form co upload file)</small>';
        }
        $fields .= '</td></tr>';
    }
    echo $fields;
    echo '<tr><td width="25%"></td><td>';
    $submitText = isset($formConfig['submit']) ? $formConfig['submit'] : "Send";
    echo '<button type="submit" name="submit" value="'.$formName.'">'.$submitText.'</button>';
    echo '</td></tr>';
    echo '</table>';
    echo '</form>';
}

function saveSettings($newSettings)
{
    global $settings;
    foreach ($newSettings as $key => $value)
    {
        $settings->$key = $value;
    }
    
    @unlink('./setting.conf');
    file_put_contents('./setting.conf', json_encode($settings));
}

function eveRequest($action, $request, $settings)
{
    global $config;
    //$request['token'] = setting('token');
    
    $url = $config[$action]['url'];
    $domain = setting(setting('server').'_url');
    if (substr($domain, -1) != '/')
    {
        $domain .= '/';
    }
    $url = @preg_replace('~:([^\\/]*)~e', '$request[$1]', $url);
    $url = $domain.$url;
    
    if (isset($config[$action]['is_secure']) and $config[$action]['is_secure'] === true and substr($url, 0, 5) == 'http:')
    {
        $url = 'https' . substr($url, 4);
    }
    
    $params = array();
	$hasFileUploaded = false;
	
    foreach ($config[$action]['params'] as $index => $param)
    {
        $type = 'text';
        if ( ! is_int($index)) {
            $type = $param;
            $param = $index;
        }
        
        $params[$param] = $request[$param];
        if ($type == 'multi' AND strlen($request[$param]) > 0) {
            $params[$param] = explode(';', $request[$param]);
        }
        
        if ($type == 'file' AND strlen($request[$param]) > 0) {
            $params[$param] = '@' . $request[$param];
			$hasFileUploaded = true;
        }
    }
    
    $method = $config[$action]['method'];
    
    if ( ! empty($_POST['method']))
    {
        $method = $_POST['method'];
    }
    
    $response = curl($method, $url, $params, $hasFileUploaded);
    $response->action = $action;
    return $response;
}

function setting($value, $default = "")
{
    global $settings;
    if (isset($settings->$value))
        return $settings->$value;
    return $default;
}

function curl($method, $url, $request = array(), $hasFileUploaded = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '100');
    $headers = array();
    
    if (setting('access_token')) {
        $headers[] = 'Authorization: Bearer ' . setting('access_token');
    }
    
    $query = http_build_query($request);    
    
    switch (strtolower($method))
    {
        case 'post':
            curl_setopt($ch, CURLOPT_URL, $url);
            // Set request method to POST
            curl_setopt($ch, CURLOPT_POST, 1);
            //Set query data here with CURLOPT_POSTFIELDS
            if ($hasFileUploaded) {                
            	curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            } else {
            	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            }
            break;
        case 'delete':
            curl_setopt($ch, CURLOPT_URL,$url.'?'.$query);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        case 'get':
            curl_setopt($ch,CURLOPT_URL,$url.'?'.$query);
            break;
        case 'put':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_PUT, 1);               
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            $headers[] = 'X-HTTP-Method-Override: PUT';
            break;
        default:
            die("This request method does not supported!");
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // SSL verify
    if (substr($url, 0, 5) === 'https')
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/AddTrustExternalCARoot.pem');
    }
    
    $response = curl_exec($ch);
    
    
    if ($response === false)
    {
        $response = curl_error($ch);
    }
    else
    {
        $curlInfo = curl_getinfo($ch);
        if ($curlInfo['http_code'] == 404)
        {
            $response = '404 Not Found';
        }
        else
        {
            if ($test_response = json_decode(trim($response), true)) {
                $response = $test_response;
            }
        }
    }
    //$content = json_decode(trim(curl_exec($ch)), true);
    //curl_close($ch);
    curl_close($ch);
# 		exit(json_encode($response));
    return (object) array('urlRequest'=>$url, 'access_token' => setting('access_token') ? setting('access_token') : "", 
        'queryRequest'=>$query, 'methodRequest'=>strtoupper($method), 'response'=>$response);
}


function dump($var, $level=1, $showSub = true, $space = "", $sub = "", $first = true)
{
    if ($first===true)
    {
        $print = dump(array(""=>$var),$level+1,$showSub,$space,$sub, false);
        //echo substr($print['data'],0,100000);
        echo $print['data'];
        return;
    }
    
    $output = "";
    $spaceBar = "&nbsp;&nbsp;&nbsp;";
    $maxStringLength = 100000;
    $count = 0;
    
    if (!in_array(gettype($var), array('object','array')))
    {
        switch (gettype($var))
        {
            case "string": $output .= "<font color='green'>".((strlen($var)>$maxStringLength)?"\"".substr(htmlspecialchars($var),0,$maxStringLength)." ...\" <font color='gray'>(".strlen($var)." characters)</font>":"\"".htmlspecialchars($var)."\"")."</font>";break;
            case "integer": $output .= "<font color='blue'>$var</font>";break;
            case "double": $output .= "<font color='pink'>$var</font>";break;
            case "boolean": $output .= "<font color='SkyBlue'>".(($var===true)?"true":"false")."</font>";break;
            case "NULL": $output .= "<font color='gray'>NULL</font>";break;
            default: $output .= $var; break;
        }
    }
    else
    {
        foreach ($var as $key=>$value)
        {
            $count++;
            $output .= ($level>0)?str_repeat("<br />",$level):"";
            $print = dump($value, $level-1, $showSub, $space.$spaceBar, ($showSub===true?$sub.$count.'.':''), false);
            $typeOfValue = gettype($value);
            $output .= "<br />$space".($showSub===true?"<font color='gray'><small>$sub$count.</small></font>":'')." ".
                (in_array($typeOfValue,array('object','array'))?"<font color='".($typeOfValue=="object"?"orange":"")."'>$typeOfValue</font> ":"").
                "<font style='color:#990000;font-weight:bold'>$key</font>";
            if (in_array($typeOfValue,array('object','array')))
                $output .= " (<font color='gray'>".$print['total_items'].")</font>";
            $output .= " <font color='gray'>=></font> ";
            if ($print['total_items']>1)
                $output .= "<br />$space{".$print['data']."<br />$space}";
            else
                $output .= $print['data'];
        }
    }
    
    $total_items = $count." item".(($count>1)?"s":"");

    return array(
        'data'=>$output,
        'total_items'=>$total_items
    );
}
