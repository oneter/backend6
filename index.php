<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title> Задание 5 </title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
    <?php include 'forma.php'; ?>
		<div class="container" style= "background-color: #3ecec4;">
      <?php
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            echo '<div class="errors">';
            foreach ($_SESSION['errors'] as $error) {
                echo '<p>' . $error . '</p>';
            }
            echo '</div>';
        } elseif (isset($_COOKIE['name'])) {
            echo '<div class="success">';
            echo '<p>Форма успешно отправлена</p>';
            echo '</div>';
        }
        ?>
			<div class="forma">
                <h2 id="Форма">Форма</h2>
                <form action="forma.php" method="POST" id="form" >
                    <label for="name"> Имя: </label>
                        <br />
                  <?= showError('name') ?>
                        <input type="text" name="name" id="name" value="<?= getFieldValue('name') ?>" placeholder="Введите имя">
                    <br />
                    <label for="email"> Почта: </label>
                  <?= showError('email') ?>
			<br />
			<input type="email" name="email" id="email" value="<?= getFieldValue('email') ?>" placeholder="Введите вашу почту" >
			<br />
			<label for="year"> Год рождения: </label>
                  <?= showError('year') ?> 
			<br />
			<select name="year" id="year" >
				<option value="<?= getSelected('year', "") ?>">Выберите год</option>
			</select>
			<br />
			<label> Пол: </label>
			<br />
                  <?= showError('sex') ?>
			<label><input type="radio" checked="checked" name="sex" value="Мужской" <?= getChecked('sex', 'Мужской') ?> />М</label>
			<label><input type="radio" name="sex" value="Женский" <?= getChecked('sex', 'Женский') ?> />Ж</label>
						<br />
			<label>	Кол-во конечностей: </label>
                  <?= showError('legs') ?>
						<br />
						<label>
							<input type="radio" checked="checked" name="legs" value="1" <?= getChecked('legs', '1') ?>  />1
						</label>
						<label>
							<input type="radio" name="legs" value="2" <?= getChecked('legs', '2') ?> />2
						</label>
						<label>
							<input type="radio" name="legs" value="3" <?= getChecked('legs', '3') ?> />3
						</label>
						<label>
							<input type="radio" name="legs" value="4" <?= getChecked('legs', '4') ?> />4
						</label>
						<label>
							<input type="radio" name="legs" value="5" <?= getChecked('legs', '5') ?> />5
						</label>
						<br />
					
                           <label> Сверхспособности: </label>
                            <br />
							<select name="powers[]" id="powers" multiple="multiple" >
								<option value="Бессмертие" <?= getSelected('powers', 'Бессмертие') ?>>Бессмертие</option>
								<option value="Прохождение сквозь стены" <?= getSelected('powers', 'Прохождение сквозь стены') ?>>Прохождение сквозь стены</option>
								<option value="Левитация" <?= getSelected('powers', 'Левитация') ?>>Левитация</option>
							</select>
						</label>
						<br />
						<label for="bio"> Биография: </label>
							<br />
							<textarea name="bio" id="bio"  placeholder="Придумайте свою биографию..."><?= getFieldValue('bio') ?></textarea>
						<br />
						<br />
						<label>
							С контрактом ознакомлен(а) <input type="checkbox" name="agree" value="yes" <?= getChecked('agree', 'yes') ?>/>
							</label>
						<br />
                        <div class="button">
                            <input type="submit" value="Отправить" />
                        </div>
        <a href="login.php" class="auth-button">Авторизация</a>
                    </form>
				<script>
              const select = document.getElementById('year');
              const currentYear = new Date().getFullYear();
              for (let i = currentYear; i >= currentYear - 100; i--) {
                  const option = document.createElement('option');
                  option.value = i;
                  option.text = i;
                  if(i == <?= isset($_COOKIE['year']) ? $_COOKIE['year'] : '""' ?>) 
                     {
                     option.selected = true;
                     }
                  select.add(option);
}

    </script>
                </div>
            </div>
        </body>
        </html>
