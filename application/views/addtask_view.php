<?php
/** @var array $data */
$data = $data ?? [];
?>
<form enctype="multipart/form-data" method="post" action="/taskslist">
    <?php echo \App\Csrf::field(); ?>
    <?php echo empty($data['id']) ? '' : '<input type="hidden" name="id" value="' . htmlspecialchars((string) $data['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">'; ?>
    <div class="form-group">
        <label>email</label>
        <input required type="email" class="form-control" name="email" value="<?php echo htmlspecialchars((string) ($data['email'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Имя пользователя</label>
        <input required type="text" class="form-control" name="user_name" aria-describedby="Test" value="<?php echo htmlspecialchars((string) ($data['user_name'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
        <label for="exampleFormControlTextarea1">Текст задачи</label>
        <textarea required class="form-control" id="exampleFormControlTextarea1" rows="4" name="text"><?php echo htmlspecialchars((string) ($data['text'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></textarea>
        <input type="hidden" name="text_old" value="<?php echo htmlspecialchars((string) ($data['text'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
    </div>
    <div class="form-group statuses">
        <input type="checkbox" aria-describedby="Статус" name="statuses[]" value="выполнено"
            <?php echo in_array('edit', $_SESSION['rules'] ?? [], true) ? '' : 'disabled="disabled"'; ?>
            <?php echo in_array('выполнено', $data['statuses'] ?? [], true) ? 'checked' : ''; ?>
        >
        <label class="">выполнена</label>
    </div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
