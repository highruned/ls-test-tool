<?php
	$is_php4 = version_compare(PHP_VERSION, '5.0.0', '<');

	define('LS_INST_PHP4', $is_php4);
	
	function apache_header_exists()
	{
		foreach ($_SERVER as $value)
		{
			if (strpos(strtolower($value), 'apache') !== false)
				return true;
		}

		return false;
	}
	
	function short_tags_allowed()
	{
		return ini_get('short_open_tag');
	}

	function check_requirements()
	{
		$install_path = str_replace("\\", "/", @realpath(dirname(__FILE__)));

		$result = array();
		
		$result['Apache Web Server'] = function_exists('apache_getenv') || apache_header_exists();
		$result['PHP 5.2.5 or higher'] = version_compare(PHP_VERSION , "5.2.5", ">=");
		$result['PHP CURL library'] = function_exists('curl_init');
		$result['PHP OpenSSL library'] = function_exists('openssl_open');
		$result['PHP Mcrypt library'] = function_exists('mcrypt_encrypt');
		$result['PHP MySQL functions'] = function_exists('mysql_connect');
		$result['PHP Multibyte String functions'] = function_exists('mb_convert_encoding');
		
		$result['Permissions for PHP to write to the installation directory'] = is_writable($install_path);
		
		if (!ini_get('safe_mode'))
			$result['Safe Mode is disabled '] = true;
		else
			$result['PHP Safe Mode detected '] = false;

		return $result;
	}
	
	function zend_optimizer_loaded()
	{
		$extensions = get_loaded_extensions(true);
		foreach ($extensions as $extension)
		{
			if (strpos($extension, 'Zend Optimizer') !== false)
				return true;
		}
		
		return false;
	}

	function inocube_loaded()
	{
		$extensions = get_loaded_extensions(true);
		foreach ($extensions as $extension)
		{
			if (strpos($extension, 'ionCube') !== false)
				return true;
		}
		
		return false;
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>LemonStand System Requirements Evaluation Tool</title>
		<link rel="stylesheet" href="resources/css/style.css" type="text/css"/>
	</head>
	<body>
		<div id="header">
			<div class="wrapper">
				<h1>LemonStand System Requirements Evaluation Tool</h1>
			</div>
		</div>

		<?php	if (LS_INST_PHP4): ?>
			<div id="page_header">
				<div class="wrapper">
					<h2>Welcome to LemonStand!</h2>
				</div>
			</div>

			<div id="content">
				<div class="wrapper">
					<h3 class="negative">We detected that your server is using PHP 4. We are sorry, but LemonStand requires PHP 5.</h3>
					<p>To install the application you need to upgrade this server to run PHP 5 and all other required libraries, or restart the installation on another server which meets all of the server requirements.</p>
					<p>To install LemonStand your server must meet the following requirements:</p>

					<ul class="bool_list bullets">
						<li>PHP 5.2.5 or higher</li>
						<li>PHP CURL library</li>
						<li>PHP OpenSSL library</li>
						<li>PHP Mcrypt library</li>
						<li>PHP MySQL functions</li>
						<li>PHP Multibyte String functions</li>
						<li>Permissions for PHP to write to the installation directory</li>
					</ul>
				</div>
			</div>		
		<?php else: ?>
			<div id="page_header">
				<div class="wrapper">
					<h2>Welcome to LemonStand!</h2>
				</div>
			</div>
			
			<div id="content">
				<div class="wrapper">
					<?php $requirements = check_requirements(); ?>
					<h3>Checking the system requirements...</h3>

					<ul class="bool_list bottom_offset">
					<?php
						$requirements = check_requirements();
						$requirements_met = true;

						foreach ($requirements as $name=>$met):
							if (!$met)
								$requirements_met = false;
					?>
						<li class="<?php echo $met ? 'positive' : 'negative' ?>"><?php echo $name ?></li>
					<?php endforeach ?>
					</ul>
					
					<h3>Checking PHP configuration...</h3>

					<ul class="bool_list bottom_offset">
					<?php 
						if (!short_tags_allowed()): 
						$requirements_met = false;
					?>
						<li class="negative">
							Short PHP tags are not allowed
							<span class="comment">Please enable short PHP tags in the global PHP configuration file (php.ini) using the <strong>short_open_tag=on</strong> declaration.</span>
						</li>
					<?php else: ?>
						<li class="positive">Short PHP tags allowed</li>
					<?php endif ?>
					</ul>

					<?php if ($requirements_met): ?>
						<h3 class="positive">Requirements are met!</h3>
						<p>Congratulations! Your system meets the requirements and you can install LemonStand on this server.</p>
						
						<h4>Supported loaders</h4>
						
						<p>If you are going to install a developer copy of LemonStand, you need a PHP loader to be installed on the server in order to execute encrypted PHP files. There are two loaders available: <strong>Zend Optimizer</strong> and <strong>IonCube Loader</strong>.</p>
						
						<?php 
							$ic_loaded = inocube_loaded();
							$zend_loaded = zend_optimizer_loaded();
							if ($ic_loaded && $zend_loaded):
						?>
							<p>We detected that both Zend Optimizer and IonCube Loader libraries are presented in your system. This means that you can select any encoder type in the developer license request form.</p>
						<?php elseif ($zend_loaded): ?>
							<p>We detected that Zend Optimizer library is presented in your system. This means that you can select the <strong>ZendGuard</strong> encoder in the developer license request form.</p>
						<?php elseif ($ic_loaded): ?>
							<p>We detected that IonCube Loader library is presented in your system. This means that you can select the <strong>IonCube</strong> encoder in the developer license request form.</p>
						<?php else: ?>
							<p>
								No loaders were detected in your system. Your PHP version is <strong><?php echo phpversion() ?></strong>.
								
								<?php if (version_compare(PHP_VERSION, '5.3.0', '<')): ?>
									Both Zend Optimizer and IonCube Loader support this PHP version. Please download any loader using the links below. After downloading a loader, follow the instructions to install in the loader archive. </p>
									<ul class="bullets">
										<li><a href="http://www.zend.com/en/products/guard/downloads" target="_blank">Zend Optimizer</a></li>
										<li><a href="http://www.ioncube.com/loaders.php" target="_blank">IonCube Loader</a> (use OS X loader for Mac OS)</li>
									</ul>
								<?php else: ?>
									Only IonCube Loader supports this PHP version at the moment. You can download the loader using the link below. After downloading the loader, follow the instructions to install in the loader archive.</p>
									<ul class="bullets">
										<li><a href="http://www.ioncube.com/loaders.php" target="_blank">IonCube Loader</a> (use OS X loader for Mac OS)</li>
									</ul>
								<?php endif ?>
						<?php endif ?>
						
					<?php else: ?>
						<h3 class="negative">Requirements not met</h3>
						<p>We are sorry. Your system does not meet the minimum requirements for the installation.</p>
					<?php endif ?>
				</div>
			</div>
		<?php endif ?>
		

		<div id="footer">
			<div class="wrapper">
				<p>Copyright &copy; <?= date('Y', time()) ?> - All Rights Reserved</p>
				<p class="right">LemonStand is a product by <a href="http://www.limewheel.com">Limewheel Creative</a></p>
				<div class="clear"></div>
			</div>
		</div>
	</body>
</html>