<?php include_once('app/bootstrap.php') ?>
<!DOCTYPE HTML>
<html lang="pt-br">
<head>
<title><?= $Site->titulo ?></title>
<meta charset="<?= $Site->charset ?>">

<script type="text/javascript">
var base = '<?= $Site->base?>'

</script>
	
<script type="text/javascript" src="<?= $Site->base ?>js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?= $Site->base ?>js/padrao.js"></script>
<script type="text/javascript" src="<?= $Site->base ?>js/bootstrap.min.js"></script>

<?php if (file_exists(WWW.'js/'.$Site->pagina.'.js')) : ?>
<script type="text/javascript" src="<?= $Site->base ?>js/<?= $Site->pagina ?>.js"></script>

<?php endif ?>

<link rel="stylesheet" type="text/css" href="<?= $Site->base ?>css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?= $Site->base ?>css/padrao.css" />
<?php if (file_exists(WWW.'css/'.$Site->pagina.'.css')) : ?>
<link rel="stylesheet" type="text/css" href="<?= $Site->base ?>css/<?= $Site->pagina ?>.css" />

<?php endif ?>

<script type="text/javascript">
$(document).ready(function()
{

    <?php if (!empty($Site->msgErro)) : ?>
    $(".msg_erro").fadeOut(10000);
    <?php endif ?>

    <?php if (isset($_SESSION['msgFlash'])) : ?>
    $("#msgFlash").fadeOut(10000);
    <?php endif ?>

});
</script>

</head>

<body>
    <?php if (isset($_SESSION['msgFlash'])) : ?>
    <div id='msgFlash'  class='<?= $_SESSION['msgFlash']['class'] ?>'>
        <?= $_SESSION['msgFlash']['texto'] ?>
        <?php unset($_SESSION['msgFlash']) ?>
    </div>
    <?php endif ?>

	<!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header page-scroll">
                <a class="navbar-brand" href="<?= $Site->base ?>"><?= $Site->sistema ?></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    
                    <?php if (isset($_SESSION['Usuario'])) : ?>
                    <li class="page-scroll">
                        <a href="<?= $Site->base ?>controle">Controle</a>
                    </li>
                    <?php endif ?>

                    <?php if (isset($_SESSION['Usuario'])) : ?>

                    <?php if ($_SESSION['Usuario']['id']==1) : ?>
                    <li>
                    <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">Ferramentas <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= $Site->base ?>debug">SqlDump</a></li>
                            <!--<li><a href="#">Another action</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Settings</a></li> -->
                        </ul>
                    </li>
                    </ul>
                    </li>
                    <?php endif ?>

                    <li class="page-scroll">
                        <a href="<?= $Site->base ?>sair">sair</a>
                    </li>
                    <?php else : ?>
                    <li class="page-scroll">
                        <a href="<?= $Site->base ?>login">Login</a>
                    </li>
                    <?php endif ?>

                </ul>
            </div>
            <!-- /.navbar-collapse -->

        </div>
        <!-- /.container-fluid -->
    </nav>

    <section>
    <div class="pagina container">
    <?= $conteudo ?>

    </div>
    </section>

    <?php if (!empty($Site->msgErro)) : ?>
    <div class='containter msg_erro'>
        <h3><?= $Site->msgErro ?></h3>
    </div>
    <?php endif ?>

    <?php if (isset($_SESSION['debug'])) : ?>
    <section>
        <div class='container erros'>
            <?php include_once(APP.'sql_erros.php') ?>
        </div>
    </section>

    <section>
        <div class='container sqls'>
            <?php include_once(APP.'sql_dump.php') ?>
        </div>
    </section>
    <?php endif ?>

</body>
</html>
<!-- tempo de execução <?= round(microtime(true)-$_SERVER['REQUEST_TIME'],4) ?> segundos -->