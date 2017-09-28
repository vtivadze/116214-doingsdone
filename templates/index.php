<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="post">
        <input class="search-form__input" type="text" name="task_name" value="" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="search_task" value="Искать">
    </form>

    <div class="tasks-controls">
        <div class="radio-button-group">
            <label class="radio-button">
                <input class="radio-button__input visually-hidden" type="radio" name="radio" <?=(isset($show) && $show == 'all') ? 'checked' : '';?>>
                <span class="radio-button__text" data-show="all" >Все задачи</span>
            </label>

            <label class="radio-button">
                <input class="radio-button__input visually-hidden" type="radio" name="radio" <?=(isset($show) && $show == 'today') ? 'checked' : '';?>>
                <span class="radio-button__text" data-show="today">Повестка дня</span>
            </label>

            <label class="radio-button">
                <input class="radio-button__input visually-hidden" type="radio" name="radio" <?=(isset($show) && $show == 'tomorrow') ? 'checked' : '';?>>
                <span class="radio-button__text" data-show="tomorrow">Завтра</span>
            </label>

            <label class="radio-button">
                <input class="radio-button__input visually-hidden" type="radio" name="radio" <?=(isset($show) && $show == 'expired') ? 'checked' : '';?>>
                <span class="radio-button__text" data-show="expired">Просроченные</span>
            </label>
        </div>

        <label class="checkbox">
            <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input id="show-complete-tasks" class="checkbox__input visually-hidden" type="checkbox" <?= $show_complete_tasks == 1 ? 'checked' : ''; ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
      
        <?php foreach($tasks as $task): ?>
            <?php if($task['date_completion'] != '' && $show_complete_tasks != 1) continue;?>
            <tr class="tasks__item task <?=!is_null($task['date_completion']) ? 'task--completed' : ''; ?> <?= (!is_null($task['deadline']) && get_days_until_deadline($task['deadline']) <= 0) ? 'task--important' : ''; ?>">

                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden" type="checkbox">
                        <span class="checkbox__text"><?=htmlspecialchars(trim($task['name']))?></span>
                    </label>
                </td>
                <td class="task__date"><?=isset($task['deadline']) ? htmlspecialchars(substr(trim($task['deadline']), 0, -9)) : 'Нет'?></td>

                <td class="task__controls">

                    <?php if(is_null($task['date_completion'])): ?>
                        <button class="expand-control" type="button" name="button"><?=htmlspecialchars(trim($task['name']))?></button>

                        <ul class="expand-list hidden">
                            <li class="expand-list__item">
                                <a href="/index.php?rm=<?=$task['id']?>">Выполнить</a>
                            </li>

                            <li class="expand-list__item">
                                <a href="#">Удалить</a>
                            </li>
                        </ul>
                    <?php endif; ?>

                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>