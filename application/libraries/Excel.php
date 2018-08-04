<?php
    class Excel
    {        
        
        public function generateAndDownload($file_name, $results = array()){
            $fileName = $file_name.'.csv';
            //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            //header('Content-Description: File Transfer');
            header("Content-type: text/csv;charset=utf-8");
            header("Content-Disposition: attachment; filename={$fileName}");
            header("Expires: 0");
            header("Pragma: no-cache");
            $fh = @fopen( 'php://output', 'w' );
            $headerDisplayed = false;
            foreach ( $results as $data ) {
                // Add a header row if it hasn't been added yet
                if ( !$headerDisplayed ) {
                    // Use the keys from $data as the titles
                    fputcsv($fh, $data);
                    $headerDisplayed = false;
                }
                // Put the data into the stream
                //fputcsv($fh, $data);
            }
            // Close the file
            fclose($fh);
            // Make sure nothing else is sent, our file is done
            exit;
        }
    }
?>