<header class="main-header">
    <a href="#">
        <img src="img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
    </a>

    <div class="main-header__side">
        <? if (!isset($_SESSION['email']) || !isset($_SESSION['password'])): ?>
            <a class="main-header__side-item button button--transparent" href="/116214-doingsdone/index.php?login">Войти</a>
        <? else: ?>
            <a class="main-header__side-item button button--plus" href="/116214-doingsdone/index.php?add">Добавить задачу</a>

            <div class="main-header__side-item user-menu">
                <div class="user-menu__image">
                    <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
                </div>

                <div class="user-menu__data">
                    <p><?=$_SESSION['name'];?></p>

                    <a href="/116214-doingsdone/logout.php">Выйти</a>
                </div>
            </div>
        <? endif; ?>
    </div>
</header>