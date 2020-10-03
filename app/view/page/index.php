<?php
if (!isset($library_home['title'])) {
    $library_home['title']="Title";
}
?>
<main role="main">
    <div class="jumbotron">
        <div class="container">
            <h1 class="display-3"><?= $library_home['title'] ?></h1>

            <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more </a></p>
        </div>
    </div>

    <div class="container">
        <hr>
    </div>
</main>