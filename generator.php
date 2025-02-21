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
    DatabaseMetadata::initialize($sqlExecutor);
    $builder = new ColModelBuilder($sqlExecutor);
    $query = $_POST['query'] ?? '';
        if(!empty($query)) {
            $colModel = $builder->buildFromQuery($query);
            $result = $builder->toJson($colModel);
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
    <title>ColModel Generator</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            background-color: white;
        }

        .container {
            background-color: white;
            margin:0;
            padding: 0 0.5em 1em 0.5em;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {

        }
        .flexRow {display: flex; flex-direction: row; justify-content: space-between; align-items: center; margin-bottom: 10px;}
        label {color: maroon;}
        input[type="text"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        textarea {
            height: 150px;
            font-family: "Roboto Mono", "Courier New", monospace;
            resize: vertical;
            background-color: white;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #45a049;
        }

        .result {
            margin-top: 20px;
            position: relative;
        }

        .result pre {
            background-color: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.5;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff;
            font-size: 12px;
            padding: 5px 10px;
        }

        .copy-btn:hover {
            background-color: #0056b3;
        }

        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 style="color:maroon;margin:0;padding:0">ColModel Generator</h1>
    <form method="post" id="colmoForm">
        <div class="flexRow" style="margin-bottom:0;padding-bottom:0">
            <div class="form-group">
                <label for="databaseName">Database Name:</label><br>
                <input type="text" id="databaseName" name="databaseName" required>
            </div>
            <div class="form-group">
                <label for="userName">User Name:</label><br>
                <input type="text" id="userName" name="userName" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div><button type="submit">Generate ColModel</button></div>
        </div>
        <div class="form-group" style="margin-top:0;padding-top:0">
            <label for="query">SQL Query:</label><br>
            <textarea id="query" name="query" required
                      placeholder="Enter your SELECT query here..."><?= htmlspecialchars($_POST['query'] ?? '') ?></textarea>
        </div>


    </form>

    <?php if($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if($result): ?>
        <div class="result">
            <button class="copy-btn" onclick="copyResult()">Copy</button>
            <pre id="result"><?= htmlspecialchars($result) ?></pre>
        </div>
    <?php endif; ?>
</div>

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
