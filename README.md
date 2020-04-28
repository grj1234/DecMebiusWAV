# DecMebiusWAV Version 0.1

This tool is to decrypt XOR-encrypted WAV files which used for some games developed by Studio Mebius and related developers (e.g. Studio Ring).  

## Usage
Usage: `DecMebiusWAV <Input file> <Output file> [XOR key]`  
  
If you want to output to stdout, specify - as output file. (Input from stdin is not supported)  
You can't use wildcard for input file and output file.  
You can specify the hex bytes of binary as XOR key. (Example: "B06FA4D7")  
When you don't specify the XOR key, DecMebiusWAV will load external binary file as XOR key.  
The file name of key file must be "(name).(ext)key" (for a single file), or ".(ext)key" (for the whole folder).  
If you don't specify the key and the external key not found, DecMebiusWAV will use default XOR key. (Default key: "AA")  

## Notes
- You have to install PHP and modify the PATH environment variable to include the PHP directory to run this tool.
- I checked this tool can be run on PHP 7.0.7, but I didn't check whether can run on the other versions of PHP or not.
	- The newer version of PHP also can run this tool, probably.
- DecMebiusWAV will overwrite the output file without confirmation even if it exists.

## Contact me
- HCS Forum: [grj1234](https://hcs64.com/mboard/forum.php?userinfo=3202)
- E-mail: grj1234@protonmail.com
