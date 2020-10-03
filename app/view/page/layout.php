<?php

use app\User;

$lang = (LANGUAGES);
$lang[$_SESSION['lang']] = 'selected';
$library_layout = app\language\language::init()->getLibrary('layout');
$uid = User::getInstance()->getUri();

?>
<!doctype>
<html>
<header>

    <title><?= $library_layout['user'] ?></title>
</header>
<body class="text-center">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
      integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>


<!-- navbar -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="<?= (WEB) ?>"><?= SITE_NAME ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?= (WEB) ?>"> <?= $library_layout['home'] ?> <span class="sr-only">(current)</span></a>
            </li>
            <?php if (!is_null($uid)) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= (WEB . 'users/'.$uid) ?>"><?= $library_layout['user'] ?></a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= (WEB . 'users') ?>"><?= $library_layout['users'] ?></a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false"><?= $library_layout['select_lang'] ?></a>
                <div class="dropdown-menu">
                    <?php foreach ($lang as $name_lang => $selected) {
    //echo "<option class=\"dropdown-item\" value=\"$name\" ".($selected=='selected'?'selected':'').">$name</option>" ;
    echo "<a class='lang dropdown-item " . ($selected == 'selected' ? 'active' : '') . "' href='#' value='" . $name_lang . "'>$name_lang</a>";
} ?>
                </div>
            </li>

        </ul>
        <?php if (!is_null($uid)) : ?>
            <form class="form-inline mt-2 mt-md-0">
                <button id='logout' class="btn btn-outline-success my-2 my-sm-0"
                        type="submit"><?= $library_layout['logout'] ?></button>
            </form>
        <?php elseif ($page != 'registration') : ?>
            <form name='login' class="form-inline mt-2 mt-md-0" autocomplete='On'>
                <input class="form-control mr-sm-2" name='email' type='email' required placeholder="email"
                       aria-label="Email">
                <input class="form-control mr-sm-2" name='password' type='password' required placeholder="password"
                       aria-label="Password">
                <button class="btn btn-outline-success my-2 my-sm-0"
                        type="submit"><?= $library_layout['login'] ?></button>
            </form>
            <pre>  </pre>
            <form class="form-inline mt-2 mt-md-0" action="<?= (WEB . 'registration/') ?>">
                <button class="btn btn-outline-success my-2 my-sm-0"
                        type="submit"><?= $library_layout['registration'] ?></button>
            </form>
        <?php endif; ?>


    </div>
</nav>
<!-- navbar -->


<?php require_once(DIR_VIEW . "$page.php"); ?>


<div id="loginModal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="mySmallModalLabel"> <?= $library_layout['login'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>


<div id="registrationModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> <?= $library_layout['registration'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button id="modalClose" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="userModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> <?= $library_layout['user'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button id="modalClose" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
<script>
    <?php if (!User::getInstance()->isAuth() && $page != 'registration') : ?>
    if (document.forms.login)
        document.forms.login.addEventListener('submit', function (e) {

            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '<?=WEB?>login',
                data: {"email": this.elements.email.value, "password": this.elements.password.value},
                success: function (uid) {
                    if (uid) {
                        window.location.href = '/users/'+uid;
                    }
                    else {

                        $('#loginModal .modal-body').html("<div class='alert alert-warning'><?=$library_layout['user_not_found']?></div>");
                        $('#loginModal').modal('show');

                    }
                }
            });
        });
    <?php else :?>
    if (document.getElementById('logout'))
        document.getElementById('logout').addEventListener('click', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '<?=WEB?>logout',
                data: {"logout": true},
                success: function (data) {
                    window.location.href = '/';
                }
            });
        });

    <?php endif;?>
    Array.prototype.map.call(document.getElementsByClassName('lang'), function (el) {

        el.addEventListener('click', function (e) {
            e.preventDefault();
            var lang = $(this).text();

            var arr = '<?=join(",", LANGUAGES)?>'.split(',');

            if (arr.indexOf(lang) == -1) lang = '<?=DEFAULT_LANG?>';

            $.ajax({
                type: 'POST',
                url: '<?=WEB?>languages',
                data: {"languages": lang, "type": "lang"},
                success: function () {
                    window.location.reload();
                }
            });
        });
    });

    document.getElementById('modalClose').addEventListener('click', function (e) {
        e.preventDefault();
        //window.location.href = '/';
    });


    function translit($key, value) {
        <?php if (isset($library_errors)) :?>
        var errors = JSON.parse('<?=$library_errors?>');

        if (value) {
            return errors[$key].replace(/\$1/, value)
        } else {
            return errors[$key];
        }
        <?php endif;?>
    }
</script>
</html>
