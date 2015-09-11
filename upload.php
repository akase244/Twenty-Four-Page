<?php
if ( !empty($_FILES)) {
    $existsHtmlFlag = false;
    /*
    echo '<pre>';
    var_dump($_FILES);
    var_dump($_POST);
    echo '</pre>';
     * 
     */
    $md5Hash = '';

    foreach ($_FILES['upfile']['name'] as $key => $file) {

        $pathInfo = pathinfo($file);
        if ($pathInfo['extension'] == 'zip') {

            $md5Hash = md5_file($_FILES['upfile']['tmp_name'][$key]);
            $extractDir = '/vagrant_data/files/'.$md5Hash;
            $zip = new ZipArchive();
            if( $zip->open($_FILES['upfile']['tmp_name'][$key]) === true ){
                $zip->extractTo($extractDir);
                $zip->close();

                //  htmlファイルが含まれているかチェック
                $htmlDir = '';
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extractDir));
                foreach ($iterator as $fileInfo) {
                    if ($fileInfo->isFile()) {
                        $pathInfo = pathinfo($fileInfo->getPathname());
                        if ($pathInfo['extension'] == 'html') {
                            $htmlDir = $pathInfo['dirname'];
                            exec('mv -f '.$htmlDir.'/* '.$extractDir);
                        }
                    }
                }
                if ($htmlDir) {
                    echo '<pre>';
                    echo 'アクセス用URLを'. $_POST['mail'].'に送信しました。';
                    //var_dump($_POST['mail']);
                    echo '<br><br><br><br>';
                    echo '<a href="http://192.168.33.11/index.php?key=' . $md5Hash . '">http://192.168.33.11/index.php?key=' . $md5Hash . '</a>';
                    echo '</pre>';

                    mail($_POST['mail'], 'アクセス用URL', 'http://192.168.33.11/index.php?key=' . $md5Hash);

                } else {
                    echo '<div styel="font-size:xx-large;">HTMLファイルをアップロードしてください。</div>';
                }
            }
        }
    }
}