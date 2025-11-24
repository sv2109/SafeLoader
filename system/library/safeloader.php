<?php

/**
 * SafeLoader — models safe loading with logging and ionCube error handling
 * 
 * @author  sv2109
 * @link    sv2109@gmail.com
*/

class SafeLoader {
    private $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function model($route) {
        
        try {

            if (!extension_loaded('ionCube Loader')) {
                 $this->handleError("ionCube Loader is not installed. Model {$route} not loaded.");
                return null;
            }

            $this->registry->get('load')->model($route);
            return $this->registry->get('model_' . str_replace('/', '_', (string)$route));

        } catch (SafeLoaderException $e) {
            $this->handleError("[{$e->getModule()}] " . $e->getMessage());
            return null;

        } catch (Throwable $e) {
            $this->handleError("Error loading model {$route}: " . $e->getMessage());
            return null;
        }
    }

    private function handleError($message) {
        $this->log($message);

        if (isset($_GET['debug_safeloader'])) {
            echo "<div style='background:#ffe0e0;border:1px solid #c00;padding:10px;margin:10px;'>";
            echo "<b>SafeLoader Debug:</b> " . nl2br(htmlspecialchars($message));
            echo "</div>";
        }
    }

    private function log($message) {
        if ($this->registry->has('log')) {
            $this->registry->get('log')->write('[SafeLoader] ' . $message);
        }
    }
}
