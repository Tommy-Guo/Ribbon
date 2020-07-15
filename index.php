<?php
    require "mySQL_details.php";
    $database = new mysqli($sql_host, $sql_user, $sql_password, "ribbon");

    $file_directory = "/uploads/";
    $current_url = '//'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    $output = "";

    function getTitle($url) {
        $page = file_get_contents($url);
        $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : null;
        return $title;
    }
    
    function createRibbon($type, $alias, $title, $content) {
        $insertQuery = $GLOBALS['database']->prepare("INSERT INTO ribbons (type, alias, title, content) VALUES (?, ?, ?, ?)");
        $insertQuery -> bind_param("ssss", $type, $alias, $title, $content);
        return array($insertQuery -> execute(), $type, $alias, $title);
    }

    function outputMessage($input) {
        $ribbonURL = '//'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . "/t/" . $input[2];
        $prefixes = array('prefix' => array('link' => 'The link', 'code' => 'The code snippet', 'text' => 'The text snippet', 'file' => 'The file'));
        if ($input[0] == true) {
            return $prefixes['prefix'][$input[1]] . " <a href='" . $ribbonURL . "'>$input[3]</a> has been created!<br><a href='" . $ribbonURL . "'>$ribbonURL</a>";
        } else {
            return "There was an error and your ribbon could not be created!";
        }
    }

    if (isset($_POST['action_links'])) {

        $title = empty($_POST['links_title']) == TRUE ? getTitle($_POST['links_content']) : $_POST['links_title'];
        $output = outputMessage(createRibbon("link", $_POST['links_alias'], $title, $_POST['links_content']));

    } else if (isset($_POST['action_text'])) {

        $output = outputMessage(createRibbon("text", $_POST['text_alias'], $_POST['text_title'], $_POST['text_content']));

    } else if (isset($_POST['action_code'])) {

        $output = outputMessage(createRibbon("code", $_POST['code_alias'], $_POST['code_title'], $_POST['code_content']));

    } else if (isset($_POST['action_file'])) {

        $currentDirectory = getcwd();
        $file_name = $_POST['files_alias'] . "." . pathinfo($_FILES['files_content']['name'],PATHINFO_EXTENSION);
        $uploadPath = $currentDirectory . $file_directory . $_POST['files_alias'] . "." .  pathinfo($_FILES['files_content']['name'],PATHINFO_EXTENSION);
        $fileTmpName  = $_FILES['files_content']['tmp_name'];
        move_uploaded_file($fileTmpName, $uploadPath);
        $output = outputMessage(createRibbon("file", $_POST['files_alias'], $_POST['files_title'], $file_name));
    }
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Ribbon</title>
        <link rel="icon" type="image/icon" href="logo.png">
        
        <link rel="stylesheet" href="lib/codemirror.css">
        <link rel="stylesheet" href="styles.css" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>

        <script src="lib/codemirror.js"></script>
        <script src="lib/meta.js"></script>
        <script src="lib/loadmode.js"></script>
        <script src="lib/autorefresh.js"></script>
        <script src="lib/jquery-3.5.1.min.js"></script>

        <?php 
            $files = scandir("lib/mode/");
            for ($x = 2; $x <= sizeof($files) - 1; $x++) {
                echo '<script src="lib/mode/'. $files[$x] . "/" . $files[$x] . '.js"></script>';
            }
        ?>

    </head>
    <body>
    
        <a id="banner" href=<?php echo "http://" . $current_url; ?>><img src="banner.png"/></a>

        <div class="nav-tabs"> 
            <a class="nav-item active" href="javascript:;" id="links_tab">Links</a>
            <a class="nav-item" href="javascript:;" id="text_tab">Text</a>
            <a class="nav-item" href="javascript:;" id="code_tab">Code</a>
            <a class="nav-item" href="javascript:;" id="files_tab">Files</a>
            <a class="nav-item" href="javascript:;" id="logs_tab">Logs</a>
        </div>

        <div class="nav-panes">
            <div class="nav-tab active" id="links_pane">
                <form action="<?php echo $current_url ?>/" method="POST">
                    <div class="divide">
                        <div style="width: 20%">
                            Title:
                            <input placeholder="Optional" id="links_title" name="links_title"> 
                        </div>
                        <div style="width: 80%">
                            Alias URL:
                            <input placeholder="a1b2c3" id="links_alias" name="links_alias"> 
                        </div>
                    </div>
                    Link:
                    <input placeholder="http://website.url" id="links_content" name="links_content">
                    <div class="buttonGroup">
                        <button class="blue" id="links_rand" onclick="generateAlias(this);" type="button"><i class="fa fa-refresh"></i> Random</button>
                        <button class="green" name="action_links" type="submit"><i class="fa fa-upload"></i> Shorten</button>
                    </div>
                </form>
            </div>

            <div class="nav-tab" id="text_pane">
                <form action="<?php echo $current_url ?>/" method="POST">
                    <div class="divide">
                        <div style="width: 20%">
                            Title:
                            <input placeholder="Optional" id="text_title" name="text_title"> 
                        </div>
                        <div style="width: 80%">
                            Alias URL:
                            <input placeholder="a1b2c3" id="text_alias" name="text_alias"> 
                        </div>
                    </div>
                    Text:
                    <textarea rows="20" id="text_content" name="text_content"></textarea>
                    <div class="buttonGroup">
                        <button class="blue" id="text_rand" onclick="generateAlias(this);" type="button"><i class="fa fa-refresh"></i> Random</button>
                        <button class="green" name="action_text" type="submit"><i class="fa fa-upload"></i> Upload</button>
                    </div>
                </form>
            </div>

            <div class="nav-tab" id="code_pane">
                <form action="<?php echo $current_url ?>/" method="POST">
                    <div class="divide">
                        <div style="width: 20%">
                            Title:
                            <input placeholder="Optional" id="code_title" name="code_title"> 
                        </div>
                        <div style="width: 80%">
                            Alias URL:
                            <input placeholder="a1b2c3" id="code_alias" name="code_alias"> 
                        </div>
                    </div>
                    Code:
                    <div class="code_container">
                        <textarea id="code_content" name="code_content"></textarea>
                    </div>
                    <div class="divide">
                        <div style="width: 20%">
                            Syntax:
                            <select id="syntax-selection">
                                <option>C</option>
                                <option>C++</option>
                                <option>CS</option>
                                <option>CSS</option>
                                <option>HTML</option>
                                <option>Java</option>
                                <option>Javascript</option>
                                <option>JSON</option>
                                <option>Markdown</option>
                                <option>PHP</option>
                                <option>Powershell</option>
                                <option>Python</option>
                                <option>Shell</option>
                                <option>MySQL</option>
                                <option>Vb.NET</option>
                            </select>
                        </div>
                        <div style="width: 100%">
                          <div class="buttonGroup">
                            <button class="blue" id="code_rand" onclick="generateAlias(this);" type="button"><i class="fa fa-refresh"></i> Random</button>
                            <button class="green" name="action_code" type="submit"><i class="fa fa-upload"></i> Upload</button>
                          </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="nav-tab" id="files_pane">
                <form action="<?php echo $current_url ?>/" method="POST" enctype="multipart/form-data">
                    <div class="divide">
                        <div style="width: 20%">
                            Title:
                            <input placeholder="Optional" id="files_title" name="files_title"> 
                        </div>
                        <div style="width: 80%">
                            Alias URL:
                            <input placeholder="a1b2c3" id="files_alias" name="files_alias"> 
                        </div>
                    </div>
                    File:
                    <input placeholder="Choose File..." id="files_content" name="files_content" type="file">
                    <div class="buttonGroup">
                        <button class="blue" id="files_rand" onclick="generateAlias(this);" type="button"><i class="fa fa-refresh"></i> Random</button>
                        <button class="green" name="action_file" type="submit"><i class="fa fa-upload"></i> Upload</button>
                    </div>
                </form>
            </div>

            <div class="nav-tab" id="logs_pane">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Alias</th>
                            <th>Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $databaseQuery = $database->query("SELECT type, alias, title FROM ribbons");
                            while ($row = $databaseQuery->fetch_row()) {
                                echo "<tr>";
                                for($i = 0; $i < ($databaseQuery->field_count); $i++){
                                    if ($i == 1) {
                                        echo "<td><a href='" . $current_url . "/t/" . $row[$i] . "'>$row[$i]</a></td>";
                                    } else {
                                        echo "<td>$row[$i]</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="output_box">
            <?php echo $output; ?>
        </div>

        <div id="footer">
            <p>A <a href="http://tommyguo.dev">Tommy Guo</a> project</p>
        </div>

        <script>
            $(document).ready(function() {
                var editor = CodeMirror.fromTextArea(document.getElementById("code_content"), {
                    lineNumbers: true,
                    autoRefresh:true,
                    matchBrackets: true,
                    styleActiveLine: true,
                    mode:  CodeMirror.findModeByName("vb")
                });

                
                $('#syntax-selection').on('change', function() {
                    console.log(CodeMirror.findModeByName(this.value));
                    editor.setOption("mode", CodeMirror.findModeByName(this.value).mode)
                });

                $(".nav-item").click(function(event) {
                    $(".nav-item").removeClass("active");
                    $(event.target).addClass("active");

                    var docName = $(event.target).attr('id').replace("_tab", "");
                    document.title = "Ribbon - " + docName.charAt(0).toUpperCase() + docName.slice(1);
                    $pane = $(event.target).attr('id').replace("_tab", "_pane")
                    $(".nav-tab").removeClass("active");
                    $("#" + $pane).addClass("active");
                });
            });
        </script>

        <script>
            function generateAlias(x) {
                document.getElementById(x.id.replace("rand", "alias")).value = rand();
            }

            function rand() {
                var result = '';
                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;
                for ( var i = 0; i < 6; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                return result;
            }
        </script>
    </body>
</html>