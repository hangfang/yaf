#!/bin/sh

cd /home/wwwroot/yaf/
/usr/local/php7/bin/php cli.php request_uri="/jobnew/dlt" >> ./application/logs/Job/dlt.log