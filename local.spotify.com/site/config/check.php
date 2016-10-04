<?php

// Locale
print "Locale: ".locale_get_default().PHP_EOL;

// Imagick
print "Imagick: ";
if (!extension_loaded('imagick'))
    echo "Not installed."
else
    echo 'Installed.';
echo PHP_EOL;

// Other
?>