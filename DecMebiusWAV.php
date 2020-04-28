<?php
// -----------------------------------------
// DecMebiusWAV Version 0.1 by grj1234
// https://github.com/grj1234/DecMebiusWAV
// -----------------------------------------
setlocale(LC_ALL,'');
ini_set('error_log','php://stderr');
$version="0.1";
$debugmode=false;
$decryption_buffer_size=1048576;
$message_handle=fopen("php://stderr","wb");
fwrite($message_handle,
	"DecMebiusWAV Version ".$version." by grj1234".PHP_EOL.
	"https://github.com/grj1234/DecMebiusWAV".PHP_EOL.
	PHP_EOL
);
fwrite($message_handle,$debugmode?("DEBUG MODE ENABLED".PHP_EOL):"");
if($argc<3) {
	fwrite($message_handle,
		"Usage: DecMebiusWAV <Input file> <Output file> [XOR key]".PHP_EOL.
		PHP_EOL.
		"This tool is to decrypt XOR-encrypted WAV files which used in some games developed by Studio Mebius and related developers (e.g. Studio Ring).".PHP_EOL.
		"If you want to output to stdout, specify - as output file. (Input from stdin is not supported)".PHP_EOL.
		"You can't use wildcard for input file and output file.".PHP_EOL.
		"DecMebiusWAV will overwrite the output file without confirmation even if it exists.".PHP_EOL.
		'You can specify the hex bytes of binary as XOR key. (Example: "B06FA4D7")'.PHP_EOL.
		"When you don't specify the XOR key, DecMebiusWAV will load external binary file as XOR key.".PHP_EOL.
		'The file name of key file must be "(name).(ext)key" (for a single file), or ".(ext)key" (for the whole folder).'.PHP_EOL.
		"If you don't specify the key and the external key not found, DecMebiusWAV will use default XOR key. ".'(Default key: "AA")'.PHP_EOL
	);
	fclose($message_handle);
	exit;
}
$in_file=$argv[1];
$out_type=($argv[2]=="-");
$out_file=$out_type?'php://stdout':$argv[2];
if($in_file=="-") {
	fwrite($message_handle,"Error: Input from stdin is not supported".PHP_EOL);
	fclose($message_handle);
	exit(1);
}
if($in_file==$out_file) {
	fwrite($message_handle,"Error: Same file name specified for input and output.".PHP_EOL);
	fclose($message_handle);
	exit(1);
}
if(!file_exists($in_file)) {
	fwrite($message_handle,"Error: Input file not found".PHP_EOL);
	fclose($message_handle);
	exit(1);
}
$in_pathinfo=pathinfo($in_file);
$key_ext=isset($in_pathinfo{"extension"})?(".".$in_pathinfo{"extension"}."key"):".key";
if(isset($argv[3])) {
	// XOR key specified as argument
	$xor_key=hex2bin($argv[3]);
	fwrite($message_handle,"Using XOR key specified as argument".PHP_EOL);
} else if(file_exists($in_file."key")) {
	// load key file (for a single file)
	$xor_key=file_get_contents($in_file."key");
	if($xor_key===false){
		fwrite($message_handle,"Error: External XOR key file for a single file found, but loading failed".PHP_EOL);
		fclose($message_handle);
		exit(1);
	}
	fwrite($message_handle,"Using external XOR key file for a single file".PHP_EOL);
} else if(file_exists($in_pathinfo{"dirname"}."\\".$key_ext)) {
	// load key file (for the whole folder)
	$xor_key=file_get_contents($in_pathinfo{"dirname"}."\\".$key_ext);
	if($xor_key===false){
		fwrite($message_handle,"Error: External XOR key file for the whole folder found, but loading failed".PHP_EOL);
		fclose($message_handle);
		exit(1);
	}
	fwrite($message_handle,"Using external XOR key file for the whole folder".PHP_EOL);
} else {
	// key file not found (0xAA)
	$xor_key=hex2bin('AA');
	fwrite($message_handle,'XOR key not specified and external key file not found, using default XOR key'.PHP_EOL);
}
fwrite($message_handle,'XOR key: '.strtoupper(bin2hex($xor_key)).PHP_EOL);
fwrite($message_handle,'Input file: '.$in_file.PHP_EOL);
fwrite($message_handle,'Output file: '.(($out_type)?'(stdout)':$argv[2]).PHP_EOL);

if(filesize($in_file)<12) {
	fwrite($message_handle,"Error: Input file is not WAV file".PHP_EOL);
	fclose($message_handle);
	exit(1);
}
$in_handle=@fopen($in_file,"rb");
if(!is_resource($in_handle)) {
	fwrite($message_handle,"Error: Failed to open input file".PHP_EOL);
	fclose($message_handle);
	exit(1);
}
$in_riff_header=fread($in_handle,12);
$in_riff_header_parsed=unpack("a4file_type/Vsize/a4riff_type",$in_riff_header);
if(($in_riff_header_parsed['file_type']!="RIFF")||($in_riff_header_parsed['riff_type']!="WAVE")) {
	fwrite($message_handle,"Error: Input file is not WAV file".PHP_EOL);
	fclose($in_handle);
	fclose($message_handle);
	exit(1);
}
if(filesize($in_file)!=($in_riff_header_parsed['size']+8)) {
	fwrite($message_handle,"Warning: The information of file size included in the RIFF header did not match the actual file size. The input file might be corrupted.");
}
$out_handle=@fopen($out_file,"wb");
if(!is_resource($out_handle)) {
	fwrite($message_handle,"Error: Failed to open output file".PHP_EOL);
	fclose($in_handle);
	fclose($message_handle);
	exit(1);
}
fwrite($out_handle,$in_riff_header);
for(;;) {
	$chunk_info=fread($in_handle,8);
	$chunk_info_parsed=unpack("a4chunk_type/Vchunk_size",$chunk_info);
	if($chunk_info_parsed['chunk_type']=='data') {
		fwrite($out_handle,$chunk_info);
		break;
	} else {
		fwrite($out_handle,$chunk_info.fread($in_handle,$chunk_info_parsed['chunk_size']));
	}
}
$encrypted_offset=ftell($in_handle);
for(;ftell($in_handle)!=filesize($in_file);){
	$encrypted_data_offset=ftell($in_handle)-$encrypted_offset;
	$encrypted_data=fread($in_handle,$decryption_buffer_size);
	$encrypted_data_size=strlen($encrypted_data);
	$xor_key_for_buffer=substr(str_repeat($xor_key,(ceil($encrypted_data_size/strlen($xor_key))+1)),($encrypted_data_offset%strlen($xor_key)),$encrypted_data_size);
	$decrypted_data=($encrypted_data^$xor_key_for_buffer);
	unset($encrypted_data);
	unset($xor_key_for_buffer);
	fwrite($message_handle,$debugmode?("[DEBUG] input_file_ptr:".ftell($in_handle).", buffer_size:".strlen($decrypted_data).", xor_key_offset:".strval($encrypted_data_offset%strlen($xor_key)).PHP_EOL):"");
	fwrite($out_handle,$decrypted_data);
	unset($decrypted_data);
}
fclose($in_handle);
fclose($out_handle);
fclose($message_handle);
exit;
