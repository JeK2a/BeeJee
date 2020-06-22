<form enctype="multipart/form-data" method="post" action="taskslist">
    <?php echo empty($data['id']) ? '' : '<input type="hidden" name="id" value="' . $data['id'] . '">'; ?>
    <div class="form-group">
        <label for="exampleInputEmail1">Имя пользователя</label>
        <input required type="text" class="form-control" name="user_name" aria-describedby="Test" value="<?php echo $data['user_name'] ?? ''; ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">e-mail</label>
        <input required type="email" class="form-control" name="email" value="<?php echo $data['email'] ?? ''; ?>">
    </div>
    <div class="form-group">
        <label for="exampleFormControlTextarea1">Текст задачи</label>
        <textarea required class="form-control" id="exampleFormControlTextarea1" rows="4" name="text"><?php echo $data['text'] ?? ''; ?></textarea>
        <input type="hidden" name="text_old" value="<?php echo $data['text'] ?? ''; ?>"
    </div>
    <div class="form-group statuses">
        <input type="checkbox" aria-describedby="Статус" name="statuses[]" value="выполнено"
            <?php echo in_array('edit', $_SESSION['rules'] ?? []) ? '' : 'disabled="disabled"'; ?>
            <?php echo in_array('выполнено', $data['statuses'] ?? []) ? 'checked' : ''; ?>
        >
        <label class="">выполнена</label>
    </div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>