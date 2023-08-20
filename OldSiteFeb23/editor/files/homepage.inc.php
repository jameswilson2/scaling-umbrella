<?php

$content = <<<EOD
<h2>Files</h2>
<ul>
	<li><a href="files/">Edit Pages</a></li>
	<li><a href="files/includes.php">Edit Includes</a></li>
	<img src="presentation/rebuild.gif" alt="Rebuild" title="Rebuild" class="minicon" width="16" height="14"> <a href="files/index.php?action=rebuild&amp;location=">Apply Changes to Includes</a>
</ul>
EOD;

echo $content;

?>