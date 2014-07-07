<?php
header("Content-Type: text/plain; charset=UTF-8");

echo "アクセス先にはファイルが存在しませんでした。\n\n";
echo $error->getMessage();
