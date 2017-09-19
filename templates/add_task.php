<div class="modal">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form" action="index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>
      <input class="form__input <?=$errors['name']['class'] ?? '';?>" type="text" name="name" id="name" value="<?=$name;?>" placeholder="Введите название">
      <span class="form__error"><?=$errors['name']['msg'] ?? '';?></span>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      <select class="form__input form__input--select  <?=$errors['project']['class'] ?? '';?>" name="project" id="project">
        <option value=""></option>
        <? foreach ($projects as $p): ?>
          <option value="<?=$p['id'];?>" <?=$p['id']==$project ? 'selected' : '';?>><?=$p['name'];?></option>
        <? endforeach; ?>
      </select>
      <span class="form__error"><?=$errors['project']['msg'] ?? '';?></span>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
      <input class="form__input form__input--date  <?=$errors['date']['class'] ?? '';?>" type="text" name="date" id="date" value="<?=$date;?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
      <span class="form__error"><?=$errors['date']['msg'] ?? '';?></span>
    </div>

    <div class="form__row">
      <label class="form__label">Файл</label>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="preview" id="preview" value="">
        <label class="button button--transparent <?=$errors['preview']['class'] ?? '';?>" for="preview">
            <span>Выберите файл</span>
        </label>
      </div>
      <span class="form__error"><?=$errors['preview']['msg'] ?? '';?></span>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="add" value="Добавить">
    </div>
  </form>
</div>