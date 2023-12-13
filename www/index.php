<?php
include 'common.php';

date_default_timezone_set('Europe/Kiev');

function blogpost_html($str, $timestamp)
{
    $date = date('d.m.Y H:i:s', $timestamp);
    $text = str_replace('\n', '<br>', htmlentities($str));
echo "<div class=\"post-box\">
<div class=\"post-date\">{$date}</div>
<p class=\"post-content\">{$text}</p>
</div>";
}

session_start();
if (!isset($_SESSION['username']) or $_SESSION['username'] === '')
{
    header('location: login.php');
    exit();
}
$filename = "../data/{$_SESSION['username']}.csv";

?>
<!DOCTYPE html>
<html>
<head>
<title>Блог</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<main>
<h1>Блог</h1>
<?php
echo "<p id=\"user-info\">Ви ввійшли як <b>{$_SESSION['username']}</b> | <a href=login.php>Bихід</a></p>";
?>

<div id="add-box">
<form method="post" id="new-item">
<textarea name="text" id="text" placeholder="Текст для нового запису" rows="4" required></textarea>
<button type="submit" id="add">Опублікувати</button>
</form>
</div>

<?php
if (!file_exists('../data') && !is_dir('../data'))
{
    mkdir('../data');
}

if (!file_exists($filename))
{
    $fp = fopen($filename, "w");
    fclose($fp);
}

if(isset($_POST["text"])) {
    $text = str_replace("\r\n", '\n', $_POST["text"]);
    $timestamp = time();
    $data = "{$timestamp} {$text}\n";
    file_put_contents($filename, $data, FILE_APPEND | LOCK_EX);
    message("Додано новий запис");
}

if (filesize($filename) !== 0)
{
    $file = file($filename);
    {
	$file = array_reverse($file);
	foreach($file as $buffer)
	{
            [$timestamp, $data] = explode(" ", $buffer, 2);
	    blogpost_html($data, $timestamp);
        }
    }
}
else
{
    message("Поки що немає записів.");
}
?>
</main>
</body>
</html>
