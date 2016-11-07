<?php include '_head.html.php'; ?>

<form action="<?=$this->dir();?>/admin/login" method="post">

  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" id="username" name="username">
    <label class="mdl-textfield__label" for="username">Nazwa użytkownika</label>
  </div>

  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="password" id="password" name="password">
    <label class="mdl-textfield__label" for="password">Hasło</label>
  </div>


  <button type="submit" name="login" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">Zaloguj</button>
</form>

<?php include '_foot.html.php'; ?>