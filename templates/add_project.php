<div class="modal">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Добавление проекта</h2>

  <form class="form" method="post">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>
      <input class="form__input" type="text" name="name" id="project_name" value="<?=$name ?? ''?>" placeholder="Введите название">
      <span class="form__error"><?=$errors['name']['msg'] ?? '';?></span>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="add_project" value="Добавить">
    </div>
  </form>
</div>