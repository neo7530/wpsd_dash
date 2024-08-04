<?php
exec('sudo /usr/local/sbin/.wpsd-background-tasks > /dev/null 2>&1 &');
touch('/tmp/.last-index-bg-exec'); # for debugging
?>

