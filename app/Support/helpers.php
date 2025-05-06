<?php

function checkEmail($email){
    $email_domain = preg_replace('/^.+?@/', '', $email).'.';
    if(!checkdnsrr($email_domain, 'MX') && !checkdnsrr($email_domain, 'A')){
       return false;
    }
    return true;
}


function mres($value)
{
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return (str_replace($search, $replace, $value));
}


function cleaner_body($stringa)
{
	$output = "";

    $html_body = explode("<body", $stringa );
    $body_tmp = "<body" . $html_body[1];
    $body_tmp = str_ireplace("</html>", "", $body_tmp);
    $body_tmp = str_ireplace("<o:p></o:p>", "", $body_tmp);
    $body_tmp = str_ireplace("&nbsp;", "", $body_tmp);
    $body_tmp = str_ireplace("\r\n", "", $body_tmp);
    $body_tmp = str_ireplace("\t", "", $body_tmp);
    
	if (strpos($body_tmp, '<body ') !== false) {
	    $posizione = strpos($body_tmp, ">");
		$output = substr($body_tmp, $posizione+1);
	    $output = str_replace('</body>', '', $output);
	}
	return $output;
}

