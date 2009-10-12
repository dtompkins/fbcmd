del fbcmd.zip
mkdir fbcmd
cd fbcmd
xcopy ..\..\..\* . /exclude:..\ignore.txt
xcopy ..\..\..\support .\support\ /s /exclude:..\ignore.txt
xcopy ..\..\..\facebook .\facebook\ /s /exclude:..\ignore.txt
cd ..
zip -r fbcmd.zip fbcmd
rmdir fbcmd /s /q
php copyzip.php
