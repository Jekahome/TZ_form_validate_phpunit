<?php


use app\User;

$name = $user['name'] ?? '';
$email = $user['email'] ?? '';
$login = $user['login'] ?? '';

?>
<script src="/static/userForm.js"></script>


<div class="jumbotron">
    <h1 class="display-4"><?= $library_user['title'] ?></h1>

</div>

<div class="container py-3">
    <h3><?=$name?></h3>
    <img src="<?= '/images/users/photo' ?>" width="400px"/>




    <br>
    <button id="drop-user" type="button" class="btn btn-danger"><?=$library_user["drop profile"]?></button>
</div>


<div  class="container py-3">
    <div class="row">
        <div class="mx-auto col-sm-6">
            <!-- form user info -->
            <div class="card">
                <div class="card-header">
                    <br><br><br><h4 class="mb-0"><?= $library_user['title form'] ?></h4>
                </div>
                <div class="card-body">
                    <form class="form" role="form" name="userForm" action="<?= WEB ?>users/<?=$uri?>/" autocomplete='On'>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"> <?= $library_fields['name_label'] ?> </label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="name" id="name" value="<?= $name ?>"
                                       placeholder="<?= $library_fields['name_label'] ?>"
                                       pattern="<?= $patterns['name'] ?>"
                                       title="<?= $library_fields['name_title'] ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['email_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="email" id="email" type="email" value="<?= $email ?>"
                                       placeholder="<?= $library_fields['email_label'] ?>"
                                       title="<?= $library_fields['email_title'] ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['login_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="login" id="login" type="text" value="<?= $login ?>"
                                       placeholder="<?= $library_fields['login_label'] ?>"
                                       pattern="<?= $patterns['login'] ?>"
                                       title="<?= $library_fields['login_title'] ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['password_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="password" id="password" type="password"
                                       value=""
                                       placeholder="<?= $library_fields['password_label'] ?>"
                                       pattern="<?= $patterns['password'] ?>"
                                       title="<?= $library_fields['password_title'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['confirmPassword_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="confirmPassword" id="confirmPassword"
                                       value="" disabled=true type="text"
                                       placeholder="<?= $library_fields['confirmPassword_label'] ?>"
                                       pattern="<?= $patterns['password'] ?>"
                                       title="<?= $library_fields['password_title'] ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-7">
                                <input type="reset" class="btn btn-secondary" value="Cancel">
                                <input name="save" id="save" type="submit" class="btn btn-primary"
                                       value="<?= $library_fields['save_value'] ?>">
                            </div>
                        </div>
                    </form>


                    <!-- Загрузка фото отдельно, из-за ограничения PUT не работает с FormData (enctype="multipart/form-data")-->
                    <form class="form" role="form" name="userFormFile" action="<?= WEB ?>users/<?=$uri?>" autocomplete='On'>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['photo_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="file" id="file" type="file"
                                       placeholder="<?= $library_fields['photo_label'] ?>"
                                       pattern="<?= implode(',', $patterns['file']) ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-7">
                                <input type="reset" class="btn btn-secondary" value="Cancel">
                                <input name="save" id="saveFile" type="submit" class="btn btn-primary"
                                       value="<?= $library_fields['save_value'] ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /form user info -->
        </div>
    </div>
</div>











<script>
    document.getElementById('drop-user').addEventListener('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'DELETE',
            url: '<?=WEB?>users/<?=User::getInstance()->getUri()?>',
            data: {},
            success: function (data) {
                window.location.href = '/';
            }
        });
    });

</script>