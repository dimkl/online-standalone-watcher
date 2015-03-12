<html>
<head>
<base href="/online-watcher/">
</head>
<body>
<?php
foreach ($changes as $changed): ?>
<div class="unwatched-series">
	<div>
	<span>Σειρά</span>
	<span><?php
    echo $changed->series ?></span>
	</div>
	<div>
	<span>Σεζόν</span>
	<span><?php
    echo $changed->season ?></span>
	</div>
	<div>
	<span>Επεισόδιο</span>
	<span><?php
    echo $changed->episode ?></span>
	</div>
	<a href="<?php echo 'index.php?task=watch&series='.$changed->series.'&season='.$changed->season .'&episode='.$changed->episode?>"> Watched </a>
	<?php foreach($changed->links as $index=>$link):?>
    <a href="<?php echo $link?>"> Torrent link-<?php echo $index?></a>
	<?php endforeach; ?>
</div>
<?php
endforeach; ?>
</body>
</html>