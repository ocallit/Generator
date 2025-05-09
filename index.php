<?php
// colmo-generator.php v 1.0.1
require_once __DIR__ . '/vendor/autoload.php';

use Ocallit\Sqler\DatabaseMetadata;
use Ocallit\Sqler\SqlExecutor;
use Ocallit\JqGrider\JqGrider\ColModelBuilder;

$error = '';
$result = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $config = [
          'hostname' => 'localhost',
          'username' => $_POST['userName'] ?? '',
          'password' => $_POST['password'] ?? '',
          'database' => $_POST['databaseName'] ?? '',
          'port' => NULL,
          'socket' => NULL,
          'flags' => 0,
        ];
        $sqlExecutor = new SqlExecutor($config);

        $query = $_POST['query'] ?? '';
        switch( $_POST['action'] ?? '') {
            case 'listTables':
                $r = $sqlExecutor->array("SHOW FULL TABLES");
                $tables = [];
                foreach($r as $table)
                    $tables[] = reset($table) . ($table['Table_type'] !== 'BASE TABLE' ? ' (VIEW)' : '');
                $result = "<ol style='line-height:2em;column-count:4'><li>" . implode("</li><li>", $tables) . "</li></ol>";
                break;
            default:
                if(!empty($query)) {
                    DatabaseMetadata::initialize($sqlExecutor);
                    $builder = new ColModelBuilder($sqlExecutor);
                    $colModel = $builder->buildFromQuery($query);
                    $result = htmlspecialchars($builder->toJson($colModel));
                }
        }

    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/generator.css">
    <link rel="stylesheet" href="assets/ocToolbar/css/ocToolbar.css">
</head>
<body style="padding:0;margin:0">
<form method="post" id="colmoForm" style="width:100%;padding:0;margin:0">
<header class="ocToolbar generatorToolbar" style="padding:0;margin: 0;width:100%" role="toolbar" aria-label="Main toolbar">
    <div class="ocToolbarMessage"><h1 class="ocToolbarTitle">Generator</h1></div>

    <fieldset class="ocToolbarGroup"><legend>Db Info</legend>
        <div class="ocToolbarItem ocToggle">
            <input role="switch" aria-checked="false" type="checkbox" id="listTables" onchange="this.form.submit()" value="listTables" name="action">
            <label for="listTables" class="ocToolbarItem">
                <i aria-hidden="true" class="fa-solid fa-database" style="color:var(--toolbar-icon-primary)"></i>
                <p>Tables</p>
            </label>
        </div>

        <div class="ocToolbarItem" style="justify-content: space-between"><label class="ocToolbarSelectItem">
                <select class="statusSelect" name="defineTable" oninput="this.form.submit()">
                    <option value=""></option>
                    <option>Table 1</option>
                    <option>Table 2</option>
                </select>
            </label>
            <p>Table Definition</p>
        </div>
    </fieldset>

    <fieldset class="ocToolbarGroup"><legend>Tabulator</legend>
        <div class="ocToolbarItem ocToggle">
            <input role="switch" aria-checked="false" type="checkbox" id="tabulatorColModel" onchange="this.form.submit()" value="tabulatorColModel" name="action">
            <label for="tabulatorColModel" class="ocToolbarItem">
                <i aria-hidden="true" class="fa-solid fa-list" style="color:var(--toolbar-icon-primary)"></i>
                <p>ColDef</p>
            </label>
        </div>
        <div class="ocToolbarItem ocToggle">
            <input role="switch" aria-checked="false" type="checkbox" id="tabulatorDef" onchange="this.form.submit()" value="tabulatorDef" name="action">
            <label for="tabulatorDef" class="ocToolbarItem">
                <i aria-hidden="true" class="fa-solid fa-table" style="color:var(--toolbar-icon-primary)"></i>
                <p>Init</p>
            </label>
        </div>
        <div class="ocToolbarItem ocToggle">
            <input role="switch" aria-checked="false" type="checkbox" id="tabulatorTemplate" onchange="this.form.submit()" value="tabulatorTemplate" name="action">
            <label for="tabulatorTemplate" class="ocToolbarItem">
                <i aria-hidden="true" class="fa-solid fa-file" style="color:var(--toolbar-icon-primary)"></i>
                <p>Page</p>
            </label>
        </div>
    </fieldset>

    <fieldset class="ocToolbarGroup"><legend>JqGrid</legend>
        <div class="ocToolbarItem ocToggle">
            <input role="switch" aria-checked="false" type="checkbox" id="jqGridColModel" onchange="this.form.submit()" value="jqGridColModel" name="action">
            <label for="jqGridColModel" class="ocToolbarItem">
                <i aria-hidden="true" class="fa-solid fa-list" style="color:var(--toolbar-icon-primary)"></i>
                <p>ColModel</p>
            </label>
        </div>
        <div class="ocToolbarItem ocToggle">
            <input role="switch" aria-checked="false" type="checkbox" id="jqGridDef" onchange="this.form.submit()" value="jqGridDef" name="action">
            <label for="jqGridDef" class="ocToolbarItem">
                <i aria-hidden="true" class="fa-solid fa-table" style="color:var(--toolbar-icon-primary)"></i>
                <p>Init</p>
            </label>
        </div>
        <div class="ocToolbarItem ocToggle">
            <input role="switch" aria-checked="false" type="checkbox" id="jqGridTemplate" onchange="this.form.submit()" value="jqGridTemplate" name="action">
            <label for="jqGridTemplate" class="ocToolbarItem">
                <i aria-hidden="true" class="fa-solid fa-file" style="color:var(--toolbar-icon-primary)"></i>
                <p>Page</p>
            </label>
        </div>
    </fieldset>

    <fieldset class="ocToolbarGroup"><legend>Db Connection</legend>
        <div class="ocToolbarItem">
            <label for="databaseName">Database Name:</label><br>
            <input type="text" id="databaseName" name="databaseName" required>
        </div>
        <div class="ocToolbarItem">
            <label for="userName">User Name:</label><br>
            <input type="text" id="userName" name="userName" required>
        </div>
        <div class="ocToolbarItem">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
    </fieldset>
</header>
<div class="container">




        <fieldset style="padding:1em 0 0 0"><legend><label for="query">Sql Query</label>
                <span><i title="copy" class="fa-solid fa-copy fa-xl ocCopyEdit"></i> <i title="Paste" class="fa-solid fa-paste fa-xl ocCopyEdit"></i> <i title="Download" class="fa-solid fa-file-arrow-down fa-xl ocCopyEdit"></i></span>
            </legend>
            <textarea id="query" name="query" required
                      placeholder="Enter your SELECT query here..."><?= htmlspecialchars($_POST['query'] ?? '') ?></textarea>
        </fieldset>

        <?php if($error) echo "<div class='error'>" . htmlspecialchars($error)  . "</div>"; ?>

    <fieldset style="padding:1em 0 0 0"><legend>
            <span><i title="copy" class="fa-solid fa-copy fa-xl ocCopyEdit" onclick="copyResult()"></i> <i title="Download" class="fa-solid fa-file-arrow-down fa-xl ocCopyEdit"></i></span>
        </legend>
        <pre id="result"><?= $result ?></pre>
    </fieldset>
</div>
</form>

<hr>
<hr>
<div class="flexRow" style="margin-bottom:0;padding-bottom:0">
    <div><label style="cursor: pointer;"><input value="Tablas" name="ver" type="checkbox" onchange="this.form.submit()"> List Tables</label></div>
    <div><button type="submit">Generate ColModel</button></div>
</div>
<hr>
<div style="margin:1em;padding:1em;border:1px solid #ddd">
    <ul>
        <li><a href="https://php-operators.com/">PHP Operators</a>
    </ul>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fields = ['databaseName', 'userName', 'password'];
        fields.forEach(field => {
            const savedValue = localStorage.getItem(field);
            if(savedValue) {
                document.getElementById(field).value = savedValue;
            }
        });
    });

    document.getElementById('colmoForm').addEventListener('submit', function() {
        const fields = ['databaseName', 'userName', 'password'];
        fields.forEach(field => {
            const value = document.getElementById(field).value;
            localStorage.setItem(field, value);
        });
    });

    function copyResult() {
        const result = document.getElementById('result');
        const range = document.createRange();
        range.selectNode(result);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();

        const btn = document.querySelector('.copy-btn');
        btn.textContent = 'Copied!';
        setTimeout(() => {
            btn.textContent = 'Copy';
        }, 2000);
    }
</script>
</body>
</html>
