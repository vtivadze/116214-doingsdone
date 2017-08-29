<main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.php" method="post">
                    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <div class="radio-button-group">
                        <label class="radio-button">
                            <input class="radio-button__input visually-hidden" type="radio" name="radio" checked="">
                            <span class="radio-button__text">Все задачи</span>
                        </label>

                        <label class="radio-button">
                            <input class="radio-button__input visually-hidden" type="radio" name="radio">
                            <span class="radio-button__text">Повестка дня</span>
                        </label>

                        <label class="radio-button">
                            <input class="radio-button__input visually-hidden" type="radio" name="radio">
                            <span class="radio-button__text">Завтра</span>
                        </label>

                        <label class="radio-button">
                            <input class="radio-button__input visually-hidden" type="radio" name="radio">
                            <span class="radio-button__text">Просроченные</span>
                        </label>
                    </div>

                    <label class="checkbox">
                        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
                        <input id="show-complete-tasks" class="checkbox__input visually-hidden" type="checkbox" <?= $show_complete_tasks == 1 ? 'checked' : ''; ?>>
                        <span class="checkbox__text">Показывать выполненные</span>
                    </label>
                </div>

                <table class="tasks">
                  
                    <? foreach($tasks as $t): ?>
                        <? if($t['Выполнен'] == 'Да' && $show_complete_tasks != 1) continue;?>
                        <tr class="tasks__item task <?=$t['Выполнен'] == 'Да' ? 'task--completed' : ''; ?> <?= ($t['Дата выполнения'] != 'Нет' && get_days_until_deadline($t['Дата выполнения']) <= 1) ? 'task--important' : ''; ?>">

                            <td class="task__select">
                                <label class="checkbox task__checkbox">
                                    <input class="checkbox__input visually-hidden" type="checkbox">
                                    <span class="checkbox__text"><?=$t['Задача']?></span>
                                </label>
                            </td>
                            <td class="task__date"><?=$t['Дата выполнения']?></td>

                            <td class="task__controls">

                                <? if($t['Выполнен'] == 'Нет'): ?>
                                    <button class="expand-control" type="button" name="button"><?=$t['Задача']?></button>

                                    <ul class="expand-list hidden">
                                        <li class="expand-list__item">
                                            <a href="#">Выполнить</a>
                                        </li>

                                        <li class="expand-list__item">
                                            <a href="#">Удалить</a>
                                        </li>
                                    </ul>
                                <? endif; ?>

                            </td>
                        </tr>
                    <? endforeach; ?>
                </table>
            </main>