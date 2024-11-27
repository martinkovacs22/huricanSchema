<?php
// scripts/copy-files.php
$sourceBasePath = __DIR__ . '/../src'; // Forrás mappa
$destinationVendorPath = __DIR__ . '/../vendor/hurican/BaseReq'; // Cél mappa

// Ellenőrzi, hogy léteznek-e a szükséges könyvtárak, ha nem, akkor létrehozza
if (!is_dir($destinationVendorPath)) {
    mkdir($destinationVendorPath, 0755, true);
}

// Mappák és fájlok másolása
function copyFolder($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (($file = readdir($dir)) !== false) {
        if (($file != '.') && ($file != '..')) {
            $srcFile = $src . '/' . $file;
            $dstFile = $dst . '/' . $file;
            if (is_dir($srcFile)) {
                copyFolder($srcFile, $dstFile);
            } else {
                copy($srcFile, $dstFile);
            }
        }
    }
    closedir($dir);
}

// Fájlok másolása a megfelelő könyvtárba
copyFolder($sourceBasePath, $destinationVendorPath);
echo "Files have been copied to vendor/hurican successfully.\n";
