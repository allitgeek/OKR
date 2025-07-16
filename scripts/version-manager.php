<?php

/**
 * OKR Management System - Version Manager
 * 
 * This script helps manage version updates, git tags, and changelog updates
 * Usage: php scripts/version-manager.php [action] [version]
 * 
 * Actions:
 * - current: Show current version
 * - bump [major|minor|patch]: Bump version number
 * - tag: Create git tag for current version
 * - backup: Create versioned backup
 */

class VersionManager
{
    private $versionFile = 'VERSION';
    private $changelogFile = 'CHANGELOG.md';
    private $readmeFile = 'README.md';
    
    public function getCurrentVersion()
    {
        if (file_exists($this->versionFile)) {
            return trim(file_get_contents($this->versionFile));
        }
        return '1.0.0'; // Default version
    }
    
    public function setVersion($version)
    {
        file_put_contents($this->versionFile, $version . PHP_EOL);
        echo "✅ Version updated to: $version\n";
    }
    
    public function bumpVersion($type = 'patch')
    {
        $current = $this->getCurrentVersion();
        list($major, $minor, $patch) = explode('.', $current);
        
        switch ($type) {
            case 'major':
                $major++;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patch':
            default:
                $patch++;
                break;
        }
        
        $newVersion = "$major.$minor.$patch";
        $this->setVersion($newVersion);
        
        return $newVersion;
    }
    
    public function createGitTag($version = null)
    {
        $version = $version ?: $this->getCurrentVersion();
        
        // Create annotated git tag
        $command = "git tag -a v$version -m 'Release version $version'";
        $output = shell_exec($command);
        
        echo "🏷️  Git tag v$version created\n";
        echo "💡 Push tag with: git push origin v$version\n";
        
        return $version;
    }
    
    public function createVersionedBackup($version = null)
    {
        $version = $version ?: $this->getCurrentVersion();
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "OKR_v{$version}_backup_{$timestamp}.tar.gz";
        
        // Create compressed backup excluding unnecessary files
        $excludes = [
            '--exclude=node_modules',
            '--exclude=vendor',
            '--exclude=storage/logs/*',
            '--exclude=storage/framework/cache/*',
            '--exclude=storage/framework/sessions/*',
            '--exclude=storage/framework/views/*',
            '--exclude=.git',
            '--exclude=*.tar.gz'
        ];
        
        $command = "tar -czf $filename " . implode(' ', $excludes) . " .";
        shell_exec($command);
        
        if (file_exists($filename)) {
            $size = $this->formatBytes(filesize($filename));
            echo "📦 Backup created: $filename ($size)\n";
        } else {
            echo "❌ Failed to create backup\n";
        }
        
        return $filename;
    }
    
    public function updateChangelog($version, $changes = [])
    {
        $date = date('Y-m-d');
        $newEntry = "\n## [$version] - $date\n\n";
        
        if (empty($changes)) {
            $newEntry .= "### Added\n- New features and improvements\n\n";
            $newEntry .= "### Fixed\n- Bug fixes and optimizations\n\n";
        } else {
            foreach ($changes as $category => $items) {
                $newEntry .= "### $category\n";
                foreach ($items as $item) {
                    $newEntry .= "- $item\n";
                }
                $newEntry .= "\n";
            }
        }
        
        // Read current changelog
        $changelog = file_exists($this->changelogFile) ? file_get_contents($this->changelogFile) : '';
        
        // Insert new entry after the header
        $headerEnd = strpos($changelog, "\n## ");
        if ($headerEnd === false) {
            $headerEnd = strpos($changelog, "\n# ");
            $headerEnd = $headerEnd ? $headerEnd + strlen("\n# Changelog\n") : 0;
        }
        
        $newChangelog = substr($changelog, 0, $headerEnd) . $newEntry . substr($changelog, $headerEnd);
        file_put_contents($this->changelogFile, $newChangelog);
        
        echo "📝 Changelog updated with version $version\n";
    }
    
    public function showVersionInfo()
    {
        $version = $this->getCurrentVersion();
        $gitHash = trim(shell_exec('git rev-parse --short HEAD'));
        $gitBranch = trim(shell_exec('git rev-parse --abbrev-ref HEAD'));
        $lastCommit = trim(shell_exec('git log -1 --format="%cd" --date=short'));
        
        echo "🔢 Current Version: $version\n";
        echo "🌿 Git Branch: $gitBranch\n";
        echo "📍 Git Hash: $gitHash\n";
        echo "📅 Last Commit: $lastCommit\n";
        
        // Check if there are uncommitted changes
        $status = shell_exec('git status --porcelain');
        if (!empty(trim($status))) {
            echo "⚠️  Uncommitted changes detected\n";
        } else {
            echo "✅ Working directory clean\n";
        }
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    $manager = new VersionManager();
    $action = $argv[1] ?? 'current';
    $param = $argv[2] ?? null;
    
    echo "🎯 OKR Management System - Version Manager\n";
    echo "==========================================\n\n";
    
    switch ($action) {
        case 'current':
        case 'info':
            $manager->showVersionInfo();
            break;
            
        case 'bump':
            $type = $param ?: 'patch';
            if (!in_array($type, ['major', 'minor', 'patch'])) {
                echo "❌ Invalid bump type. Use: major, minor, or patch\n";
                exit(1);
            }
            $newVersion = $manager->bumpVersion($type);
            echo "🚀 Version bumped to: $newVersion\n";
            echo "💡 Don't forget to update CHANGELOG.md and commit changes\n";
            break;
            
        case 'tag':
            $version = $param ?: $manager->getCurrentVersion();
            $manager->createGitTag($version);
            break;
            
        case 'backup':
            $version = $param ?: $manager->getCurrentVersion();
            $manager->createVersionedBackup($version);
            break;
            
        case 'release':
            // Full release process
            $type = $param ?: 'patch';
            echo "🚀 Starting release process...\n\n";
            
            $newVersion = $manager->bumpVersion($type);
            $manager->createGitTag($newVersion);
            $manager->createVersionedBackup($newVersion);
            
            echo "\n✅ Release process completed for version $newVersion\n";
            echo "💡 Next steps:\n";
            echo "   1. Update CHANGELOG.md with release notes\n";
            echo "   2. Commit version changes: git commit -am 'Release version $newVersion'\n";
            echo "   3. Push changes: git push origin main\n";
            echo "   4. Push tag: git push origin v$newVersion\n";
            break;
            
        default:
            echo "❌ Unknown action: $action\n\n";
            echo "Available actions:\n";
            echo "  current    - Show current version info\n";
            echo "  bump [type] - Bump version (major|minor|patch)\n";
            echo "  tag [version] - Create git tag\n";
            echo "  backup [version] - Create versioned backup\n";
            echo "  release [type] - Full release process\n";
            exit(1);
    }
} 