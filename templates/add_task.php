<div class="modal">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form" action="index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>
      <input class="form__input <?=array_key_exists('name', $errors) ? 'form__input--error' : '';?>" type="text" name="name" id="name" value="<?=$name ?? '';?>" placeholder="Введите название">
      <? if(array_key_exists('name', $errors)): ?>
        <span class="form__error"><?=$errors['name'];?></span>
      <? endif; ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      <select class="form__input form__input--select  <?=array_key_exists('project', $errors) ? 'form__input--error' : '';?>" name="project" id="project">
        <option value=""></option>
        <? for ($i = 1; $i < count($projects); $i++): ?>
          <option value="<?=$i;?>" <?=(isset($project) && $i == $project) ? 'selected' : '';?>><?=$projects[$i];?></option>
        <? endfor; ?>
      </select>
      <? if(array_key_exists('project', $errors)): ?>
        <span class="form__error"><?=$errors['project'];?></span>
      <? endif; ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
      <input class="form__input form__input--date  <?=array_key_exists('date', $errors) ? 'form__input--error' : '';?>" type="text" name="date" id="date" value="<?=$date ?? '';?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
      <? if(array_key_exists('date', $errors)): ?>
        <span class="form__error"><?=$errors['date'];?></span>
      <? endif; ?>
    </div>

    <div class="form__row">
      <label class="form__label">Файл</label>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="preview" id="preview" value="">
        <label class="button button--transparent" for="preview">
            <span>Выберите файл</span>
        </label>
      </div>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="add" value="Добавить">
    </div>
  </form>
</div>