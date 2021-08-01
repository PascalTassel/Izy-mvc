<?php
$IZY =& get_instance();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Izy-mvc - Welcome</title>
        <?php
        foreach ($IZY->output->get_canonicals() as $rel => $link)
        {
            echo '<link rel="'. $rel .'" href="' . $link .'">' . "\n";
        }
        ?>
    </head>
    <body>
        <header>
            <a href="https://www.izy-mvc.com/">
                <img src="https://www.izy-mvc.com/assets/im/brand.svg" width="120">
            </a>
        </header>
        <main>
            <section>
                <h1>Bienvenue sur la page d'accueil de votre application !</h1>
                
                <p>Pour modifier cette page, éditez le fichier <strong><?php echo $_SERVER['HTTP_HOST'] . '/app/views/welcome.php'; ?></strong>.</p>
                
                <p>Pour en savoir plus, consultez le <a href="https://www.izy-mvc.com/userguide">guide de l'utilisateur d'Izy-mvc</a>.</p>
            </section>
        </main>
        <footer>
            <small><?php  echo round($IZY->output->get_length() / 1024, 2) . ' Ko chargés en ' . $IZY->output->get_time(); ?> sec.</small>
        </footer>
        
        <style type="text/css">
            html {
                height: 100%;
            }
            body {
                height: 100%;
                font-family: sans-serif;
                background-color: #fff;
                color: #1e1e2f;
                margin: 0;
            }
            a {
                color: #e14eca;
            }
            a:hover,
            a:focus {
                color: #ad3d9c;
            }
            header {
                position: fixed;
                top: 0;
                right: 0;
                left: 0;
                padding: .5rem;
                background-color: #181826;
            }
            main {
                display: flex;
                height: 100%;
                align-items: center;
                justify-content: center;
                background: #fff url('https://www.izy-mvc.com/assets/im/bg-lead.png') center;
                background-size: cover;
            }
            section {
                max-width: 50rem;
                margin: 1rem;
                padding: 1rem;
                border: 1px solid #ffd5f8;
                border-radius: .25rem;
                background-color: #fff;
                text-align: center;
            }
            footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                padding: .5rem;
                background-color: #fff1fd;
                border-top: 1px solid #ffd5f8;
                color: #e14eca;
            }
        </style>
    </body>
</html>
