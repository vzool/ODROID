<?

if(!function_exists("php_cli")){
    function php_cli($cmd, $auto_reload = true){
        $result = trim(shell_exec($cmd));
        if($auto_reload) header('Location: '.$_SERVER['REQUEST_URI']);
        return $result;
    }
}

$GPIO = array(
	array('WiringPi' => '14', 'Header' => '23'),
    array('WiringPi' => '7', 'Header' => '7'),
    array('WiringPi' => '22', 'Header' => '31'),
    array('WiringPi' => '1', 'Header' => '12'),
    array('WiringPi' => '12', 'Header' => '19'),
    array('WiringPi' => '5', 'Header' => '18'),
    array('WiringPi' => '13', 'Header' => '21'),
    array('WiringPi' => '10', 'Header' => '24'),
    array('WiringPi' => '21', 'Header' => '29'),
    array('WiringPi' => '3', 'Header' => '15'),
	array('WiringPi' => '24', 'Header' => '35'),
    array('WiringPi' => '0', 'Header' => '11'),
    array('WiringPi' => '4', 'Header' => '16'),
    array('WiringPi' => '2', 'Header' => '13'),
    array('WiringPi' => '23', 'Header' => '33'),
    array('WiringPi' => '6', 'Header' => '22'),
    
    array('WiringPi' => '11', 'Header' => '26'),
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

if($_POST){

    try{
    	if(isset($_POST['gpio'])){
		    $gpio = $_POST['gpio'];
		    $mode = $_POST['mode'];
		    $status = $_POST['status'];

			php_cli("gpio mode $gpio $mode && gpio write $gpio $status");
    	}

    	if(isset($_POST['on_all'])){
    		foreach($GPIO as $g){
                $auto_reload = end($GPIO) === $g;
				php_cli("gpio mode {$g['WiringPi']} out && gpio write {$g['WiringPi']} 1", $auto_reload);
    		}
    	}

    	if(isset($_POST['off_all'])){
    		foreach($GPIO as $g){
                $auto_reload = end($GPIO) === $g;
			    php_cli("gpio mode {$g['WiringPi']} out && gpio write {$g['WiringPi']} 0", $auto_reload);
    		}
    	}

    	if(isset($_POST['cmd'])){
    		$header = $GPIO_CMD_MAP[strtoupper($_POST['cmd'])];
			$result = php_cli("gpio read $header", false);
			if($result === '1'){
	    		php_cli("gpio write $header 0 && gpio mode $header in");
			}else{
				php_cli("gpio mode $header out && gpio write $header 1");
			}
    	}

    }catch(Exception $ex){
	    echo "<h1>$ex</h1>";
    }
}

foreach($GPIO_CMD_MAP as $k => $v){
	$GPIO_STATUS[$k] = php_cli("gpio read $v", false);
}
?>

<center>
    <h1>^_^ Welcome to My Home Infrastructure Panel(HIP) ^_^</h1>
</center>
<hr/>
<div id='cmd_button'>
<?foreach($GPIO_CMD_MAP as $k => $g):?>
    <form method="post">
        <button class='<?=$GPIO_STATUS[$k] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='<?=$k?>'><?=$k?>&nbsp;-&nbsp;<?=$GPIO_STATUS[$k] == "1" ? "ON" : "OFF"?></button>
    </form>
<?endforeach?>
</div>

<table border='1' width='100%'>
    <tr align='center'>
        <td>
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
        </td>
        <td>
            <form method="post">
                    <input name='on_all' type='submit' value='ON ALL'/>
                    <input name='off_all' type='submit' value='OFF ALL'/>
            </form>
        </td>
    </tr>
</table>
<center>
    <h6>powered by <a href='http://www.hardkernel.com/main/products/prdt_info.php?g_code=G141578608433' target='_blank'>ODROID-C1</a> Coded by vZool</h6>
</center>
<style type="text/css">
form button{
	width: 50%;
	height: 10%;
    float: right;
}
.on, .off{
	font-weight: bold;
	font-size: 2em;
}
.on{
	color: green;
}
.off{
	color: red;
}
</style>