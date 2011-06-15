<html>
    <link href="<?php echo $this->make_route('css/style.css') ?>" rel="stylesheet" type="text/css" />
    <body>
        <ul>
            <li> <a href="<?php echo $this->make_route() ?>">Home Page</a>
            <li> <a href="<?php echo $this->make_route('user/tim') ?>">Tim</a>
            <li> <a href="<?php echo $this->make_route('redirect') ?>">Redirect</a>
        </ul>
        <?php include($this->view_content); ?>
    </body>
</html>