<?php
exec('env', $env_output);
echo "Environment Variables:\n";
echo implode("\n", $env_output);
?>
