<?php
include("lib.php");
define("PAGENAME", $player->username);
$player = check_user($secret_key, $db);

include("includes/header.php");
?>

<div id="topbar" class="transparent">
	<div id="title"><?php echo $player->username;?></div>
	<div id="leftnav">
		<a href="account.php"><?php echo $lang['page_account']; ?></a>
	</div>
</div>
<div id="tributton">
	<div class="links">
		<a id="pressed" href="home.php"><?php echo $lang['char_home']; ?></a><a href="spells.php"><?php echo $lang['char_spells']; ?></a><a href="inventory.html"><?php echo $lang['char_inventory']; ?></a>
	</div>
</div>
<div id="content">
	<ul class="pageitem">
		<li class="textbox">
		<table width="100%" align="center"><tr>
		<td width="100" align="center"><img style="border-left: 2px solid black; border-right: 2px solid black; border-top: 2px solid black; border-bottom: 2px solid black; -webkit-border-radius:5px;" src="<?php if ($player->avatar == NULL){ echo $setting->avatar; }else{ echo $player->avatar; } ?>" width="65" height="65"/></td>
		<td align="center">
		HP <img style="border-left: 2px solid black; border-right: 2px solid black; border-top: 2px solid black; border-bottom: 2px solid black; -webkit-border-radius:5px;" src="bargen.php?hp" width="130"/><br/>
		EP <img style="border-left: 2px solid black; border-right: 2px solid black; border-top: 2px solid black; border-bottom: 2px solid black; -webkit-border-radius:5px;" src="bargen.php?energy" width="130"/><br/>
		MP <img style="border-left: 2px solid black; border-right: 2px solid black; border-top: 2px solid black; border-bottom: 2px solid black; -webkit-border-radius:5px;" src="bargen.php?mana" width="130"/>
		</td>
		</tr></table>
		</li>

		<li class="textbox">
		<table width="100%" align="center"><tr>
		<td width="100" align="center"><?php echo "" . $lang['level'] . " " . $player->level . ""; ?></td>
		<td align="center">
		EXP <img style="border-left: 2px solid black; border-right: 2px solid black; border-top: 2px solid black; border-bottom: 2px solid black; -webkit-border-radius:5px;" src="bargen.php?exp" width="130"/>
		</td>
		</tr></table>
		</li>

	<?php
		if ($player->guild != NULL){
		echo "<li class=\"textbox\">";
		echo "<table width=\"100%\" align=\"center\"><tr>";
		echo "<td width=\"100\" align=\"center\">" . $lang['guild'] . "</td>";
		echo "<td align=\"center\">";
		echo "" . $player->guild . "";
		echo "</td>";
		echo "</tr></table>";
		echo "</li>";
		}
	?>
	</ul>
	
	<ul class="pageitem">
		<li class="textbox">
	<table width="100%" align="center"><tr><td width="35%">
		<span class="graytitle"><b>FOR</b> <?php echo $player->strength;?></span><br/>
		<span class="graytitle"><b>VIT</b> <?php echo $player->vitality;?></span>
	</td><td width="35%">
		<span class="graytitle"><b>AGI</b> <?php echo $player->agility;?></span><br/>
		<span class="graytitle"><b>RES</b> <?php echo $player->resistance;?></span>
	</td><td width="30%">
		<b><?php echo $lang['gold']; ?></b><br/>
		<font color="#DFA40F"><?php echo $player->gold;?></font>
	</td></tr></table>
		</li>
	</ul>

	<ul class="pageitem">
	<?php
		if ($player->stat_points > 0){
		echo "<li class=\"menu\" style=\"background-color: #EEA2A2; -webkit-border-top-left-radius: 8px; -webkit-border-top-right-radius: 8px;\">";
		echo "<a href=\"statpoits.php\">";
		echo "<span class=\"name\">" . sprintf($lang['char_statpoints'], $player->stat_points) . "</span>";
		echo "<span class=\"comment\">" . $lang['distribute_points'] . "</span>";
		echo "<span class=\"arrow\"></span>";
		echo "</a>";
		echo "</li>";
		}

		if ($player->magic_points > 0){
		echo "<li class=\"menu\">";
		echo "<a href=\"statpoits.php\">";
		echo "<span class=\"name\">" . sprintf($lang['char_magicpoints'], $player->magic_points) . "</span>";
		echo "<span class=\"comment\">" . $lang['distribute_points'] . "</span>";
		echo "<span class=\"arrow\"></span>";
		echo "</a>";
		echo "</li>";
		}
	?>

	</ul>
</div>

<?php include("includes/footer.php"); ?>