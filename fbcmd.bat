@ECHO OFF
SET FBDIR=%FBCMD%
IF "%FBDIR%"=="" SET FBDIR=c:\fbcmd
php %FBDIR%\fbcmd.php %*
