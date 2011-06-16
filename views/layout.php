<html>
    <link href="<?php echo $this->make_route('css/style.css') ?>" rel="stylesheet" type="text/css" />
    <body>
        <ul>
            <li><a href="<?php echo $this->make_route() ?>">Home Page</a></li>
            <li><a href="<?php echo $this->make_route('user/tim') ?>">Tim</a></li>
            <li><a href="<?php echo $this->make_route('redirect') ?>">Redirect To Tim</a></li>
        </ul>
        <?php include($this->view_content); ?>
    </body>
</html>