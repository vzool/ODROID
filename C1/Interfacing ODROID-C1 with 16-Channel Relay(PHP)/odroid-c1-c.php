<?

/*=============================================================================================*/
/*############################### Authenticated Access Security ###############################*/
/*=============================================================================================*/
$realm = 'Restricted Area!';
$wrong_credential_message = "<h1>401 Restricted Area: Failed to Authenticate!</h1>";

//user => password
$users = array("vzool" => "qwe6630446asd");

if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    die('Text to send if user hits Cancel button');
}

// analyze the PHP_AUTH_DIGEST variable
if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
    !isset($users[$data['username']]))
    die($wrong_credential_message);

// generate the valid response
$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

if ($data['response'] != $valid_response)
    die($wrong_credential_message);

// function to parse the http auth header
function http_digest_parse($txt){
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}
/*=============================================================================================*/
/*############################### Authenticated Access Security ###############################*/
/*=============================================================================================*/

if(!function_exists("php_cli")){
    function php_cli($cmd, $auto_reload = true){
        $result = trim(shell_exec($cmd));
        if($auto_reload) header('Location: '.$_SERVER['REQUEST_URI']);
        return $result;
    }
}

if(!function_exists("gpio_ref")){
    function gpio_ref($char, $GPIO){
        foreach($GPIO as $g){
            if($g['Char'] === $char)
                return $g;
        }
        return null;
    }
}

$GPIO = array(
	array('Char' => 'A', 'WiringPi' => '14', 'Header' => '23'),
    array('Char' => 'B', 'WiringPi' => '7', 'Header' => '7'),
    array('Char' => 'C', 'WiringPi' => '22', 'Header' => '31'),
    array('Char' => 'D', 'WiringPi' => '1', 'Header' => '12'),
    array('Char' => 'E', 'WiringPi' => '12', 'Header' => '19'),
    array('Char' => 'F', 'WiringPi' => '5', 'Header' => '18'),
    array('Char' => 'G', 'WiringPi' => '13', 'Header' => '21'),
    array('Char' => 'H', 'WiringPi' => '10', 'Header' => '24'),
    array('Char' => 'I', 'WiringPi' => '21', 'Header' => '29'),
    array('Char' => 'J', 'WiringPi' => '3', 'Header' => '15'),
	array('Char' => 'K', 'WiringPi' => '24', 'Header' => '35'),
    array('Char' => 'L', 'WiringPi' => '0', 'Header' => '11'),
    array('Char' => 'M', 'WiringPi' => '4', 'Header' => '16'),
    array('Char' => 'N', 'WiringPi' => '2', 'Header' => '13'),
    array('Char' => 'O', 'WiringPi' => '23', 'Header' => '33'),
    array('Char' => 'P', 'WiringPi' => '6', 'Header' => '22'),
    
    array('Char' => null, 'WiringPi' => '11', 'Header' => '26'),
	array('Char' => null, 'WiringPi' => '26', 'Header' => '32'),
	array('Char' => null, 'WiringPi' => '27', 'Header' => '36'),
);

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
            $header = gpio_ref(strtoupper($_POST['cmd']), $GPIO);
            $header = $header['WiringPi'];
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

$GPIO_STATUS = array();
foreach($GPIO as $g){
    if(!$g['Char'])continue;
    $GPIO_STATUS[$g['Char']] = php_cli("gpio read {$g['WiringPi']}", false);
}
?>

<center>
    <h1>^_^ Welcome to My Home Infrastructure Panel(HIP) ^_^</h1>
</center>
<hr/>
<div id='cmd_button'>
<?foreach($GPIO as $g):?>
    <?if(!$g['Char'])continue;?>
    <form method="post">
        <button class='<?=$GPIO_STATUS[$g['Char']] == "1" ? "on" : "off"?>' name='cmd' type='submit' value='<?=$g['Char']?>'><?=$g['Char']?>&nbsp;-&nbsp;<?=$GPIO_STATUS[$g['Char']] == "1" ? "ON" : "OFF"?></button>
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