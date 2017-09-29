<header class="main-header">
    <a href="#">
        <img src="img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
    </a>

    <div class="main-header__side">
        <?php if (!isset($_SESSION['name'])): ?>
            <a class="main-header__side-item button button--transparent" href="/index.php?login">Войти</a>
        <?php else: ?>
            <a class="main-header__side-item button button--plus" href="/index.php?add_task">Добавить задачу</a>

            <div class="main-header__side-item user-menu">
                <div class="user-menu__image">
                    <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
                </div>

                <div class="user-menu__data">
                    <p><?=$_SESSION['name'];?></p>

                    <a href="/logout.php">Выйти</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>