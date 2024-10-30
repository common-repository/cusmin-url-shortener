<?php
/*
Plugin Name: Cusmin URL Shortener
Plugin URI: https://cusmin.com/url-shortener
Description: Generate Short URLs with ease
Version: 1.4
Author: Cusmin
Author URI: https://cusmin.com
License: GPLv2 or later
Text Domain: cusmin-url-shortener
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2017 Cusmin
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('CusminGoogl')){
    require_once 'includes/CusminGoogl.php';
}
if(!class_exists('CusminURLShortener')){
    require_once 'CusminURLShortener.php';
}

$cb = new CusminURLShortener();