set path=%path%;C:\Program Files (x86)\Windows Installer XML v3.5\bin
del Install-fbcmd.msi
candle fbcmd.wxs 
light -ext WixUIExtension -cultures:en-US fbcmd.wixobj -out Install-fbcmd.msi
del *.wixobj
del *.wixpdb
php copymsi.php

