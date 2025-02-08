<?php

$title = isset($title) ? $title : '';
$description = isset($description) ? $description : '';
$og_title = isset($og_title) ?? $title;
$og_description = isset($og_description) ?? $description;
$og_url = isset($og_url) ?? '';
?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?php echo $title; ?></title>
  <meta name="description" content="<?php echo $description; ?>">
  <meta name="author" content="Michal Slepko @ Rootscope">

  <link rel="canonical" href="https://phpserialize.com">
  <meta property="og:title" content="<?php echo $og_title; ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo $og_url; ?>">
  <meta property="og:description" content="<?php echo $og_description; ?>">
  <meta property="og:image" content="image.png">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/clearfix.css">
  <link rel="stylesheet" href="/styles.css">

</head>

<body>