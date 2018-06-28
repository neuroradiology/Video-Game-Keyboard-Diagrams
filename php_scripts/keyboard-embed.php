<?php
	// Copyright (C) 2018  Michael Horvath
	//
	// This program is free software: you can redistribute it and/or modify
	// it under the terms of the GNU General Public License as published by
	// the Free Software Foundation, either version 3 of the License, or
	// any later version.
	// 
	// This program is distributed in the hope that it will be useful,
	// but WITHOUT ANY WARRANTY; without even the implied warranty of
	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	// GNU General Public License for more details.
	// 
	// You should have received a copy of the GNU General Public License
	// along with this program. If not, see <https://www.gnu.org/licenses/>.

	$path_root		= "../";
	$path_file		= "./keyboard-embed.php";

	header("Content-Type: text/html; charset=utf8");

	include($path_root. "ssi/keyboard-connection.php");
	include("./keyboard-common.php");

	$con = mysqli_connect($con_website,$con_username,$con_password,$con_database);
 
	// check connection
	if (mysqli_connect_errno())
	{
		trigger_error("Database connection failed: "  . mysqli_connect_error(), E_USER_ERROR);
	}

	mysqli_query($con, "SET NAMES 'utf8'");

	$game_seo		= array_key_exists("seo", $_GET) ? $_GET["seo"] : null;
	$game_id		= array_key_exists("gam", $_GET) ? intval(ltrim($_GET["gam"], "0")) : null;
	$style_id		= array_key_exists("sty", $_GET) ? intval(ltrim($_GET["sty"], "0")) : null;
	$layout_id		= array_key_exists("lay", $_GET) ? intval(ltrim($_GET["lay"], "0")) : null;
	$format_id		= array_key_exists("fmt", $_GET) ? intval(ltrim($_GET["fmt"], "0")) : null;
	$svg_bool		= array_key_exists("svg", $_GET) ? intval(ltrim($_GET["svg"], "0")) : null;
	$fix_url		= false;
	$php_url		= "";
	$svg_url		= "";
	$stylegroup_id		= 0;
	$command_table		= [];
	$combo_table		= [];
	$mouse_table		= [];
	$joystick_table		= [];
	$note_table		= [];
	$cheat_table		= [];
	$console_table		= [];
	$emote_table		= [];
	$author_table		= [];
	$style_table		= [];
	$gamesrecord_id		= 0;
	$gamesrecord_author	= "";
	$stylesrecord_id	= 0;
	$stylesrecord_author	= "";
	$combo_count		= 0;
	$mouse_count		= 0;
	$joystick_count		= 0;
	$note_count		= 0;
	$cheat_count		= 0;
	$console_count		= 0;
	$emote_count		= 0;
	$style_filename		= "";
	$style_name		= "";
	$style_author		= "";
	$game_name		= "";
	$platform_name		= "";
	$layout_platform	= 0;
	$layout_name		= "";
	$layout_author		= "";
	$layout_keysnum		= 0;
	$layout_keygap		= 4;
	$string_title		= cleantextHTML("Video Game Keyboard Diagrams");
	$string_combo		= cleantextHTML("Keyboard Combinations");
	$string_mouse		= cleantextHTML("Mouse Controls");
	$string_joystick	= cleantextHTML("Joystick/Gamepad Controls");
	$string_note		= cleantextHTML("Additional Notes");
	$string_cheat		= cleantextHTML("Cheat Codes");
	$string_console		= cleantextHTML("Console Commands");
	$string_emote		= cleantextHTML("Chat Commands/Emotes");
	$string_description	= cleantextHTML("Keyboard hotkey & binding chart for ");
	$string_keywords	= cleantextHTML("English,keyboard,keys,diagram,chart,overlay,shortcut,binding,mapping,map,controls,hotkeys,database,print,printable,video game,software,visual,guide,reference");


	// validity checks
	if ($game_id === null)
	{
		if ($game_seo !== null)
		{
			$selectString = "SELECT g.game_name, g.game_id FROM games AS g WHERE g.game_friendlyurl = \"" . $game_seo . "\";";
			selectQuery($con, $selectString, "doGamesSEOHTML");
		}
		else
		{
			$game_id = 1;
		}
	}
	if ($game_seo === null)
	{
		$fix_url = true;
	}
	if ($style_id === null)
	{
		$style_id = 15;
		$fix_url = true;
	}
	if ($layout_id === null)
	{
		$layout_id = 1;
		$fix_url = true;
	}
	if ($format_id === null)
	{
		if ($svg_bool !== null)
		{
			$format_id = $svg_bool;
		}
		else
		{
			$format_id = 1;
		}
		$fix_url = true;
	}


	selGamesHTML();
	selAuthorsHTML();
	selStylesHTML();
	selThisStyleHTML();
	selLayoutsHTML();
	selPlatformsHTML();
	selGamesRecordsHTML();
	selStylesRecordsHTML();
	selCommandsHTML();


	mysqli_close($con);

	// validity checks
	$game_seo		= $game_seo ? $game_seo : "unrecognized-game";
	$temp_game_name		= $game_name ? $game_name : "Unrecognized Game";
	$temp_layout_name	= $layout_name ? $layout_name : "Unrecognized Layout";
	$temp_style_name	= $style_name ? $style_name : "Unrecognized Style";
	$temp_platform_name	= $platform_name ? $platform_name : "Unrecognized Platform";
	$thispage_title_a	= $temp_game_name;
	$thispage_title_b	= " - " . $string_title . " - " . $temp_platform_name . " " . $temp_layout_name . " - " . $temp_style_name . " - ID:" . $gamesrecord_id;

	// validity checks (should check the layout here too... but)
	if (!checkStyle($style_id))
	{
		$style_id = 15;
		$fix_url = true;
	}

	$php_url = "http://isometricland.net/keyboard/keyboard-diagram-" . $game_seo . ".php?sty=" . $style_id . "&lay=" . $layout_id . "&fmt=" . $format_id;
	$svg_url = "http://isometricland.net/keyboard/keyboard-diagram-" . $game_seo . ".svg?sty=" . $style_id . "&lay=" . $layout_id . "&fmt=" . $format_id;

	// fix URL
	if ($fix_url === true)
	{
		header("Location: " . $php_url);
		die();
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
<?php
	echo
"		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>
		<title>" . $thispage_title_a . $thispage_title_b . "</title>
		<link rel=\"canonical\" href=\"" . $php_url . "\"/>
		<link rel=\"icon\" type=\"image/png\" href=\"" . $path_root . "favicon.png\"/>
		<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $path_root . "style_normalize.css\"/>
		<link rel=\"stylesheet\" type=\"text/css\" href=\"./style_common.css\"/>
		<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"/>
		<meta name=\"description\" content=\"" . $string_description . $temp_game_name . ". (" . $temp_style_name . ")\"/>
		<meta name=\"keywords\" content=\"" . $temp_game_name . "," . $temp_style_name . "," . "SVG" . "," . $string_keywords . "\"/>\n";
	include($path_root . "ssi/analyticstracking.php");
	echo
"		<script src=\"keyboard-chart-js.php\"></script>
		<style type=\"text/css\">\n";
	include("./embed_" . $style_filename . ".css");
	echo
"		</style>\n";
?>
	</head>
	<body>
		<header>
			<div class="bodiv">
				<h2><?php echo $thispage_title_a; ?><small><?php echo $thispage_title_b; ?></small></h2>
			</div>
		</header>
		<main>
			<div class="bodiv" style="width:1684px;height:640px;">
				<div id="keydiv" style="position:relative;width:1684px;height:640px;">
					<img src="<?php echo $svg_url; ?>" width="1684" height="640"/>
					<div style="position:absolute;left:600px;top:521.5px;width:728px;height:90px;padding:none;margin:none;z-index:10;">
<?php /*include($path_root . "ssi/adsense_horz_large.php");*/ ?>
					</div>
				</div>
			</div>
<?php
	// combo
	if ($combo_count > 0)
	{
		echo
"			<div class=\"bodiv inbtop combox\">
				<h3>" . $string_combo . "</h3>
				<p>\n";
		for ($i = 0; $i < $combo_count; $i++)
		{
			$combo_row = $combo_table[$i];
			if ($combo_row[0] || $combo_row[1])
			{
				$combo_com = cleantextHTML($combo_row[0]);
				$combo_des = cleantextHTML($combo_row[1]);
				echo
"					" . $combo_com . " = " . $combo_des . "<br/>\n";
			}
		}
		echo
"				</p>
			</div>\n";
	}

	// mouse
	if ($mouse_count > 0)
	{
		echo
"			<div class=\"bodiv inbtop combox\">
				<h3>" . $string_mouse . "</h3>
				<p>\n";
		for ($i = 0; $i < $mouse_count; $i++)
		{
			$mouse_row = $mouse_table[$i];
			if ($mouse_row[0] || $mouse_row[1])
			{
				$mouse_com = cleantextHTML($mouse_row[0]);
				$mouse_des = cleantextHTML($mouse_row[1]);
				echo
"					" . $mouse_com . " = " . $mouse_des . "<br/>\n";
			}
		}
		echo
"				</p>
			</div>\n";
	}

	// joystick
	if ($joystick_count > 0)
	{
		echo
"			<div class=\"bodiv inbtop combox\">
				<h3>" . $string_joystick . "</h3>
				<p>\n";
		for ($i = 0; $i < $joystick_count; $i++)
		{
			$joystick_row = $joystick_table[$i];
			if ($joystick_row[0] || $joystick_row[1])
			{
				$joystick_com = cleantextHTML($joystick_row[0]);
				$joystick_des = cleantextHTML($joystick_row[1]);
				echo
"					" . $joystick_com . " = " . $joystick_des . "<br/>\n";
			}
		}
		echo
"				</p>
			</div>\n";
	}

	// note
	if ($note_count > 0)
	{
		echo
"			<div class=\"bodiv inbtop combox\">
				<h3>" . $string_note . "</h3>
				<p>\n";
		for ($i = 0; $i < $note_count; $i++)
		{
			$note_row = $note_table[$i];
			if ($note_row[0] || $note_row[1])
			{
				$note_com = cleantextHTML($note_row[0]);
				$note_des = cleantextHTML($note_row[1]);
				echo
"					" . $note_des . "<br/>\n";
			}
		}
		echo
"				</p>
			</div>\n";
	}

	// cheat
	if ($cheat_count > 0)
	{
		echo
"			<div class=\"bodiv inbtop combox\">
				<h3>" . $string_cheat . "</h3>
				<p>\n";
		for ($i = 0; $i < $cheat_count; $i++)
		{
			$cheat_row = $cheat_table[$i];
			if ($cheat_row[0] || $cheat_row[1])
			{
				$cheat_com = cleantextHTML($cheat_row[0]);
				$cheat_des = cleantextHTML($cheat_row[1]);
				echo
"					" . $cheat_com . " = " . $cheat_des . "<br/>\n";
			}
		}
		echo
"				</p>
			</div>\n";
	}

	// console
	if ($console_count > 0)
	{
		echo
"			<div class=\"bodiv inbtop combox\">
				<h3>" . $string_console . "</h3>
				<p>\n";
		for ($i = 0; $i < $console_count; $i++)
		{
			$console_row = $console_table[$i];
			if ($console_row[0] || $console_row[1])
			{
				$console_com = cleantextHTML($console_row[0]);
				$console_des = cleantextHTML($console_row[1]);
				echo
"					" . $console_com . " = " . $console_des . "<br/>\n";
			}
		}
		echo
"				</p>
			</div>\n";
	}

	// emote
	if ($emote_count > 0)
	{
		echo
"			<div class=\"bodiv inbtop combox\">
				<h3>" . $string_emote . "</h3>
				<p>\n";
		for ($i = 0; $i < $emote_count; $i++)
		{
			$emote_row = $emote_table[$i];
			if ($emote_row[0] || $emote_row[1])
			{
				$emote_com = cleantextHTML($emote_row[0]);
				$emote_des = cleantextHTML($emote_row[1]);
				echo
"					" . $emote_com . " = " . $emote_des . "<br/>\n";
			}
		}
		echo
"				</p>
			</div>\n";
	}
?>
		</main>
		<footer>
<?php include("./keyboard-footer.php"); ?>
		</footer>
	</body>
</html>
