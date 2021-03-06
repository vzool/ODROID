
<?

$GPIO = array(
	array('WiringPi' => '0', 'Header' => '11'),
	array('WiringPi' => '1', 'Header' => '12'),
	array('WiringPi' => '2', 'Header' => '13'),
	array('WiringPi' => '3', 'Header' => '15'),
	array('WiringPi' => '4', 'Header' => '16'),
	array('WiringPi' => '5', 'Header' => '18'),
	array('WiringPi' => '6', 'Header' => '22'),
	array('WiringPi' => '7', 'Header' => '7'),
	array('WiringPi' => '10', 'Header' => '24'),
	array('WiringPi' => '11', 'Header' => '26'),
	array('WiringPi' => '12', 'Header' => '19'),
	array('WiringPi' => '13', 'Header' => '21'),
	array('WiringPi' => '14', 'Header' => '23'),
	array('WiringPi' => '21', 'Header' => '29'),
	array('WiringPi' => '22', 'Header' => '31'),
	array('WiringPi' => '23', 'Header' => '33'),
	array('WiringPi' => '24', 'Header' => '35'),
	array('WiringPi' => '26', 'Header' => '32'),
	array('WiringPi' => '27', 'Header' => '36'),
);

$GPIO_CMD_MAP = array(
	'A' => '14',
	'B' => '7',
	'C' => '22',
	'D' => '1',
	'E' => '12',
	'F' => '5',
	'G' => '13',
	'H' => '10',
	'I' => '21',
	'J' => '3',
	'K' => '24',
	'L' => '0',
	'M' => '4',
	'N' => '2',
	'O' => '23',
	'P' => '6',
);

$GPIO_STATUS = array();

$gpio = -1;
$mode = -1;
$status = -1;
$debug = 0;

if($_POST){

    try{
    	if(isset($_POST['gpio'])){
		    $gpio = $_POST['gpio'];
		    $mode = $_POST['mode'];
		    $status = $_POST['status'];

	    	$cmd = "gpio mode $gpio $mode";
			$output = shell_exec($cmd);
	    	echo "<h1>$cmd ==> $output</h1>";
	    	/*sleep(2);*/

		    $cmd = "gpio write $gpio $status";
		    $output = shell_exec($cmd);

		    echo "<h1>$cmd ==> $output</h1>";
    	}

    	if(isset($_POST['on_all'])){
    		if(isset($_POST['debug']))$debug = $_POST['debug'];
    		foreach($GPIO as $g){
    			$cmd = "gpio mode {$g['WiringPi']} out";
				$output = shell_exec($cmd);
		    	if($debug)echo "<h1>$cmd ==> $output</h1>";
			    $cmd = "gpio write {$g['WiringPi']} 1";
			    $output = shell_exec($cmd);
			    if($debug)echo "<h1>$cmd ==> $output</h1>";
    		}
    	}

    	if(isset($_POST['off_all'])){
    		if(isset($_POST['debug']))$debug = $_POST['debug'];
    		foreach($GPIO as $g){
    			$cmd = "gpio mode {$g['WiringPi']} out";
				$output = shell_exec($cmd);
		    	if($debug)echo "<h1>$cmd ==> $output</h1>";
			    $cmd = "gpio write {$g['WiringPi']} 0";
			    $output = shell_exec($cmd);
			    if($debug)echo "<h1>$cmd ==> $output</h1>";
    		}
    	}

    	if(isset($_POST['cmd'])){
    		$debug_text = "<hr/>";
    		$header = $GPIO_CMD_MAP[strtoupper($_POST['cmd'])];
    		$cmd = "gpio read $header";
			$output = shell_exec($cmd);
			$debug_text .= "<h3>$cmd ### $output</h3>";
			if(trim($output) === '1'){
	    		$cmd = "gpio mode $header in";
				$output = shell_exec($cmd);
				$debug_text .= "<h3>$cmd ### $output</h3>";
			}else{
	    		$cmd = "gpio mode $header out";
				$output = shell_exec($cmd);
				$cmd = "gpio write $header 1";
				$output = shell_exec($cmd);
				$debug_text .= "<h3>$cmd ### $output</h3>";
			}
			$debug_text .= "<hr/>";

			echo $debug_text;
    	}

    }catch(Exception $ex){
	    echo "<h1>$ex</h1>";
    }
}


foreach($GPIO_CMD_MAP as $k => $v){
	$cmd = "gpio read $v";
	$output = shell_exec($cmd);
	$GPIO_STATUS[$k] = trim($output);
}

?>

<form method="post">
        <select name='gpio'>
        		<?foreach($GPIO as $g):?>
        			<? $selected = $gpio == $g['WiringPi'] ? "SELECTED" : ""?>
                	<option <?=$selected?> value='<?=$g['WiringPi']?>'>Header PIN <?=$g['Header']?> ### WiringPi <?=$g['WiringPi']?></option>
                <?endforeach?>
        </select>

        <select name='mode'>
        	<option <?= $mode == "out" ? "SELECTED" : ""?> value='out'>OUT</option>
        	<option <?= $mode == "in" ? "SELECTED" : ""?> value='in'>IN</option>
        </select>

        <select name='status'>
        	<option <?= $status == 1 ? "SELECTED" : ""?> value='1'>ON</option>
        	<option <?= $status == 0 ? "SELECTED" : ""?> value='0'>OFF</option>
        </select>

        <input type='submit' value='Execute'/>
</form>

<hr/>
<form method="post">
        <input name='on_all' type='submit' value='ON ALL'/>
        <input name='off_all' type='submit' value='OFF ALL'/>
        <input type='checkbox' name='debug' value='<?=$debug?>'>debug</input>
</form>

<hr/>

<table dir='rtl' width='100%'>
    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['A'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='A'>A&nbsp;-&nbsp;<?=$GPIO_STATUS['A'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['B'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='B'>B&nbsp;-&nbsp;<?=$GPIO_STATUS['B'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>

    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['C'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='C'>C&nbsp;-&nbsp;<?=$GPIO_STATUS['C'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['D'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='D'>D&nbsp;-&nbsp;<?=$GPIO_STATUS['D'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>
    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['E'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='E'>E&nbsp;-&nbsp;<?=$GPIO_STATUS['E'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['F'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='F'>F&nbsp;-&nbsp;<?=$GPIO_STATUS['F'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>
    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['G'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='G'>G&nbsp;-&nbsp;<?=$GPIO_STATUS['G'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['H'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='H'>H&nbsp;-&nbsp;<?=$GPIO_STATUS['H'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>
    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['I'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='I'>I&nbsp;-&nbsp;<?=$GPIO_STATUS['I'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['J'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='J'>J&nbsp;-&nbsp;<?=$GPIO_STATUS['J'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>
    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['K'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='K'>K&nbsp;-&nbsp;<?=$GPIO_STATUS['K'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['L'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='L'>L&nbsp;-&nbsp;<?=$GPIO_STATUS['L'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>
    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['M'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='M'>M&nbsp;-&nbsp;<?=$GPIO_STATUS['M'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['N'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='N'>N&nbsp;-&nbsp;<?=$GPIO_STATUS['N'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>
    <tr>
    	<td>
			<form method="post">
    			<button class='<?=$GPIO_STATUS['O'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='O'>O&nbsp;-&nbsp;<?=$GPIO_STATUS['O'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    	<td>
    		<form method="post">
    			<button class='<?=$GPIO_STATUS['P'] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='P'>P&nbsp;-&nbsp;<?=$GPIO_STATUS['P'] == "1" ? "ON" : "OFF"?></button>
			</form>
    	</td>
    </tr>
</table>

<style type="text/css">
td form button{
	width: 80%;
	height: 12.5%;
}
td{
	text-align: center;
}
.on, .off{
	font-weight: bold;
	font-size: 1.5em;
}
.on{
	color: green;
}
.off{
	color: red;
}
</style>