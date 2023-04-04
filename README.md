__This tool has many bugs and vulnerabilities. I recommend to use [vgmstream](https://vgmstream.org/) instead.__  

# DecMebiusWAV Version 0.3

This tool decrypts Mebius engine's XOR-encrypted WAV files (also called Mebius KOE files by GARbro), which used in some games developed by Studio Mebius and related developers (e.g. Studio Ring).  

## Usage
Usage: `DecMebiusWAV <Input file> <Output file> [XOR key]`  
  
If you want to output to stdout, specify - as output file. (Input from stdin is not supported)  
You can't use wildcard for input file and output file.  
This tool needs the XOR key to decrypt, so you need to specify the key by argument or external file.  
To specify XOR key by argument, you need to use the hex-encoded binary string. (Example: "B06FA4D7")  
When you don't specify the XOR key by argument, DecMebiusWAV will load external binary file as XOR key.  
The file name of key file must be "(name).(ext)key" (for a single file), or ".(ext)key" (for the whole folder).  

## Notes
- You have to install PHP and modify the PATH environment variable to include the PHP directory to run this tool.
- I checked this tool can be run on PHP 7.0, but I didn't check whether can run on the other versions of PHP or not.
	- The newer version of PHP also can run this tool, probably.
- DecMebiusWAV will overwrite the output file without confirmation even if it exists.

## License
All source code in this repository is released under the [NYSL Version 0.9982](http://www.kmonos.net/nysl/).  
File "LICENSE_ja" contains the license text of NYSL, written in Japanese. File "LICENSE" contains the unofficial English translation of NYSL.

## Changelog
### Version 0.3 (2023-04-04)
- Fixed that DecMebiusWAV.php script fails to run on PHP 8.0+ due to curly brackets for accessing array elements
- Fixed that DecMebiusWAV.php script may cause out of memory error when searching RIFF chunks
- Deleted the default key
- Improved the translation of license text

### Version 0.2 (2021-01-19)
- Fixed that DecMebiusWAV.php script fails to open the key file for the whole folder
- Added license text

### Version 0.1 (2020-04-28)
- Initial release

## Contact me
- HCS Forum: [grj1234](https://hcs64.com/mboard/forum.php?userinfo=3202)
- Twitter: [@user_88810887](https://twitter.com/user_88810887)
- Fediverse (Misskey): [@grj1234@misskey.04.si](https://misskey.04.si/@grj1234)
- E-mail: grj1234@protonmail.com
