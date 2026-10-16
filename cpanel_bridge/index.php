<?php

/**
 * SmartFun cPanel Subfolder Bridge
 * File ini diletakkan di: /home/pesonaas/public_html/smartfun/index.php
 * Berfungsi meneruskan request ke core repository tanpa mengganggu web utama public_html.
 */
define('LARAVEL_START', microtime(true));

// Load core index dari repository
require __DIR__.'/../../repositories/smartfun/public/index.php';
