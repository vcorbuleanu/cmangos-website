<?php
    class loader {
        public function view($file, $data = null) {
            if (is_array($data))
                extract($data);

            ob_start();
            if (file_exists('./application/views/' . $file . '.php'))
                require('./application/views/' . $file . '.php');
            $content = ob_get_clean();
            echo $content;
        }

        public function model($file, $name = null) {
            $file .= '_model';
            if ($name) {
                if (!method_exists(controller::instance(), $name))
                    controller::instance()->$name = new $file();
            } else {
                if (!method_exists(controller::instance(), 'model'))
                    controller::instance()->model = new $file();
            }
        }
    }
?>