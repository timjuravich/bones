<html>
    <link href="<?php echo $this->make_route('css/style.css') ?>" rel="stylesheet" type="text/css" />
    <body>
        <ul>
			<li><a href="<?php echo $this->make_route() ?>">Home</a></li>            
			<li><a href="<?php echo $this->make_route('hello/tim') ?>">Say Hi To Tim</a></li>
        </ul>
        <?php include($this->content); ?>
    </body>
</html>