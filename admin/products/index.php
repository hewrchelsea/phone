<?php
session_start();

if(!isset($_SESSION['admin'])) {
    http_response_code(404);
    die;
}else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/admin.css">
    <script src="/phone/js/jquery.js"></script>
    <script src="../js/admin.js" defer></script>
    <title>FOOTYPHONES | ADMIN</title>
</head>
<body>
    <h1 class="title">Admin Dashboard</h1>
    <section>
        <a href="./add" class="link">
            <h2 class="link_title">Add Products</h2>
            <div class="icon plus"></div>
        </a>
        <a href="./update" class="link">
            <h2 class="link_title">Update Products</h2>
            <div class="icon update"></div>
        </a>
    </section>
</body>
</html>
<?php
}
?>