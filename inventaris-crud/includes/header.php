<?php
require_once __DIR__ . '/functions.php';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>CRUD Inventaris</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <div class="topbar">
    <div class="brand">
      <span class="eyebrow">Tugas Rutin 8</span>
      <h1>CRUD <span>Inventaris</span></h1>
    </div>
    <a href="index.php" class="btn btn-secondary">&larr; Daftar Produk</a>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>">
      <?= e($flash['message']) ?>
    </div>
  <?php endif; ?>
