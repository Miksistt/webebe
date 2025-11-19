<?php
namespace App\Core;
use App\Traits\Helper;
define('CONTENT_PATH', __DIR__ . '\content');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Filemanager
{

    private string $contentPath;

    public function __construct(string $path = './content')
    {
        $realPath = realpath($path);
        $this->contentPath = ($realPath !== false) ? rtrim($realPath, DIRECTORY_SEPARATOR) : '';
    }

    public function read(string $relPath): ?string
    {
        $fp = $this->contentPath . '/' . ltrim($relPath, '/');
        $rp = realpath($fp);
        if ($rp && strpos($rp, $this->contentPath) === 0 && is_file($rp)) return file_get_contents($rp);
        return null;
    }

    public function write(string $relPath, string $content): bool
    {
        $fp = $this->contentPath . '/' . ltrim($relPath, '/');
        $d = dirname($fp);
        if (!is_dir($d)) mkdir($d, 0755, true);
        $realDir = realpath($d);
        if ($realDir === false || strpos($realDir, $this->contentPath) !== 0) return false;
        return file_put_contents($fp, $content) !== false;
    }

    public function delete(string $relPath): bool
    {
        $fp = $this->contentPath . '/' . ltrim($relPath, '/');
        $rp = realpath($fp);
        return ($rp && strpos($rp, $this->contentPath) === 0 && is_file($rp)) ? unlink($rp) : false;
    }
    public function listFiles($dir='posts', $extension = '.md')
    {
        $fullDir = $this->contentPath . '/' . ltrim($dir, '/');
        if (!is_dir($fullDir)) return [];
        $files = glob($fullDir . '/*' . $extension);
        return array_map(function ($f) {
            return str_replace($this->contentPath . '/', '', $f);
        }, $files);
    }

    public function listDir($dir ='')
    {
        $fullPath = $this->contentPath . '/' . ltrim($dir, '/');
        if (!is_dir($fullPath))
            return false;
        return array_filter(glob($fullPath.'/*'), 'is_dir');
    }


    public function list(string $relPath = ''): array{
        $dir = $this->contentPath . '/' . ltrim($relPath, '/');
        $real = realpath($dir);
        if (!$real || strpos($real, $this->contentPath) !== 0 || !is_dir($real)) return [];
        $out = [];
        foreach (scandir($dir) as $f) {
            if ($f == '.' || $f == '..') continue;
            $p = $real . '/' . $f;
            $out[] = [
                    'name' => $f,
                'isDir' => is_dir($p),
                'size' => is_file($p) ? filesize($p) : null,
            ];
        }
        return $out;
    }
}

$fm = new Filemanager();
$current = trim($_GET['dir'] ?? '', '/');
$err = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['uploadfile']) && $_FILES['uploadfile']['error'] == UPLOAD_ERR_OK){
    $name = basename($_FILES['uploadfile']['name']);
    $tmpPath = $_FILES['uploadfile']['tmp_name'];
    if(!$fm->write($current . '/' . $name, file_get_contents($tmpPath))){
        $err = 'Ошибка загрузки!';
    } else{
        header("Расположение: ?dir=" . urlencode($current)); exit;
    }
}

if (isset($_GET['delete'])){
    if(!$fm->delete($current . '/' . $_GET['delete'])){
        $err = 'Ошибка удаления';
    } else{
        header("Расположение: ?dir=" . urlencode($current)); exit;
    }
}

$entries = $fm->list($current);
$parent = '';
if ($current !== '') {
    $parts = explode('/', $current);
    array_pop($parts);
    $parent = implode('/', $parts);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Файловый менеджер</title>
    <link rel="stylesheet" href="/public/css/FileM.css" />
</head>
<body>
<h1>Файловый менеджер</h1>
<nav>
    <?php if ($current !== ''): ?>
        <a href="?dir=<?= urlencode($parent) ?>">← Вверх</a>
    <?php endif; ?>
</nav>
<?php if ($err): ?><p class="error"><?= htmlspecialchars($err) ?></p><?php endif; ?>
<table>
    <thead><tr><th>Имя</th><th>Размер</th><th>Действия</th></tr></thead>
    <tbody>
    <?php foreach ($entries as $e): ?>
        <tr>
            <td>
                <?php if ($e['isDir']): ?>
                    <svg class="icon-folder" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2z"/></svg>
                    <a href="?dir=<?= urlencode(($current ? $current . '/' : '') . $e['name']) ?>"><?= htmlspecialchars($e['name']) ?></a>
                <?php else: ?>
                    <svg class="icon-file" viewBox="0 0 20 20"><path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V7l-5-5H4z"/></svg>
                    <?= htmlspecialchars($e['name']) ?>
                <?php endif; ?>
            </td>
            <td><?= $e['isDir'] ? '-' : number_format($e['size']/1024,2) . ' KB' ?></td>
            <td class="actions">
                <?php if (!$e['isDir']): ?>
                    <a href="?dir=<?= urlencode($current) ?>&delete=<?= rawurlencode($e['name']) ?>" onclick="return confirm('Удалить файл?');">Удалить</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<form class="upload" enctype="multipart/form-data" method="POST">
    <label for="uploadfile" class="upload-label">
        <span>Выберите файл:</span>
        <input type="file" name="uploadfile" id="uploadfile" required />
    </label>
    <input type="submit" value="Загрузить" class="upload-btn" />
</form>
</body>
</html>