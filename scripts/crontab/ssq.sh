#!/bin/sh

cd /home/wwwroot/yaf/
/usr/local/php7/bin/php cli.php request_uri="/weapp/jobnew/ssq" >> ./logs/job/ssq.log