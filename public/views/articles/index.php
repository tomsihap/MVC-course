<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    

    <ul>
    <?php foreach($articles as $a) : ?>
        <li><strong><?= $a->title(); ?></strong>: <?= $a->content();?></li>
    <?php endforeach;?>
    </ul>
</body>
</html>