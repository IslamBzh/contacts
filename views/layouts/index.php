<!DOCTYPE html>

<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport"   content="width=device-width, initial-scale=1">

    <meta name="author"     content="IslamBzh">

    <title><?=$this->title?></title>

    <!-- stylesheets -->
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <link rel="stylesheet" type="text/css" href="/css/contacts.css">

    <!-- scripts -->
    <script type="text/javascript" src="/js/main.js"></script>
    <script defer type="text/javascript" src="/js/contacts.js"></script>

    <script type="text/javascript" src="/js/IMask.js"></script>

    <script type="text/javascript">
        const CSRF = '<?=\components\Session::getCSRF()?>';
    </script>

</head>

<body>
    <div id="content">
        <? $this->loadTmpl() ?>
    </div>
</body>

</html>