<h1>Смена пароля</h1>
<form method="post" action="/changepassword">
    <?php echo \App\Csrf::field(); ?>
    <div class="form-group">
        <label for="current_password">Текущий пароль</label>
        <input required type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password">
    </div>
    <div class="form-group">
        <label for="new_password">Новый пароль</label>
        <input required type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password" minlength="8">
        <small class="form-text text-muted">Минимум 8 символов.</small>
    </div>
    <div class="form-group">
        <label for="new_password_confirm">Подтверждение нового пароля</label>
        <input required type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" autocomplete="new-password" minlength="8">
    </div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
