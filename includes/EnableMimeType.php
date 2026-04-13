<?php

namespace A3DMV;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class EnableMimeType {
    private $mime_types = [
        'gltf'  => 'model/gltf+json',
        'glb'  => 'model/gltf+binary'
    ];

    public function enable() {
        add_filter('upload_mimes', [$this, 'add_mime_types']);
        add_filter('wp_check_filetype_and_ext', [$this, 'check_filetype'], 10, 5);
    }

    public function add_mime_types($mime_types) {
        return array_merge($mime_types, $this->mime_types);
    }

    public function check_filetype($data, $file, $filename, $mimes, $real_mime = null) {
        if (!empty($data['ext']) && !empty($data['type'])) {
            // Verify the actual file MIME type matches the expected type
            if ($this->verify_mime_type($file, $data['type'])) {
                return $data;
            }
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (array_key_exists($ext, $this->mime_types)) {
            $expected_type = $this->mime_types[$ext];

            // Verify actual file content matches expected MIME type
            if ($this->verify_mime_type($file, $expected_type)) {
                $data['ext'] = $ext;
                $data['type'] = $expected_type;
            }
        }

        return $data;
    }

    /**
     * Verify the actual file MIME type matches the expected type.
     * Prevents extension-only MIME type bypass attacks.
     *
     * @param string $file Path to the file
     * @param string $expected_type Expected MIME type
     * @return bool True if file matches expected type
     */
    private function verify_mime_type($file, $expected_type) {
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }

        // Use finfo to detect actual MIME type
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $actual_type = finfo_file($finfo, $file);
            finfo_close($finfo);

            // Check if actual type matches expected type
            if ($actual_type === $expected_type) {
                return true;
            }

            // GLB files are binary, check for GLB magic bytes
            if ($expected_type === 'model/gltf+binary') {
                return $this->is_valid_glb($file);
            }

            // GLTF files are JSON, check if valid JSON
            if ($expected_type === 'model/gltf+json') {
                return $this->is_valid_gltf($file);
            }
        }

        return false;
    }

    /**
     * Validate GLB file by checking magic bytes.
     * GLB files start with "glTF" (0x676C5446) followed by version
     *
     * @param string $file Path to the file
     * @return bool True if valid GLB
     */
    private function is_valid_glb($file) {
        $handle = fopen($file, 'rb');
        if (!$handle) {
            return false;
        }

        // Read first 4 bytes (should be "glTF")
        $magic = fread($handle, 4);
        fclose($handle);

        return $magic === "glTF";
    }

    /**
     * Validate GLTF file by checking if it's valid JSON with asset object.
     *
     * @param string $file Path to the file
     * @return bool True if valid GLTF
     */
    private function is_valid_gltf($file) {
        $content = file_get_contents($file);
        if ($content === false) {
            return false;
        }

        // Check if valid JSON and contains 'asset' key (required for glTF)
        $json = json_decode($content, true);
        return json_last_error() === JSON_ERROR_NONE && isset($json['asset']);
    }
}