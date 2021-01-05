<?php
$globals=array_keys( $GLOBALS ); 
//phpinfo(INFO_MODULES);
//print_r($_SERVER);

$data = [
    "CONFIGURATION" => [
        "HOST URL" 		=> $_SERVER["HTTP_HOST"],
        "DOCKER IMAGE" 	=> "PHP-FPM-NGINX-7.3",
        "TIME ZONE"  	=> date_default_timezone_get(),
        "DATE / TIME"   => $date = date('m/d/Y h:i:s a', time()),
    ],

	"CONTAINER INFO" => [
        "NAME" 			=> gethostbyaddr($_SERVER["SERVER_ADDR"]),
        "SYSTEM"        => php_uname(),
        "CPU"        	=> $cpu_result = shell_exec("cat /proc/cpuinfo | grep model | cut -d':' -f2 | cut -d'k' -f1"),
        "MEMORY"      	=> $mem_result = shell_exec("cat /proc/meminfo | grep MemTotal | cut -d':' -f2 | cut -d'k' -f1"),
        "DISK"   		=> $stat['hdd_total'] = round(disk_total_space("/") / 1024 / 1024/ 1024, 2), 
        "IP" 			=> $_SERVER["SERVER_ADDR"],
        "PORT" 			=> $_SERVER["SERVER_PORT"],
        "REMOTE PORT" 	=> $_SERVER["REMOTE_PORT"],
    ],

    "SERVICES INFO" => [
        "PHP VERSION" 			=> $_SERVER['PHP_VERSION'],
        "NGINX VERSION" 		=> $_SERVER['SERVER_SOFTWARE'],
        "SUPERVISOR VERSION" 	=> $cpu_result = shell_exec("supervisord --version | cut -d':' -f2 | cut -d'k' -f1"),
    ],
];
?>

<html>
	<head>
	  <title>Devops Tools</title>
	    <meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
           		<style type="text/css">
					td, th { border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px; } 
					h1 { font-size: 150%; }
					h2 { font-size: 125%; }
					.e { background-color: #ccccff; font-weight: bold; color: #000; width:300px; }
					.h { background-color: #9999cc; font-weight: bold; color: #000; }
					.v { background-color: #ddd; max-width: 300px; overflow-x: auto; }
					.v i { color: #777; }
					.vr { background-color: #cccccc; text-align: right; color: #000; white-space: nowrap; }
					.b { font-weight:bold; }
					.white, .white a { color:#fff; } 	
					hr { width: 934px; background-color: #cccccc; border: 0px; height: 1px; color: #000; }
					.meta, .small { font-size: 75%; }
					.meta { margin: 2em 0; }
					.meta a, th a { padding: 10px; white-space:nowrap; }
				</style>
           		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	</head>

	<body>
		<div class="container">
			<h2 class="text-center "></h2>
			<img src="https://files.readme.io/83c5e57-cloudsmith-docker-documentation-banner.png" class="img-fluid" alt="Responsive image">
			<h3>
				Container Id: <?php echo gethostbyaddr($_SERVER["SERVER_ADDR"]); ?>
			</h3>
    <?php foreach ($data as $title => $values): ?>
        <?php if (!$values): continue; endif; ?>
        <div class="panel">
            <table class="table">
            <thead class="thead-dark">
                <tr class="info">
                    <th colspan="2"><?= $title ?></th>
                </tr>
                <?php foreach ($values as $key => $value): ?>
                <tr>
                    <td style="width: 25%"><?= $key ?></td>
                    <td style="width: 75%"><?= $value ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
 	<?php endforeach; ?>
    <div type="button">
        <a href="?EXTENSIONS&FUNCTIONS&CONSTANTS&GLOBALS&INI" type="button" class="btn btn-primary">ALL</a>
        <a <?php echo isset($_GET['INI'])?'class="btn btn-primary"':'' ?> href="?INI" type="button" class="btn btn-primary">INI</a>
        <a <?php echo isset($_GET['EXTENSIONS'])?'class="btn btn-primary"':'' ?> href="?EXTENSIONS" type="button" class="btn btn-primary">Extensions</a>
        <a <?php echo isset($_GET['FUNCTIONS'])?'class="btn btn-primary"':'' ?> href="?FUNCTIONS" type="button" class="btn btn-primary">Functions</a>
        <a <?php echo isset($_GET['CONSTANTS'])?'class="btn btn-primary"':'' ?> href="?CONSTANTS" type="button" class="btn btn-primary">Constants</a>
        <a <?php echo isset($_GET['GLOBALS'])?'class="btn btn-primary"':'' ?> href="?GLOBALS" type="button" class="btn btn-primary">Globals</a>
</div>
<?php 

echo "<br>";
if ( isset($_GET['INI']) && $ini=ini_get_all() ) { 
  	ksort($ini);  print_table($ini,array('Directive','Local Value','Master Value','Access'),false); 
  	echo '<h2>access level legend</h2>';
  	print_table(array('Entry can be set in user scripts, ini_set()'=>INI_USER,'Entry can be set in php.ini, .htaccess, httpd.conf'=>INI_PERDIR,
     			'Entry can be set in php.ini or httpd.conf'=>INI_SYSTEM,'<div style="width:865px">Entry can be set anywhere</div>'=>INI_ALL ));
}

if ( isset($_GET['EXTENSIONS']) && $extensions=get_loaded_extensions(true) ) { 
	 natcasesort( $extensions); print_table($extensions,false,true); 
}

if ( isset($_GET['FUNCTIONS']) && $functions=get_defined_functions() ) { 
	 natcasesort( $functions['internal']); print_table($functions['internal'],false,true); 
}

if ( isset($_GET['CONSTANTS']) && $constants=get_defined_constants(true) ) { 
	ksort( $constants); foreach ( $constants as $key=>$value) { if (!empty($value)) { ksort( $value); echo '<h2 id="constants-',$key,'">Constants (',$key,')</h2>'; print_table($value); } } 
}

if ( isset($_GET['GLOBALS']) ) { 
	if (0) { $_SERVER; $_ENV; $_SESSION; $_COOKIE; $_GET; $_POST; $_REQUEST; $_FILES; }	// PHP 5.4+ JIT
	$order=array_flip(array('_SERVER','_ENV','_COOKIE','_GET','_POST','_REQUEST','_FILES'));
	foreach ( $order as $key=>$ignore ) { if ( isset($GLOBALS[$key]) ) { echo '<h2 id="',$key,'">$',$key,'</h2>';  if ( empty($GLOBALS[$key]) ) { echo '<hr>'; } else { print_table( $GLOBALS[$key]); } } }
	natcasesort($globals); $globals=array_flip($globals); unset( $globals['GLOBALS'] );  
	foreach ( $globals as $key=>$ignore ) { if ( !isset($order[$key]) ) { echo '<h2 id="',$key,'">$',$key,'</h2>';  if ( empty($GLOBALS[$key]) ) { echo '<hr>'; } else { print_table( $GLOBALS[$key]); } } }
}
?>
<?php

function print_table( $array, $headers=false, $formatkeys=false, $formatnumeric=false ) { 
	if ( empty($array) || !is_array($array) ) { return; } 
  	echo '<table>';
  	if ( !empty($headers) ) { 
  		if ( !is_array( $headers) ) { $headers=array_keys( reset( $array) ); }
  		echo '<tr class="h">'; foreach ( $headers as $value) { echo '<th>',$value,'</th>'; } echo '</tr>';  			
  	}
  	foreach ( $array as $key=>$value ) { 
    		echo '<tr>';
    		if ( !is_numeric( $key) || !$formatkeys ) { echo '<td class="e">',($formatkeys?ucwords(str_replace('_',' ',$key)):$key),'</td>'; }
    		if ( is_array($value) ) { foreach ($value as $column) { echo '<td class="v">',format_special($column,$formatnumeric),'</td>'; } }
    		else { echo '<td class="v">',format_special($value,$formatnumeric),'</td>'; } 
    		echo '</tr>';
	}
 	echo '</table>';
}

function format_special( $value, $formatnumeric ) { 
	if ( is_array($value) ) { $value='<i>array</i>'; }
	elseif ( is_object($value) ) { $value='<i>object</i>'; }
    	elseif ( $value===true ) { $value='<i>true</i>'; }
    	elseif ( $value===false ) { $value='<i>false</i>'; }
    	elseif ( $value===NULL ) { $value='<i>null</i>'; }
    	elseif ( $value===0 || $value===0.0 || $value==='0' ) { $value='0'; }    	
    	elseif ( empty($value) ) { $value='<i>no value</i>'; }
	elseif ( is_string($value) && strlen($value)>50 ) { $value=implode('&#8203;',str_split($value,45)); }
	elseif ( $formatnumeric && is_numeric($value) ) { 
        			if ( $value>1048576 ) { $value=round($value/1048576,1).'M'; }
        			elseif ( is_float($value) ) { $value=round($value,1); }
      	}
	return $value;
}

$output = shell_exec('cat /app/.env');
echo "<pre>$output</pre>";

?>

		</div>
	</body>
</html>

