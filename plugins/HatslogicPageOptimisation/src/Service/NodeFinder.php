<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Service;

class NodeFinder {
    private $isWindows;
    private $commonPaths;

    public function __construct() {
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $this->commonPaths = [
            '/opt/plesk/node/latest/bin/node',
            '/opt/plesk/node/*/bin/node',
            '/usr/local/bin/node',
            '/usr/bin/node',
            '/opt/node/bin/node',
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
            '/home/site/wwwroot/node/node',
        ];
    }

    public function findNodeAndNpm() {
        $result = [
            'node' => $this->findBinary('node'),
            'npm' => $this->findBinary('npm'),
        ];

        return $result;
    }

    private function findBinary($binaryName) {
        $result = [
            'found' => false,
            'path' => null,
            'version' => null,
            'error' => null
        ];

        try {
            // 1. Check for NVM paths
            $binaryPath = $this->findInNvm($binaryName);
            if ($binaryPath) {
                return $this->validateAndReturn($binaryPath, $binaryName);
            }

            // 2. Check using system commands
            $binaryPath = $this->findUsingCommands($binaryName);
            if ($binaryPath) {
                return $this->validateAndReturn($binaryPath, $binaryName);
            }

            // 3. Check common paths
            $binaryPath = $this->findInCommonPaths($binaryName);
            if ($binaryPath) {
                return $this->validateAndReturn($binaryPath, $binaryName);
            }

            // 4. Platform-specific checks
            $binaryPath = $this->findUsingPlatformSpecific($binaryName);
            if ($binaryPath) {
                return $this->validateAndReturn($binaryPath, $binaryName);
            }

            $result['error'] = ucfirst($binaryName) . ' installation not found';
            return $result;

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function findInNvm($binaryName) {
        $nvmDir = getenv('NVM_DIR') ?: getenv('HOME') . '/.nvm';
        if (!$nvmDir || !is_dir($nvmDir)) {
            return null;
        }

        $versionsDir = $nvmDir . '/versions/node';
        if (!is_dir($versionsDir)) {
            return null;
        }

        // Find the latest version installed
        $versions = glob($versionsDir . '/*', GLOB_ONLYDIR);
        if (empty($versions)) {
            return null;
        }

        usort($versions, 'version_compare');
        $latestVersionPath = end($versions);

        $binaryPath = $latestVersionPath . '/bin/' . $binaryName;
        if ($this->isExecutable($binaryPath)) {
            return $binaryPath;
        }

        return null;
    }

    private function findUsingCommands($binaryName) {
        $commands = $this->isWindows 
            ? ["where $binaryName", "where $binaryName.exe"] 
            : ["command -v $binaryName", "which $binaryName", "type -p $binaryName"];

        foreach ($commands as $command) {
            $output = $this->safeExecute($command);
            if ($output) {
                return trim($output);
            }
        }
        return null;
    }

    private function findInCommonPaths($binaryName) {
        foreach ($this->commonPaths as $path) {
            $binaryPath = str_replace('node', $binaryName, $path);

            if (strpos($binaryPath, '*') !== false) {
                $matches = glob($binaryPath);
                if ($matches) {
                    foreach ($matches as $match) {
                        if ($this->isExecutable($match)) {
                            return $match;
                        }
                    }
                }
            } elseif ($this->isExecutable($binaryPath)) {
                return $binaryPath;
            }
        }
        return null;
    }

    private function findUsingPlatformSpecific($binaryName) {
        if (!$this->isWindows) {
            $output = $this->safeExecute("source /etc/profile 2>/dev/null && which $binaryName");
            if ($output) {
                return trim($output);
            }
        }
        return null;
    }

    private function validateAndReturn($binaryPath, $binaryName) {
        $result = [
            'found' => true,
            'path' => $binaryPath,
            'version' => null,
            'error' => null
        ];

        $versionCommand = escapeshellarg($binaryPath) . ' --version';
        $version = $this->safeExecute($versionCommand);
        if ($version) {
            $result['version'] = trim($version);
        }

        return $result;
    }

    private function isExecutable($path) {
        return file_exists($path) && ($this->isWindows || is_executable($path));
    }

    private function safeExecute($command) {
        if (!function_exists('shell_exec') || ini_get('safe_mode')) {
            throw new \Exception('shell_exec is disabled');
        }

        $output = shell_exec($command . ' 2>&1');
        return $output !== null ? $output : null;
    }
}
