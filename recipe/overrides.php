<?php

use function Deployer\parse;
use function Deployer\run;

function relative_symlink(string $source, string $dest)
{
    $source = parse($source);
    $dest   = parse($dest);

    // Get common path
    $splitSource   = explode('/', $source);
    $uncommonPaths = array_diff($splitSource, explode('/', $dest));
    $uncommonIndex = array_search(reset($uncommonPaths), $splitSource, true);
    $commonPath    = implode('/', array_splice($splitSource, 0, $uncommonIndex));

    // Create source path relative from destination
    $relativeSrc  = trim(str_replace($commonPath, '', $source), '/');
    $relativeDest = trim(str_replace($commonPath, '', $dest), '/');
    $source       = str_repeat('../', substr_count($relativeDest, '/')) . $relativeSrc;

    run(sprintf('cd %s && {{bin/symlink}} %s %s', dirname($dest), $source, basename($dest)));
}

require_once __DIR__ . '/overrides/release.php';
require_once __DIR__ . '/overrides/shared.php';
require_once __DIR__ . '/overrides/symlink.php';
require_once __DIR__ . '/overrides/rollback.php';
require_once __DIR__ . '/overrides/rsync.php';
require_once __DIR__ . '/overrides/cleanup.php';
