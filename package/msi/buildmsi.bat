set path=%path%;C:\Program Files\Windows Installer XML v3\bin
del Install*.msi
candle fbcmd.wxs 
light -ext WixUIExtension -cultures:en-US fbcmd.wixobj
del fbcmd.wixobj
del fbcmd.wixpdb
php rename_msi.php
