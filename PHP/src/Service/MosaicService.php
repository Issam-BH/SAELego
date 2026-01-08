<?php
class MosaicService {
    private $binPath;
    private $bricksPath;

    public function __construct() {
        $this->binPath = __DIR__ . '/../../bin/pavage'; 
        $this->bricksPath = __DIR__ . '/../../bin/briques.txt';
    }

    public function generateMosaic($sourceImagePath) {
        $tmpDir = sys_get_temp_dir() . '/lego_' . uniqid();
        if (!mkdir($tmpDir)) {
            throw new Exception("Impossible de créer le dossier temporaire");
        }

        try {
            copy($this->bricksPath, $tmpDir . '/briques.txt');
            $this->convertImageToTxt($sourceImagePath, $tmpDir . '/image.txt');

            $command = escapeshellcmd($this->binPath) . " " . escapeshellarg($tmpDir);
            shell_exec($command);

            $outputs = [
                'optimized' => ['file' => 'outAnyShape.txt', 'label' => 'Formes Optimisées'],
                'standard'  => ['file' => 'outV3.txt',       'label' => 'Standard (1x1, 2x2, 4x2)'],
                'cheap'     => ['file' => 'outCheap.txt',    'label' => 'Économique'],
                'reference' => ['file' => 'out11.txt',       'label' => 'Pixel Art (1x1 seulement)']
            ];

            $results = [];

            foreach ($outputs as $key => $config) {
                $filePath = $tmpDir . '/' . $config['file'];
                if (file_exists($filePath)) {
                    $parsed = $this->parseResultFile($filePath);
                    if ($parsed) {
                        $results[$key] = [
                            'label'  => $config['label'],
                            'data'   => $parsed['bricks'],
                            'cost'   => $parsed['cost'],
                            'count'  => $parsed['count'],
                            'error'  => $parsed['error']
                        ];
                    }
                }
            }

            return $results;

        } finally {
        }
    }

    private function convertImageToTxt($imgParams, $destPath) {
        $size = 64; 
        $ext = strtolower(pathinfo($imgParams, PATHINFO_EXTENSION));
        if ($ext === 'jpg' || $ext === 'jpeg') $img = imagecreatefromjpeg($imgParams);
        elseif ($ext === 'png') $img = imagecreatefrompng($imgParams);
        else throw new Exception("Format non supporté");
        
        $smallImg = imagecreatetruecolor($size, $size);
        imagecopyresampled($smallImg, $img, 0, 0, 0, 0, $size, $size, imagesx($img), imagesy($img));
        
        $content = "$size $size\n";
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $rgb = imagecolorat($smallImg, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $content .= sprintf("%02x%02x%02x ", $r, $g, $b);
            }
            $content .= "\n";
        }
        
        file_put_contents($destPath, $content);
        imagedestroy($img);
        imagedestroy($smallImg);
    }

    private function parseResultFile($path) {
        $lines = file($path);
        if (empty($lines)) return null;

        $header = array_shift($lines); 
        $meta = sscanf($header, "%d %d %d %d");
        
        $count = $meta[0] ?? 0;
        $cost = $meta[1] ?? 0;
        $error = $meta[2] ?? 0;

        $bricks = [];
        foreach ($lines as $line) {
            if (preg_match('/(\d+)x(\d+)(?:-[^ \/]+)?\/([0-9A-Fa-f]{6}) (\d+) (\d+) (\d+)/', $line, $matches)) {
                $bricks[] = [
                    'w' => (int)$matches[1],
                    'h' => (int)$matches[2],
                    'color' => '#' . $matches[3],
                    'x' => (int)$matches[4],
                    'y' => (int)$matches[5],
                    'rot' => (int)$matches[6]
                ];
            }
        }
        
        return [
            'bricks' => $bricks,
            'count'  => $count,
            'cost'   => $cost,
            'error'  => $error
        ];
    }
}
?>