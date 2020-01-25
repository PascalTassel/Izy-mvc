<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Izy-mvc - <?php echo htmlspecialchars($title); ?></title>
    <?php
    if(isset($description))
    {
      echo '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
    }
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php
    if(isset($metas))
    {
      foreach($metas as $meta)
      {
        echo $meta . "\n";
      }
    }
    ?>
    <link href="<?php echo \app\helpers\Url_helper::img_url("favicon-32.png"); ?>" sizes="32x32" rel="icon" type="image/png">
    <link href="<?php echo \app\helpers\Url_helper::css_url("common.css"); ?>" rel="stylesheet" media="screen">
    <?php
    if(isset($css))
    {
      foreach($css as $file)
      {
        ?>
        <link href="<?php echo \app\helpers\Url_helper::css_url($file); ?>" rel="stylesheet" media="screen">
        <?php
      }
    }
    ?>
  </head>

  <body<?php echo isset($body_id) ? ' id="' . $body_id . '"' : ''; ?>>
    <header id="header">
      <?php include("app/views/layout/header.php"); ?>
    </header>
    <section id="main">
      <?php echo OUTPUT; ?>
    </section>
    <footer id="footer">
      <?php include("app/views/layout/footer.php"); ?>
    </footer>

    <script>
    var $site_url = '<?php echo \core\helpers\Url_helper::site_url(); ?>';
    var $current_url = '<?php echo \core\helpers\Url_helper::current_url(); ?>';
    </script>
		<script src="<?php echo \app\helpers\Url_helper::js_url("jquery-3.4.1.min.js"); ?>"></script>
    <script src="<?php echo \app\helpers\Url_helper::js_url("popper.min.js"); ?>"></script>
    <script src="<?php echo \app\helpers\Url_helper::js_url("bootstrap.min.js"); ?>"></script>
		<script src="<?php echo \app\helpers\Url_helper::js_url("common.js"); ?>"></script>
    <?php
    if(isset($js))
    {
      foreach($js as $file)
      {
        ?>
    		<script src="<?php echo \app\helpers\Url_helper::js_url($file); ?>"></script>
        <?php
      }
    }
    ?>
  </body>
</html>
