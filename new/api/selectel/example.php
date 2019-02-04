<?php

error_reporting(E_ALL);
ini_set('display_errors','On');    
ini_set('default_charset', 'utf-8');

include ("SelectelStorage.php");
include ("SelectelContainer.php");
include ("SelectelStorageException.php");
include ("SCurl.php");

echo '<pre>';


try {
	$selectelStorage = new SelectelStorage("54917_chatpics", "FnmTBSl3aU");


/*
	echo "\n\nCreate Container:\n";
	$container = $selectelStorage->createContainer('selectel', array("X-Container-Meta-Type: public"));
	print_r($container->getInfo());
*/
	
//	echo "Containers list\n";
	$containerList = $selectelStorage->listContainers();
//	print_r($containerList);

	echo "\n\nContainer Info:\n";
	$cInfo = $selectelStorage->getContainer($containerList[0])->getInfo();
	print_r($cInfo);


//	echo "\n\nCreate directory:\n";
    $container = $selectelStorage->getContainer($containerList[0]);
//	$container->createDirectory('php/test');


/*
	echo "\n\nDirectories:\n";
	$dirList = $container->listFiles($limit = 10000, $marker = null, $prefix = null, $path = "");
	print_r($dirList);
*/




/******************************/
	echo "\n\nPutting File:\n";
	
	$file = '../2_13-06-2016_18-22-32.jpg';
	$userId = 5555;
	
    $fileInfo = pathinfo($file);
	$newName = $userId.'-'.urlencode($fileInfo['filename']);
	$ext = $fileInfo['extension'];

//	$res = $container->putFile(__DIR__.'/'.$file, $newName.'.'.$ext);
//	print_r($res);	
/******************************/

echo $selectelStorage->url.$containerList['0'].'/'.$newName.'.'.$ext;

	echo "\n\nFiles in directory:\n";
	$fileList = $container->listFiles($limit = 10000, $marker = null, $prefix = null, $path = '');
	print_r($fileList);

	echo "\n\nFile info:\n";
	foreach ($fileList as $file)
	{
    	$fileInfo = $container->getFileInfo($file);
    	if ($fileInfo['bytes'] > 0)
        {	
            $fileInfo['link'] = $selectelStorage->url.$containerList['0'].'/'.$fileInfo['name'];
        	print_r($fileInfo);
        }
	}

/*
	echo "\n\nGetting file (base64):\n";
	$file = $container->getFile($fileList[0]);
	$file['content'] = base64_encode($file['content']);
	print_r($file);
*/

/*
	echo "\n\nCopy: \n";
	$copyRes = $container->copy('example.php', 'php/test/Examples_copy.php5');
	print_r($copyRes);
*/

/*
    echo "\n\nDelete: \n";
    $deleteRes = $container->delete('example.php');
    print_r($deleteRes);
    $deleteRes = $container->delete('php');
    print_r($deleteRes);
*/
	
}
catch (Exception $e)
{
	print_r($e->getTrace());
}

