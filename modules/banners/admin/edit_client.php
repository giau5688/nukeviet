<?php

/**
 * @Project NUKEVIET 3.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2012 VINADES.,JSC. All rights reserved
 * @Createdate 2-9-2010 14:43
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$id = $nv_Request->get_int( 'id', 'get', 0 );

if( empty( $id ) )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name );
	die();
}

$sql = "SELECT * FROM `" . NV_BANNERS_GLOBALTABLE. "_clients` WHERE `id`=" . $id;
$result = $db->sql_query( $sql );
$numrows = $db->sql_numrows( $result );

if( $numrows != 1 )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name );
	die();
}

$row = $db->sql_fetchrow( $result );

$error = '';

if( $nv_Request->get_int( 'save', 'post' ) == '1' )
{
	$login = strip_tags( $nv_Request->get_string( 'login_iavim', 'post', '' ) );
	$pass = strip_tags( $nv_Request->get_string( 'pass_iavim', 'post', '' ) );
	$re_pass = strip_tags( $nv_Request->get_string( 're_pass_iavim', 'post', '' ) );
	$full_name = nv_htmlspecialchars( strip_tags( $nv_Request->get_string( 'full_name', 'post', '' ) ) );
	$email = strip_tags( $nv_Request->get_string( 'email_iavim', 'post', '' ) );
	$website = strip_tags( $nv_Request->get_string( 'website_iavim', 'post', '' ) );
	$location = nv_htmlspecialchars( strip_tags( $nv_Request->get_string( 'location', 'post', '' ) ) );
	$yim = nv_htmlspecialchars( strip_tags( $nv_Request->get_string( 'yim_iavim', 'post', '' ) ) );
	$phone = strip_tags( $nv_Request->get_string( 'phone', 'post', '' ) );
	$fax = strip_tags( $nv_Request->get_string( 'fax', 'post', '' ) );
	$mobile = strip_tags( $nv_Request->get_string( 'mobile', 'post', '' ) );
	$uploadtype = $nv_Request->get_array( 'uploadtype', 'post' );
	$uploadtype = implode( ',', $uploadtype );

	$check_login = nv_check_valid_login( $login, NV_UNICKMAX, NV_UNICKMIN );
	$check_email = nv_check_valid_email( $email );
	$check_pass = nv_check_valid_pass( $pass, NV_UPASSMAX, NV_UPASSMIN );

	if( $website == "http://" ) $website = '';

	if( ! empty( $check_login ) )
	{
		$error = $check_login;
		$login = $row['login'];
	}
	elseif( ! empty( $check_email ) )
	{
		$error = $check_email;
		$email = $row['email'];
	}
	elseif( ! empty( $pass ) and ! empty( $check_pass ) )
	{
		$error = $check_pass;
		$pass = $re_pass = '';
	}
	elseif( ! empty( $pass ) and empty( $re_pass ) )
	{
		$error = $lang_global['re_password_empty'];
	}
	elseif( ! empty( $pass ) and $pass != $re_pass )
	{
		$error = sprintf( $lang_global['passwordsincorrect'], $pass, $re_pass );
		$pass = $re_pass = '';
	}
	elseif( empty( $full_name ) )
	{
		$error = $lang_module['full_name_empty'];
	}
	elseif( ! empty( $website ) and ! nv_is_url( $website ) )
	{
		$error = $lang_module['website_incorrect'];
	}
	elseif( ! empty( $yim ) and ! preg_match( "/^[a-zA-Z0-9\.\-\_]+$/", $yim ) )
	{
		$error = $lang_module['yim_incorrect'];
	}
	elseif( $db->sql_numrows( $db->sql_query( "SELECT `id` FROM `" . NV_BANNERS_GLOBALTABLE. "_clients` WHERE `id`!=" . $id . " AND `login`=" . $db->dbescape( $login ) ) ) > 0 )
	{
		$error = sprintf( $lang_module['login_is_already_in_use'], $login );
		$login = $row['login'];
	}
	elseif( $db->sql_numrows( $db->sql_query( "SELECT `id` FROM `" . NV_BANNERS_GLOBALTABLE. "_clients` WHERE `id`!=" . $id . " AND `email`=" . $db->dbescape( $email ) ) ) > 0 )
	{
		$error = sprintf( $lang_module['email_is_already_in_use'], $email );
		$email = $row['email'];
	}
	else
	{
		$pass = ( ! empty( $pass ) ) ? $crypt->hash( $pass ) : $row['pass'];

		$sql = "UPDATE `" . NV_BANNERS_GLOBALTABLE. "_clients` SET `login`=" . $db->dbescape( $login ) . ", `pass`=" . $db->dbescape( $pass ) . ", `full_name`=" . $db->dbescape( $full_name ) . ",
 `email`=" . $db->dbescape( $email ) . ", `website`=" . $db->dbescape( $website ) . ", `location`=" . $db->dbescape( $location ) . ", `yim`=" . $db->dbescape( $yim ) . ",
 `phone`=" . $db->dbescape( $phone ) . ", `fax`=" . $db->dbescape( $fax ) . ", `mobile`=" . $db->dbescape( $mobile ) . ", `uploadtype`=" . $db->dbescape( $uploadtype ) . " WHERE `id`=" . $id;
		$db->sql_query( $sql );
		nv_insert_logs( NV_LANG_DATA, $module_name, 'log_edit_client', "clientid " . $id, $admin_info['userid'] );
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=info_client&id=' . $id );
		die();
	}
}
else
{
	$login = $row['login'];
	$pass = $re_pass = '';
	$full_name = $row['full_name'];
	$email = $row['email'];
	$website = $row['website'];
	$location = $row['location'];
	$yim = $row['yim'];
	$phone = $row['phone'];
	$fax = $row['fax'];
	$mobile = $row['mobile'];
	$uploadtype = $row['uploadtype'];
	$uploadtype = explode( ',', $uploadtype );
	$imagecheck = ( in_array( 'images', $uploadtype ) ) ? 'checked=checked' : '';
	$flashcheck = ( in_array( 'flash', $uploadtype ) ) ? 'checked=checked' : '';
}

if( $website == '' ) $website = "http://";

$info = ( ! empty( $error ) ) ? $error : $lang_module['edit_client_info'];
$is_error = ( ! empty( $error ) ) ? 1 : 0;

$contents = array();
$contents['info'] = $info;
$contents['is_error'] = $is_error;
$contents['submit'] = $lang_module['edit_client_submit'];
$contents['action'] = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=edit_client&amp;id=" . $id;
$contents['login'] = array( $lang_module['login'], 'login_iavim', $login, NV_UNICKMAX );
$contents['pass'] = array( $lang_global['password'], 'pass_iavim', $pass, NV_UPASSMAX );
$contents['re_pass'] = array( $lang_global['password2'], 're_pass_iavim', $re_pass, NV_UPASSMAX );
$contents['full_name'] = array( $lang_module['full_name'], 'full_name', $full_name, 255 );
$contents['email'] = array( $lang_module['email'], 'email_iavim', $email, 70 );
$contents['website'] = array( $lang_module['website'], 'website_iavim', $website, 255 );
$contents['location'] = array( $lang_module['location'], 'location', $location, 255 );
$contents['yim'] = array( $lang_module['yim'], 'yim_iavim', $yim, 255 );
$contents['phone'] = array( $lang_module['phone'], 'phone', $phone, 255 );
$contents['fax'] = array( $lang_module['fax'], 'fax', $fax, 255 );
$contents['mobile'] = array( $lang_module['mobile'], 'mobile', $mobile, 255 );
$contents['uploadtype'] = array( $lang_module['uploadtype'], 'uploadtype', $imagecheck, $flashcheck );

$contents = call_user_func( "nv_edit_client_theme", $contents );

$page_title = $lang_module['edit_client'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';

?>