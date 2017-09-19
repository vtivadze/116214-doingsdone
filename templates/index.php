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
                        <? if($t['date_completion'] != '' && $show_complete_tasks != 1) continue;?>
                        <tr class="tasks__item task <?=!is_null($t['date_completion']) ? 'task--completed' : ''; ?> <?= (!is_null($t['date_completion']) && get_days_until_deadline($t['deadline']) <= 1) ? 'task--important' : ''; ?>">

                            <td class="task__select">
                                <label class="checkbox task__checkbox">
                                    <input class="checkbox__input visually-hidden" type="checkbox">
                                    <span class="checkbox__text"><?=htmlspecialchars(trim($t['name']))?></span>
                                </label>
                            </td>
                            <td class="task__date"><?=htmlspecialchars(trim($t['deadline'] ?? 'Нет'))?></td>

                            <td class="task__controls">

                                <? if(is_null($t['date_completion'])): ?>
                                    <button class="expand-control" type="button" name="button"><?=htmlspecialchars(trim($t['name']))?></button>

                                    <ul class="expand-list hidden">
                                        <li class="expand-list__item">
                                            <a href="/index.php?rm=<?=$t['id']?>">Выполнить</a>
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