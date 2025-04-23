<?php

namespace AiContextBundle\Service;
class ChecksumService
{
    private string $checksumFile;

    public function __construct(string $checksumFile)
    {
        $this->checksumFile = $checksumFile;
    }

    /**
     * Calculate the checksum of the given paths.
     * @param array $paths
     * @return string
     */
    public function calculateChecksum(array $paths): string
    {
        $data = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                    $data[] = $file->getRealPath() . ':' . $file->getMTime();
                }
            }
        }

        return hash('sha256', implode('|', $data));
    }

    /**
     * Check if the checksum has changed since the last save.
     * @param array $paths
     * @return bool
     */
    public function hasChanged(array $paths): bool
    {
        $current = $this->calculateChecksum($paths);
        $last = $this->getLastChecksum();

        return $current !== $last;
    }

    /**
     * Save the current checksum to a file.
     * @param array $paths
     * @return void
     */
    public function saveChecksum(array $paths): void
    {
        $current = $this->calculateChecksum($paths);
        @mkdir(dirname($this->checksumFile), recursive: true);
        file_put_contents($this->checksumFile, $current);
    }

    /**
     * Get the last saved checksum from the file.
     * @return string|null
     */
    private function getLastChecksum(): ?string
    {
        return file_exists($this->checksumFile)
            ? file_get_contents($this->checksumFile)
            : null;
    }
}
