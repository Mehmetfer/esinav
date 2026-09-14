<?php
require_once '/opt/metro/infra/nginx/sinav-metro/php-sinav/includes/auth.php';
require_once '/opt/metro/infra/nginx/sinav-metro/php-sinav/includes/dil.php';
require_once '/opt/metro/infra/nginx/sinav-metro/php-sinav/includes/education.php';
echo "user_education_groups: " . (function_exists('user_education_groups') ? 'EXISTS' : 'NOT FOUND') . "\n";
echo "education_categories: " . (function_exists('education_categories') ? 'EXISTS' : 'NOT FOUND') . "\n";
echo "db: " . (function_exists('db') ? 'EXISTS' : 'NOT FOUND') . "\n";
echo "e(): " . (function_exists('e') ? 'EXISTS' : 'NOT FOUND') . "\n";

