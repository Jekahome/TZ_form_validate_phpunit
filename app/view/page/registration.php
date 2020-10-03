<?php

$name = $fields['name'] ?? '';
$email = $fields['email'] ?? '';
$login = $fields['login'] ?? '';
$password = $fields['password'] ?? '';
$confirmPassword = $fields['confirmPassword'] ?? '';

?>
<script src="/static/registrationForm.js"></script>


<div class="container py-3">
    <div class="row">
        <div class="mx-auto col-sm-6">
            <!-- form user info -->
            <div class="card">
                <div class="card-header">
                    <br><br><br><h4 class="mb-0"><?= $library_fields['title'] ?></h4>
                </div>
                <div class="card-body">
                    <form class="form" role="form" name="registrationForm" action="<?= WEB ?>registration" autocomplete='On'>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"> <?= $library_fields['name_label'] ?> </label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="name" id="name" value="<?= $name ?>"
                                       required placeholder="<?= $library_fields['name_label'] ?>"
                                       pattern="<?= $patterns['name'] ?>" title="<?= $library_fields['name_title'] ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['email_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="email" id="email" type="email" value="<?= $email ?>"
                                       required placeholder="<?= $library_fields['email_label'] ?>"
                                       title="<?= $library_fields['email_title'] ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['login_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="login" id="login" type="text" value="<?= $login ?>"
                                       required placeholder="<?= $library_fields['login_label'] ?>"
                                       pattern="<?= $patterns['login'] ?>"
                                       title="<?= $library_fields['login_title'] ?>">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['password_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="password" id="password" type="password"
                                       value="<?= $password ?>" required
                                       placeholder="<?= $library_fields['password_label'] ?>"
                                       pattern="<?= $patterns['password'] ?>"
                                       title="<?= $library_fields['password_title'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"><?= $library_fields['confirmPassword_label'] ?></label>
                            <div class="col-lg-9">
                                <input class="form-control" name="confirmPassword" id="confirmPassword"
                                       value="<?= $confirmPassword ?>" disabled=true type="text" required
                                       placeholder="<?= $library_fields['confirmPassword_label'] ?>"
                                       pattern="<?= $patterns['password'] ?>"
                                       title="<?= $library_fields['password_title'] ?>">
                            </div>
                        </div>

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
                                <input name="save" id="save" type="submit" class="btn btn-primary"
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














